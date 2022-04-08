<?php

namespace App\Services\Notice;

use App\Models\NoticeLog;
use App\Services\MessageService;

trait NoticeConfig
{

    /**
     * 通知发送方式配置数组
     * @var \string[][]
     */
    public static $sendFsArray = [
        1 => [
            'title' => '站内'
        ]
    ];

    /**
     * 一次最大处理消息个数
     * @var int
     */
    public static $onceMaxNum = 100;

    /**
     * 已经发送的消息个数
     * @var int
     */
    protected $sendNum = 0;

    /**
     * 当前的通知类型
     * @var string
     */
    protected $ntp = '';

    /**
     * 当前通知类型配置
     * @var
     */
    protected $nowConfig;

    /**
     * 当前指定的 通知信息
     * @var null
     */
    protected $ntInfor = null;

    /**
     * 相关参数
     * @var array
     */
    public $p = [];

    /**
     * 发送类型通知
     * @param $ntp
     * @param int $startSendTimes
     * @param array $p
     * @return mixed
     */
    public function sendTypeNotice($ntp, $startSendTimes = 0, $p = [])
    {
        $this->setSendNum($startSendTimes);
        $this->p = $p;
        $this->setNowConfig($ntp);
        $name = $this->getClassName();
        $nt = new $name();
        $nt->setNowConfig($ntp);
        $nt->setSendNum($startSendTimes);
        $nt->p = $p;
        $nt->setNtInfor();
        return $nt->send();
    }

    /**
     * 设置消息相关数据
     */
    public function setNtInfor()
    {
        if (!$this->p) {
            $this->ntInfor = null;
            return;
        }

        $nt_id = $this->p['nt_id'] ?? 0;
        if (!$nt_id) {
            $this->ntInfor = null;
            return;
        }

        $data = NoticeLog::query()->find($nt_id);
        if (!$data) {
            $this->ntInfor = null;
            return;
        }

        $this->ntInfor = $data;
    }

    /**
     * 系统通知配置数组
     * @return array[]
     */
    private static function getNoticeTypeSystem()
    {
        $arr = [
            's_1' => [
                'key'       => 's_1',                                  // 通知模板 key
                'title'     => '点赞通知',                              // 通知标题
                'tpl'       => '${user_name} 赞了你的作品',              // 通知内容
                'sendFs'    => 1,                                      // 通知发送方式（请查看 $sendFsArray）
                'close'     => 0,                                      // 是否关闭通知 0 否 1 是
                'className' => 'CheckSendNoticeLikes',                 // 具体通知代码类名（详细的通知逻辑会写在 services/notice目录下 该命名的文件内）
                'wxMsgId'   => 0                                       // 微信模板ID
            ],

            's_2' => [
                'key'       => 's_2',                                  // 通知模板 key
                'title'     => '评论通知',                              // 通知标题
                'tpl'       => '${user_name} 评论了你的作品',            // 通知内容
                'sendFs'    => 1,                                      // 通知发送方式（请查看 $sendFsArray）
                'close'     => 0,                                      // 是否关闭通知 0 否 1 是
                'className' => 'CheckSendNoticeComment',               // 具体通知代码类名（详细的通知逻辑会写在 services/notice目录下 该命名的文件内）
                'wxMsgId'   => 0                                       // 微信模板ID
            ],

            's_3' => [
                'key'       => 's_3',                                  // 通知模板 key
                'title'     => '关注通知',                              // 通知标题
                'tpl'       => '${user_name} 关注了你',                 // 通知内容
                'sendFs'    => 1,                                      // 通知发送方式（请查看 $sendFsArray）
                'close'     => 0,                                      // 是否关闭通知 0 否 1 是
                'className' => 'CheckSendNoticeFans',                  // 具体通知代码类名（详细的通知逻辑会写在 services/notice目录下 该命名的文件内）
                'wxMsgId'   => 0                                       // 微信模板ID
            ],

        ];

        return $arr;
    }

    /**
     * 获取类名称
     * @return string
     */
    private function getClassName()
    {
        $name = $this->nowConfig['className'] ?? '';

        return "App\\Services\\Notice\\$name";
    }

    /**
     * 设定 消息发送的个数
     * @param $num
     */
    public function setSendNum($num)
    {
        $this->sendNum = $num;
    }

