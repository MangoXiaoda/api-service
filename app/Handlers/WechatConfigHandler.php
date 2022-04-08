<?php
/*
 * @Description: 微信 easywechat 配置
 * @Author: lizhongda
 * @Date: 2022/3/15 上午10:42
 */

namespace App\Handlers;

use EasyWeChat\Factory;

class WechatConfigHandler
{
    /**
     * [1-1]微信公众平台设置
     * @return array
     */
    public function official_config()
    {
        $config = [
            // 必要配置, 这些都是之前在 .env 里配置好的
            'app_id' => config('wechat.official_account.default.app_id'),
            'secret' => config('wechat.official_account.default.secret')
        ];

        return $config;
    }

    /**
     * [1-2]微信公众号相关
     * @return \EasyWeChat\OfficialAccount\Application
     */
    public function officialAccount()
    {
        $officialAccount = Factory::officialAccount($this->official_config());

        return $officialAccount;
    }

    /**
     * [2-1]微信支付设置
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    public function pay_config()
    {
//        $config = config('wechat.payment.defaul');

        $config = [
            // 必要配置, 这些都是之前在 .env 里配置好的
            'app_id' => config('wechat.payment.default.app_id'),
            'mch_id' => config('wechat.payment.default.mch_id'),
            'key'    => config('wechat.payment.default.key'),   // API 密钥

            // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
            'cert_path'          => config('wechat.payment.default.cert_path'), // XXX: 绝对路径！！！！
            'key_path'           => config('wechat.payment.default.key_path'),      // XXX: 绝对路径！！！！

            'notify_url' => config('wechat.payment.default.notify_url'),   // 通知地址
        ];

        return $config;
    }

    /**
     * [2-2]生成微信支付相关
     * @return \EasyWeChat\Payment\Application
     */
    public function pay()
    {
        $pay = Factory::payment($this->pay_config());

        return $pay;
    }

    /**
     * [3-1]微信小程序设置
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    public function mini_config()
    {
//        $config = config('wechat.mini_program.default');
        // TODO 有时间看一下 上面的方法为什么无效，会报 Request access_token fail 41002 错误（已解决）
        // 报错原因为 数据库字段与 wechat 小程序配置文件字段名不一致导致，数据库叫 appid  config/wechat文件 mini_program 内叫 app_id
        // 解决方案暂时修改 mini_program 内的字段名 与数据库保持一致
        $config = [
            // 必要配置, 这些都是之前在 .env 里配置好的
            'app_id' => config('wechat.mini_program.default.app_id'),
            'secret' => config('wechat.mini_program.default.secret'),

            'response_type' => 'array',
        ];

        return $config;
    }

    /**
     * [3-2]微信小程序相关
     * @return \EasyWeChat\MiniProgram\Application
     */
    public function miniProgram()
    {
        $miniProgram = Factory::miniProgram($this->mini_config());

        return $miniProgram;
    }

    /**
     * [4-1]微信开放平台设置
     * @return array
     */
    public function platform_config()
    {
        $config = [
            // 必要配置, 这些都是之前在 .env 里配置好的
            'app_id' => config('wechat.open_platform.default.app_id'),
            'secret' => config('wechat.open_platform.default.secret')
        ];

        return $config;
    }

    /**
     * [4-2]微信开放平台相关
     * @return \EasyWeChat\OpenPlatform\Application
     */
    public function openPlatform()
    {
        $openPlatform = Factory::openPlatform($this->platform_config());

        return $openPlatform;
    }


}

