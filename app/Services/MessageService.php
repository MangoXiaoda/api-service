<?php
/*
 * @Description: 消息服务类
 * @Version:
 * @Author: LiZhongDa
 * @Date: 2022-03-16 15:10:33
 */

namespace App\Services;

use App\Models\UserMessage;
use App\Models\UserMessageStatus;
use Carbon\Carbon;
use Doctrine\DBAL\Query\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MessageService extends Service
{
    // 用户id
    private $user_id = 0;

    // 消息类组
    private $to_group = -1;

    // 是否已读
    private $is_read = -1;

    // 消息类型
    private $msg_type = 0;

    // 当前页码
    private $page = 1;

    // 每页条数
    private $perpage = 10;

    /**
     * 获取消息列表
     * @param $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getMessageList($params)
    {
        $this->setSearchParams($params);

        $list = UserMessage::query()
            ->with(['user_message_status' => function ($query) {
                // 有用户uid时，只获取对应登录用户的消息状态数据
                $query->when($this->user_id, function ($query) {
                    $query->where('uid', $this->user_id);
                });
            }])
            // 按消息类型过滤数据 1系统通知,2用户,3评论,4兑换,5点赞,6关注
            ->when($this->msg_type, function ($query) {
                $query->where('type', $this->msg_type);
            })
            // 按消息类组过滤数据 0全平台,1指定用户
            ->when($this->to_group > -1, function ($query) {
                $query->where('to_group', $this->to_group);
            })
            // 消息类组为 指定用户时，按用户id 过滤数据
            ->when($this->to_group == 1 && $this->user_id, function ($query) {
                $query->whereHas('user_message_status', function ($query) {
                    $query->where('uid', $this->user_id);
                });
            })
            // 指定用户消息数据（区分已读未读数据 0 未读 1 已读）
            ->when($this->to_group == 1 && $this->is_read > -1, function ($query) {
                $query->whereHas('user_message_status', function ($query) {
                    $query->where('status', $this->is_read);
                });
            })
            // 系统消息数据（区分已读未读数据 0 未读 1 已读）
            ->when($this->to_group == 0 && $this->is_read > -1, function ($query) {
                // 未读
                if ($this->is_read == 0)
                    $query->whereDoesntHave('user_message_status');
                // 已读
                if ($this->is_read == 1)
                    $query->whereHas('user_message_status');
            })
            // 按时间降序排序
            ->orderBy('created_at', 'desc')
            ->paginate($this->perpage)
            ->appends($params);

        $this->checkUserMessageStatus($list);

        // 标记消息为已读（为了防止多次调用已读接口，只在获取列表第一页时标记所有未读消息为已读）
        if ($this->page == 1)
            $this->markMessageRead([
                'to_group' => $this->to_group,
                'user_id'  => $this->user_id,
                'msg_type' => $this->msg_type
            ]);

        return $list;
    }

    /**
     * 检测用户消息已读未读
     * @param $list
     * @return mixed
     */
    private function checkUserMessageStatus(&$list)
    {
        collect($list->items())->values()->each(function ($item) {
            // 检测用户 消息已读未读
            $item['is_read'] = 0;
            $message_status = $item['user_message_status'][0]['status'] ?? 0;
            if ($message_status == 1)
                $item['is_read'] = 1;

            // 阅读时间
            $item['read_time'] = $item['user_message_status'][0]['read_time'] ?? '';

            unset($item['user_message_status']);
        });

        return $list;
    }

    /**
     * 发送消息
     * @param $data
     * @return array|false|string
     */
    public function sendMessage($data)
    {
        $to_group = $data['to_group'] ?? 1;  // 消息类组 0全平台,1指定用户
        $user_ids = $data['user_ids'] ?? []; // 用户id 数组

        if ($to_group == 1 && !$user_ids)
            return r_result(201, '发送给指定用户时，请选择用户');

        $data = collect($data)->only([
            'type', 'to_group', 'introduction', 'details'
        ]);

        if ($data->isEmpty())
            return r_result(202, '数据有误');

        $data = $data->toArray();

        try {
            DB::beginTransaction();

            $res = UserMessage::query()->create($data);

            // 发送给指定用户时，信息状态表写入数据，其它情况不写入
            if ($to_group == 1) {
                foreach ($user_ids as $uid) {
                    UserMessageStatus::query()->create([
                        'uid'         => $uid,
                        'messages_id' => $res->id
                    ]);
                }
            }

            DB::commit();

            return r_result(200, '操作成功');
        } catch (QueryException $e) {

            DB::rollBack();
            Log::error('发送消息失败', ['message' => $e->getMessage()]);
            return r_result(203, '操作失败');
        }
    }

    /**
     * 获取消息详情
     * @param $params
     * @return array|false|string
     */
    public function getMessageDetail($params)
    {
        $message_id = $params['message_id'] ?? 0; // 消息id
        $user_id    = $params['user_id'] ?? 0;    // 请先登录
        $to_group   = $params['to_group'] ?? -1;  // 消息类组 0全平台,1指定用户

        if (!$message_id)
            return r_result(201, '缺少消息id');

        if (!$user_id)
            return r_result(202, '请先登录');

        if (!in_array($to_group, [0, 1]))
            return r_result(203, '消息类组参数有误');

        $info = UserMessage::query()
            ->where('id', $message_id)
            // 消息类组为 指定用户时
            ->when($to_group == 1, function ($query) use ($user_id) {
                $query->whereHas('user_message_status', function ($query) use ($user_id) {
                    $query->where('uid', $user_id);
                });
            })
            ->first();

        if (!$info)
            return r_result(204, '消息数据有误');

        // 标记消息为已读
        $this->markMessageRead([
            'to_group'   => $to_group,
            'user_id'    => $user_id,
            'message_id' => $message_id
        ]);

        return r_result(200, '获取成功', $info->toArray());
    }

    /**
     * 标记消息为已读
     * @param $data
     * @return array|false|string
     */
    public function markMessageRead($data)
    {
        $to_group   = $data['to_group'] ?? -1;  // 消息类组:0全平台,1指定用户
        $user_id    = $data['user_id'];         // 用户id
//        $message_id = $data['message_id'] ?? 0; // 消息id
        $msg_type   = $data['msg_type'] ?? 0;   // 消息类型:1系统通知,2用户,3评论,4兑换,5点赞,6关注

        if (!in_array($to_group, [0, 1]))
            return r_result(201, '消息类组参数有误');

        if (!$user_id)
            return r_result(202, '缺少用户编号');

//        if (!$message_id)
//            return r_result(203, '缺少消息id');

        $update_data = [
            'uid'       => $user_id,
            'status'    => 1,
            'read_time' => Carbon::now()
        ];

//        $where_arr = [
//            'messages_id' => $message_id,
//            'uid'         => $user_id
//        ];

        // 指定用户消息标记为已读
        if ($to_group == 1)
            UserMessageStatus::query()
                ->where('uid', $user_id)
                ->when($msg_type, function ($query) use ($msg_type) {
                    $query->whereHas('user_message', function ($query) use ($msg_type) {
                        $query->where('type', $msg_type);
                    });
                })
                ->update($update_data);

        // 平台消息标记为已读
        if ($to_group == 0)
            UserMessageStatus::query()->updateOrCreate(['uid' => $user_id], $update_data);

        return r_result(200, '操作成功');
    }

    /**
     * 获取消息未读数
     * @param $params
     * @return array|false|string
     */
    public static function getMessageUnReadNum($params)
    {
        $user_id   = $params['user_id'] ?? 0;
        $data_type = $params['data_type'] ?? 0; // 1 获取总的消息未读数 2 分开获取各个消息未读数

        if (!$user_id)
            return r_result(201, '请先登录');

        $system_unread_num = self::countMsgUnReadNum(['user_id' => $user_id, 'to_group' => 0]);
        $user_unread_num   = self::countMsgUnReadNum(['user_id' => $user_id, 'to_group' => 1]);

        $all_unread_num = $system_unread_num + $user_unread_num;
        if ($data_type == 1)
            return r_result(200, '获取成功', $all_unread_num);

        $data =  [
            'system_unread_num' => $system_unread_num,
            'user_unread_num'   => $user_unread_num,
        ];

        return r_result(200, '获取成功', $data);
    }

    /**
     * 计算消息未读数
     * @param $params
     * @return int
     */
    public static function countMsgUnReadNum($params)
    {
        $user_id  = $params['user_id'] ?? 0;   // 用户id
        $to_group = $params['to_group'] ?? -1; // 消息类组:0全平台,1指定用户

        if (!$user_id || !in_array($to_group, [0, 1]))
            return 0;

        // 计算未读消息数
        $message_num = UserMessageStatus::query()
            ->where('uid', $user_id)
            ->when($to_group == 1, function ($query) {
                $query->where('status', 0);
            })
            ->whereHas('user_message', function ($query) use ($to_group) {
                $query->where('to_group', $to_group);
            })
            ->count();

        // 指定人消息未读数 可直接获取
        if ($to_group == 1)
            return $message_num;

        $count = UserMessage::query()
            ->where('to_group', $to_group)
            ->count();
        $unread_num = $count - $message_num;

        return $unread_num;
    }

    /**
     * 设置搜索参数
     * @param $params
     */
    private function setSearchParams($params)
    {
        // 用户id
        if (isset($params['user_id'])) {
            $this->user_id = $params['user_id'];
        }

        // 消息类组
        if (isset($params['to_group'])) {
            $this->to_group = $params['to_group'];
        }

        // 消息类型
        if (isset($params['msg_type'])) {
            $this->msg_type = $params['msg_type'];
        }

        // 是否已读
        if (isset($params['is_read'])) {
            $this->is_read = $params['is_read'];
        }

        // 当前页码
        if (isset($params['page'])) {
            $this->page = $params['page'];
        }

    }

}
