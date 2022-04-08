<?php
/*
 * @Description: user服务类
 * @Version:
 * @Author: LiZhongDa
 * @Date: 2022-03-22 10:35:33
 */

namespace App\Services;

use App\Models\User;
use App\Models\UserFans;
use App\Models\Works;

class UserService extends Service
{

    // 用户id
    private $user_id = 0;

    // 粉丝类型，1 关注我的 2 我关注的
    private $f_type = 0;

    // 每页数据条数
    private $perpage = 10;

    /**
     * 获取用户信息
     * @param $params
     * @return array|false|string
     */
    public function getUserInfo($params)
    {
        if (!$this->hasAuthorization())
            return r_result(201, '未授权登录，请先登录');

        $user_id = $params['user_id'];

        if (!$user_id)
            return r_result(202, '请先登录');

        $user_info = User::query()
            ->select('id', 'name', 'nickname', 'gender', 'avatar', 'phone', 'permission_status')
            ->where('id', $user_id)
            ->first()
            ->toArray();

        // 关注数
        $user_info['focus_num'] = UserFans::query()->where('user_id', $user_id)->count();
        // 粉丝数
        $user_info['fans_num']  = UserFans::query()->where('fans_id', $user_id)->count();
        // 作品数
        $user_info['works_num'] = Works::query()->where('user_id', $user_id)->count();
        // 积分数
        $user_info['point_num'] = 0;
        // 消息未读数
        $user_info['unread_num'] = 0;
        $res = MessageService::getMessageUnReadNum(['user_id' => $user_id, 'data_type' => 1]);
        if ($res['code'] == '200')
            $user_info['unread_num'] = $res['data'];

        return r_result(200, '获取成功', $user_info);
    }

    /**
     * 判断是否授权登录（没有昵称说明没有授权认为未登录，无法进行相关操作）
     * @return bool
     */
    public function hasAuthorization()
    {
        $user = auth()->user()->toArray();

        if (empty($user['nickname']))
            return false;

        return true;
    }

    /**
     * 编辑用户信息
     * @param $data
     * @return array|false|string
     */
    public function editUserInfo($data)
    {
        $user_id = $data['user_id'] ?? 0;

        if (!$user_id)
            return r_result(201, '请先登录');

        $data = collect($data)->only([
            'id', 'name', 'nickname', 'gender', 'avatar', 'phone', 'permission_status'
        ]);

        $data = $data->toArray();

        User::query()->where('id', $user_id)->update($data);

        $res = $this->getUserInfo(['user_id' => $user_id]);

        if ($res['code'] != 200)
            return $res;

        return r_result(200, '获取成功', $res['data']);
    }

    /**
     * 用户 关注/取消关注 粉丝
     * @param $data
     * @return array|false|string
     */
    public function UserFans($data)
    {
        $user_id = $data['user_id'];
        $fans_id = $data['fans_id'];
        $f_type  = $data['f_type'];  // 操作类型 1 关注 2 取消关注

        if (!$fans_id)
            return r_result(201, '请先登录');

        $data = collect($data)->only([
            'user_id', 'fans_id'
        ]);

        $data = $data->toArray();

        // 正向条件
        $where_arr = [
            'user_id' => $user_id,
            'fans_id' => $fans_id,
        ];

        // 反向条件
        $reverse_where = [
            'user_id' => $fans_id,
            'fans_id' => $user_id,
        ];

        // 检测是否为互相关注
        $is_mutual = UserFans::query()->where($reverse_where)->count();
        if ($is_mutual) {
            $data['status'] = 1;
            // 修改互粉状态
            UserFans::query()->where($reverse_where)->update(['status' => 1]);
        }

        switch ($f_type) {
            // 关注
            case 1:
                $re = UserFans::query()->updateOrCreate($where_arr, $data);
                $nt = new NoticeService('s_3');
                $nt->checkSendOne(['id' => $re->id]);
                break;
            // 取消关注
            case 2:
                UserFans::query()->where($where_arr)->delete();
                UserFans::query()->where($reverse_where)->update(['status' => 0]);
                break;
        }

        return r_result(200, '操作成功');
    }

    /**
     * 获取用户粉丝 我关注的/关注我的 列表
     * @param $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getFansList($params)
    {
        $this->setSearchParams($params);

        $list = UserFans::query()
            // 获取 关注我的/我关注的 粉丝列表
            ->when(in_array($this->f_type, [1, 2]), function ($query) {

                switch ($this->f_type) {
                    // 关注我的
                    case 1:
                        $query->where('user_id', $this->user_id);
                        break;
                    // 我关注的
                    case 2:
                        $query->where('fans_id', $this->user_id);
                        break;
                }

            })
            // 关联用户表（当前登陆者）
            ->with('user:id,name,nickname,avatar')
            // 关联用户表（粉丝）
            ->with('user_fans:id,name,nickname,avatar')
            // 获取粉丝作品数
            ->withCount(['fans_works'])
            ->paginate($this->perpage)
            ->appends($params);

        collect($list->items())->values()->each(function ($item) {
            // 计算粉丝用户的粉丝数
            $item['fans_user_num'] = UserFans::query()->where('fans_id', $item['fans_id'])->count();
        });

        return $list;
    }

    /**
     * 作品是否有权限
     * @param $works_id
     * @param $user_id
     * @return bool
     */
    public static function hasWorksPermission($works_id, $user_id)
    {
        if (!$works_id || !$user_id)
            return false;

        // 本人作品不需要权限控制
        $q_user_id = Works::query()->where('id', $works_id)->value('user_id');
        if (!$q_user_id)
            return false;

        if ($user_id == $q_user_id)
            return true;

        $res = Works::query()
            ->where('id', $works_id)
            ->where('permission_status', 0)
            ->whereHas('user', function ($query) {
                $query->where('permission_status', 0);
            })
            ->count();

        return $res ? true : false;
    }

    /**
     * 设置搜索参数
     * @param $params
     */
    private function setSearchParams($params)
    {

        // 用户id
        if (isset($params['user_id']))
            $this->user_id = $params['user_id'];

        // 粉丝类型，1 关注我的 2 我关注的
        if (isset($params['f_type']))
            $this->f_type = $params['f_type'];

    }


}
