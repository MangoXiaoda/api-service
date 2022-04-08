<?php
/**
 * 通知服务类（详细请查看 App\Services\Notice\NoticeConfig 文件）
 * User: lizhongda
 * Date: 2022/03/31
 * Time: 10:24
 */

namespace App\Services;

use App\Services\Notice\NoticeConfig;

class NoticeService extends Service
{
    use NoticeConfig;

    /**
     * NoticeService constructor.
     * @param string $ntp
     */
    public function __construct($ntp = '')
    {
        $this->setNowConfig($ntp);
    }

    /**
     * 检测并发放单个类型通知
     * @param array $p 相关参数
     * @return array|false|mixed|string 结果数组
     */
    public function checkSendOne($p = [])
    {
        $this->p = $p;

        if (!$this->nowConfig)
            return r_result(201, '不支持的通知类型');

        if ($this->checkSendClose())
            return r_result(202, '该通知已关闭');

        if ($this->checkIsSendMax())
            return r_result(203, '已到达最大发送数量');

        return $this->sendTypeNotice($this->ntp, $this->sendNum, $p);
    }

}