    /**
     * 增加通知的发送个数
     */
    protected function addSendNum()
    {
        $this->sendNum++;
    }

    /**
     * 获取发送方式
     * @return mixed|string
     */
    protected function getSendFs()
    {
        return $this->nowConfig['sendFs'] ?? '';
    }

    /**
     * 增加发送的通知日志
     * @param $to_uid
     * @param $about_id
     * @param array $data
     * @return bool
     */
    protected function addNoticeLog($to_uid, $about_id, $data = [])
    {
        if ($this->checkNoticeHaveSend($to_uid, $about_id))
            return false;

        // 处理指定记录的信息
        if ($this->ntInfor) {
            $nt = NoticeLog::query()->find($this->ntInfor['id']);

            if ($nt) {
                $nt->about_id = $about_id;
                $nt->nt_status = 1;
                $nt->save();
                return true;
            }
        }

        $nt = new NoticeLog();
        $nt->nt_type = $this->ntp;
        $nt->to_uid = $to_uid;
        $nt->about_id = $about_id;
        $nt->nt_data = LjJencode($data);
        $nt->nt_status = 1;
        $nt->save();

        return true;
    }

    /**
     * 检测通知是否已经发送过
     * @param $to_uid
     * @param $about_id
     * @return bool
     */
    public function checkNoticeHaveSend($to_uid, $about_id)
    {
        if ($this->ntInfor) {
            $nt = NoticeLog::query()->find($this->ntInfor['id']);

            if ($nt && $nt->nt_status == 1)
                return true;

            return false;
        }

        $type = $this->ntp;
        $nt = NoticeLog::query()
            ->where('nt_type', $type)
            ->where(['to_uid' => $to_uid, 'about_id' => $about_id])
            ->where('nt_status', 1)
            ->count();

        if ($nt)
            return true;

        return false;
    }

    /**
     * 检测是否达到最大发送消息数量
     * @return bool
     */
    protected function checkIsSendMax()
    {
        if ($this->sendNum >= self::$onceMaxNum)
            return true;
        return false;
    }

    /**
     * 检测是否关闭了当前通知
     * @return bool
     */
    protected function checkSendClose()
    {
        if (!$this->nowConfig)
            return true;

        if ($this->nowConfig['close'] ?? 0)
            return true;

        return false;
    }

    /**
     * 设定当前的配置数据
     * @param $ntp
     * @return bool
     */
    public function setNowConfig($ntp)
    {
        $set = self::getTypeSetByKey($ntp);

        if (!$set)
            return false;

        $this->ntp = $ntp;
        $this->nowConfig = $set;

        return true;
    }

    /**
     * 根据关键字获取配置数组
     * @param $key
     * @return array|mixed
     */
    public static function getTypeSetByKey($key)
    {
        $arr = self::getNoticeTypeArray();

        foreach ($arr as $k => $v) {

            if (!$v['list'])
                continue;

            foreach ($v['list'] as $kk => $vv) {

                if ($kk == $key)
                    return $vv;

            }
        }

        return [];
    }

    /**
     * 获取通知类型列表
     * @return array[]
     */
    public static function getNoticeTypeArray()
    {
        return [
            'system' => [
                'title' => '系统通知',
                'list'  => self::getNoticeTypeSystem(),
            ]
        ];
    }

    /**
     * 发送一条站内信
     * @param $data
     * @return array|false|string
     */
    protected function sendOneStationMsg($data)
    {
        $user_ids  = $data['user_ids'] ?? 0;  // 用户id数组

        if (!$user_ids)
            return r_result(201, '缺少用户id数组');

        if ($this->checkIsSendMax())
            return r_result(202, '已达到最大发送数量');

        // 同步执行发送消息
        $service = new MessageService();
        $re = $service->sendMessage($data);

        return r_result($re['code'], $re['desc']);

//        // 发送通知
//        try {
//            // 队列执行发送消息
//            StationMsg::dispatch($data);
//
//        } catch (\Exception $e) {
//            // 同步执行发送消息
//            $service = new MessageService();
//            $re = $service->sendMessage($data);
//
//            return r_result($re['code'], $re['desc']);
//        }
//
//        return r_result(200, '发送成功');
    }

}
