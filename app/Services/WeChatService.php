<?php
/*
 * @Description: 微信服务类
 * @Version:
 * @Author: LiZhongDa
 * @Date: 2022-04-06 09:38:33
 */

namespace App\Services;

use App\Enums\Cache\KeyPrefix;
use App\Enums\Order\VerifyCode;
use App\Handlers\WechatConfigHandler;
use App\Models\Goods;
use App\Models\OrderGoods;
use App\Models\OrderLog;
use App\Models\OrderSub;
use App\Models\WxPay;
use Doctrine\DBAL\Query\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function EasyWeChat\Kernel\Support\generate_sign;

class WeChatService extends Service
{

    /**
     * 微信内容检测地址
     * @var string
     */
    public static $WxCheckApiUrl = 'https://api.weixin.qq.com/wxa/';

    /**
     *
     * @param $sub_url
     * @return string
     */
    public static function getWxApiUrl($sub_url)
    {
        return self::$WxCheckApiUrl . $sub_url;
    }

    /**
     * 更新小程序 token
     * @return mixed|string
     * @throws \EasyWeChat\Kernel\Exceptions\HttpException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function updateWeixinMiniProgramToken()
    {
        // 实例化微信小程序类
        $miniProgram = (new WechatConfigHandler())->miniProgram();
        $accessToken = $miniProgram->access_token;
        $tk_arr = $accessToken->getToken(true); // token 数组  token['access_token'] 字符串

        if (!isset($tk_arr['access_token'])) {
            Log::error(LjJencode($tk_arr));
            return '';
        }

        $token = $tk_arr['access_token'];
        // 小程序 token 存入 redis里
        $key = KeyPrefix::MINI_PROGRAM.'TOKEN';
        Cache::forever($key, $token);

        return $token;
    }

    /**
     * 获取微信小程序token
     * @return mixed
     */
    public function getWeixinMiniProgramToken()
    {;
        $key = KeyPrefix::MINI_PROGRAM.'TOKEN';
        $token = Cache::get($key, function () {
            try {
                $token = $this->updateWeixinMiniProgramToken();

                return $token;
            } catch (\ErrorException $e) {
                return '';
            }
        });

        return $token;
    }

    /**
     * 微信内容检测
     * @param $data
     * @return array|false|string
     */
    public function wxContentCheck($data)
    {
        $c_type  = $data['c_type'] ?? 0;   // 1 文本检测 2 图片检测 3 音频检测
        $content = $data['content'] ?? ''; // 检测内容
        $scene   = $data['scene'] ?? 1;    // 场景枚举值（1 资料；2 评论；3 论坛；4 社交日志）

        if (!$content || !$c_type)
            return r_result(200);

        $url = '';
        $params = [
            'openid'  => auth()->user()->weapp_openid ?? 0,
            'scene'   => $scene,
            'version' => 2,
        ];
        // 文本检测
        if ($c_type == 1) {
            $params['content'] = $content;
            $url = self::getWxApiUrl('msg_sec_check');
        }

        // 图片、音频检测
        if (in_array($c_type, [2, 3])) {
            $r_url = fileUrlToWebUrl($content);
            $params['media_url']  = $r_url;
            $params['media_type'] = $c_type == 2 ? 2 : 1; // 1:音频;2:图片
            $url = self::getWxApiUrl('media_check_async');
        }

        if (!$url)
            return r_result(200);

        $params = LjJencode($params);
        $res = CurlRequest($url . '?access_token=' . $this->getWeixinMiniProgramToken(), $params, 'POST');
        $res = LjJdecode($res);
        $errcode = $res['errcode'] ?? null;

        // 容错机制，防止token失效，导致检测失败
        if (in_array($errcode, [40001])) {
            $res = CurlRequest($url . '?access_token=' . $this->updateWeixinMiniProgramToken(), $content, 'POST');
            $res = LjJdecode($res);
        }

        $suggest = $res['result']['suggest'] ?? '';
        $label   = $res['result']['label'] ?? 0;

        if ($errcode == 0 && ($suggest != 'pass'))
            return r_result($label, '内容违规违法,请删除后重试');

        return r_result(200);
    }

