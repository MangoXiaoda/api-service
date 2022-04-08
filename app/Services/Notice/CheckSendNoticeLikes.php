<?php
/*
 * @Description: 点赞消息通知
 * @Version:
 * @Author: LiZhongDa
 * @Date: 2022-03-31 10:34:33
 */

namespace App\Services\Notice;

use App\Models\User;
use App\Services\WorksService;

class CheckSendNoticeLikes
{
    use NoticeConfig;

    /**
     * 发送作品点赞通知
     * @return array|false|string
     */
    public function send()
    {
        $works_id = $this->p['works_id'] ?? 0; // 作品id
        $about_id = $this->p['about_id'] ?? 0; // 相关id

        if (!$works_id || !$about_id)
            return r_result(201, '缺少参数', ['sendNum' => 0]);

        $works_info = WorksService::queryOneWorks($works_id, true);
        if (!$works_info)
            return r_result(202, '作品信息不存在', ['sendNum' => 0]);

        // 作品作者用户id
        $user_id = $works_info['user_id'];

        // 检查是否已经发送
        if ($this->checkNoticeHaveSend($user_id, $about_id. '_' .$works_id))
            return r_result(200, '已经发送过', ['sendNum' => 0]);

        // 查询点赞用户信息
        $user_info = User::query()->find($about_id);
        if (!$user_info)
            return r_result(203, '点赞用户信息不存在', ['sendNum' => 0]);

        // 拼接 消息简介说明
        $details = $user_info->avatar;
        $details .= '&$'. $user_info->nickname;
        $details .= '&$'. '赞了你的作品';
        $details .= '&$'. $works_id;

        $r_type = $works_info['works_resource'][0]['r_type'] ?? 0;
        $details .= '&$'. $r_type;

        // TODO 视频暂时还是用视频作为资源地址
        if ($r_type == 3)
            $details .= '&$'. $works_info['works_resource'][0]['r_url'] ?? '';
        else
            $details .= '&$'. $works_info['cover_url'] ?? '';

        $send_data = [
            'introduction' => $user_info->nickname . ' 赞了你的作品',
            'details'      => $details,
            'to_group'     => 1,
            'user_ids'     => [$user_id],
            'type'         => 5, // 点赞
        ];

        $res = $this->sendOneStationMsg($send_data);

        if ($res['code'] == 200) {
            $this->addSendNum();
            $this->addNoticeLog($user_id, $about_id. '_' .$works_id, $send_data);
        }

        return r_result(200, '发送成功', ['sendNum' => $this->sendNum]);
    }


}
