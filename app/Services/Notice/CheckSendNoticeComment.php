<?php
/*
 * @Description: 评论消息通知
 * @Version:
 * @Author: LiZhongDa
 * @Date: 2022-03-31 14:22:33
 */

namespace App\Services\Notice;

use App\Models\User;
use App\Models\WorksComment;
use App\Services\WorksService;

class CheckSendNoticeComment
{
    use NoticeConfig;

    /**
     * 发送作品评论通知
     * @return array|false|string
     */
    public function send()
    {
        $works_id   = $this->p['works_id'] ?? 0;   // 作品id
        $about_id   = $this->p['about_id'] ?? 0;   // 相关id
        $comment_id = $this->p['comment_id'] ?? 0; // 评论id

        if (!$works_id || !$about_id || !$comment_id)
            return r_result(201, '缺少参数', ['sendNum' => 0]);

        $works_info = WorksService::queryOneWorks($works_id, true);
        if (!$works_info)
            return r_result(202, '作品信息不存在', ['sendNum' => 0]);

        // 作品作者用户id
        $user_id = $works_info['user_id'];

        // 检查是否已经发送
        if ($this->checkNoticeHaveSend($user_id, $comment_id))
            return r_result(200, '已经发送过', ['sendNum' => 0]);

        // 查询评论用户信息
        $user_info = User::query()->find($about_id);
        if (!$user_info)
            return r_result(203, '评论用户信息不存在', ['sendNum' => 0]);

        // 查询评论内容
        $comment_info = WorksComment::query()->where('id', $comment_id)->first();
        if (!$comment_info)
            return r_result(204, '评论数据不存在', ['sendNum' => 0]);

        // 判断是否为回复评论，显示不同文案，查询回复给某人的 user_id
        $text = ' 评论了你的作品：';
        if ($comment_info['comment_id']) {
            $user_id = WorksComment::query()->where('id', $comment_info['comment_id'])->value('user_id');
            $text = ' 回复了你的评论：';
        }

        // 拼接 消息简介说明
        $details = $user_info->avatar;
        $details .= '&$'. $user_info->nickname;
        $details .= '&$'. $text;
        $details .= '&$'. $comment_info->content;
        $details .= '&$'. $works_id;

        $r_type = $works_info['works_resource'][0]['r_type'] ?? 0;
        $details .= '&$'. $r_type;

        // TODO 视频暂时还是用视频作为资源地址
        if ($r_type == 3)
            $details .= '&$'. $works_info['works_resource'][0]['r_url'] ?? '';
        else
            $details .= '&$'. $works_info['cover_url'] ?? '';

        $send_data = [
            'introduction' => $user_info->nickname . $text . $comment_info->content,
            'details'      => $details,
            'to_group'     => 1,
            'user_ids'     => [$user_id],
            'type'         => 3, // 评论
        ];

        $res = $this->sendOneStationMsg($send_data);

        if ($res['code'] == 200) {
            $this->addSendNum();
            $this->addNoticeLog($user_id, $comment_id, $send_data);
        }

        return r_result(200, '发送成功', ['sendNum' => $this->sendNum]);
    }

}
