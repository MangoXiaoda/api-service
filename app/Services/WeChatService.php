<?php
/*
 * @Description: 微信服务类
 * @Version:
 * @Author: LiZhongDa
 * @Date: 2022-04-06 09:38:33
 */

namespace App\Services;

use App\Enums\Cache\KeyPrefix;
use App\Handlers\WechatConfigHandler;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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

}