    /**
     * 支付
     * @param $data
     * @return array|false|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pay($data)
    {
        $gs_id     = $data['gs_id'] ?? 0;   // 商品id
        $buy_num   = $data['buy_num'] ?? 0; // 购买数量
        $user_info = $data['user_info'];    // 用户信息
        $sub_sn    = self::generateSubSn(); // 子订单号

        if (!$sub_sn)
            return r_result(201, '系统错误，请重试');

        if (!$user_info)
            return r_result(202, '请先登录');

        if (!$gs_id)
            return r_result(202, '缺少商品id');

        $gs_info = GoodsService::queryOneGoods($gs_id);
        if (!$gs_info)
            return r_result(203, '购买的商品不存在');

        $price = $gs_info['price'];

        if (!$price)
            return r_result(204, '商品价格信息错误');

        // 购买数量检测
        if (!$buy_num || !is_numeric($buy_num))
            return r_result(205, '请选择正确的购买数量');

        if ($gs_info['sales'] >= $gs_info['total'])
            return r_result(206, '商品数量不足,无法购买');

        $user_id = $user_info['id'];
        $open_id = $user_info['weapp_openid'] ?? ''; // 用户open_id
        if (!$open_id)
            return r_result(207, '系统错误，请重试');

        // 写入操作日志
        $log = [
            'sub_sn'        => $sub_sn,
            'action'        => VerifyCode::USER_PAY_CREATE,  // 用户创建支付
            'operator_id'   => $user_id,                     // 用户id
            'operator_name' => $auth_user['nickname'] ?? '', // 用户姓名
            'operator_type' => 3,                            // 操作者类型:1系统,2后台管理员3买家
            'msg'           => '创建支付订单',
            'client_ip'     => request()->ip()
        ];
        OrderLog::query()->create($log);

        // 实例化支付类
        $payment = (new WechatConfigHandler())->pay();
        $result = $payment->order->unify([
            'body'         => '美香园超市',
            'out_trade_no' => $sub_sn,
            'open_id'      => $open_id,
            'trade_type'   => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'total_fee'    => $price * 100
        ]);

        // 如果成功生成统一下单的订单，那么进行二次签名
        if ($result['return_code'] === 'SUCCESS') {
            // 二次签名的参数必须与下面相同
            $params = [
                'appId'     => config('wechat.payment.default.app_id'),
                'timeStamp' => strval(time()),
                'nonceStr'  => $result['nonce_str'],
                'package'   => 'prepay_id=' . $result['prepay_id'],
                'signType'  => 'MD5'
            ];

            // 生成 paySign 签名
            $params['paySign'] = generate_sign($params, config('wechat.payment.default.key'));

            // 记录二次签名
            $sign_data = [
                'sub_sn'     => $sub_sn,
                'user_id'    => $user_id,
                'app_id'     => $params['appId'],
                'time_stamp' => $params['timeStamp'],
                'nonce_str'  => $params['nonceStr'],
                'package'    => $params['package'],
                'sign_type'  => $params['signType'],
                'pay_sign'   => $params['paySign']
            ];

            WxPay::query()->create($sign_data);

            try {
                DB::beginTransaction();

                // 写入子单信息表
                OrderSub::query()->create([
                    'sub_sn'       => $sub_sn,
                    'user_id'      => $user_id,
                    'goods_amount' => $price,
                    'paid_amount'  => $price,
                    'code'         => $this->generateVerifyCode(),
                ]);

                // 写入订单商品表
                OrderGoods::query()->create([
                    'sub_sn'      => $sub_sn,
                    'goods_id'    => $gs_id,
                    'goods_price' => $price,
                    'buy_num'     => $buy_num,
                    'real_price'  => $price,
                    'title'       => $gs_info['title'],
                    'thumb'       => delWebPrefixUrl($gs_info['thumb'])
                ]);

                // 累加商品已售数量
                Goods::query()->where('id', $gs_id)->increment('sales', $buy_num);

                DB::commit();
            } catch (QueryException $e) {

                DB::rollBack();
                Log::error('写入订单数据失败', ['message' => $e->getMessage()]);

            }

            return r_result(200, '创建订单成功');

        } else {

            return r_result(201, '创建订单失败');
        }
    }

    /**
     * 生成子单订单号
     * 子单：主单固定字符No+Ymd+rand尾号+rand(10000001,99999999)
     * 例如：LM 20210601 10000021
     * @return string
     */
    public static function generateSubSn()
    {
        // 检测生成的订单号是否重复，重复则重新生成
        while (true) {
            $sub_sn = 'No' . date('Ymd', time()) . rand(10000001, 99999999);
            $count = OrderSub::query()->where('sub_sn', $sub_sn)->count();
            if (!$count)
                break;
        }

        return $sub_sn;
    }

    /**
     * 生成订单核销码
     * @return string
     */
    public static function generateVerifyCode()
    {
        return date('Ymdh', time()) . rand(100000001,999999999);
    }

}
