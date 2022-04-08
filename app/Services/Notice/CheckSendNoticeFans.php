<?php
/*
 * @Description: 关注消息通知
 * @Version:
 * @Author: LiZhongDa
 * @Date: 2022-03-31 10:34:33
 */

namespace App\Services\Notice;

use App\Models\UserFans;

class CheckSendNoticeFans
{
    use NoticeConfig;

    /**
     * 发送关注通知
     * @return array|false|string
     */
    public function send()
    {
        $id = $this->p['id'] ?? 0; // 粉丝表id

        if (!$id)
            return r_result(201, '缺少参数', ['sendNum' => 0]);

        $info = UserFans::query()
            ->where('id', $id)
            ->with('user:id,name,nickname,avatar')
            ->with('user_fans:id,name,nickname,avatar')
            ->first();

        if (!$info)
            return r_result(202, '粉丝数据不存在', ['sendNum' => 0]);

        $user_id = $info->user_id;

        // 检查是否已经发送
        if ($this->checkNoticeHaveSend($user_id, $id))
            return r_result(200, '已经发送过', ['sendNum' => 0]);

        // 拼接 消息简介说明
        $details = $info->user_fans->avatar;
        $details .= '&$'. $info->user_fans->nickname;
        $details .= '&$'. '关注了你';
        $details .= '&$'. $info->status;

        $send_data = [
            'introduction' => $info->user_fans->nickname . ' 关注了你',
            'details'      => $details,
            'to_group'     => 1,
            'user_ids'     => [$user_id],
            'type'         => 6, // 关注
        ];

        $res = $this->sendOneStationMsg($send_data);

        if ($res['code'] == 200) {
            $this->addSendNum();
            $this->addNoticeLog($user_id, $id, $send_data);
        }

        return r_result(200, '发送成功', ['sendNum' => $this->sendNum]);
    }

}
