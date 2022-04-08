<?php
/*
 * @Description: 积分服务类
 * @Version:
 * @Author: LiZhongDa
 * @Date: 2022-03-24 14:34:33
 */

namespace App\Services;

use App\Models\User;
use App\Models\UserPointsLog;
use Illuminate\Support\Facades\DB;

class PointService extends Service
{

    // 用户id
    private $user_id = 0;

    // 每页数据条数
    private $perpage = 10;

    /**
     * 增加用户积分
     * @param $data
     * @return array|bool|string
     */
    public static function addUserPoint($data)
    {
        $about_id    = $data['about_id'] ?? 0;    // 相关id （评论id、作品id）
        $user_id     = $data['user_id'] ?? 0;     // 用户id
        $change_type = $data['change_type'] ?? 0; // 变更方式：1增加 2减少
        $p_type      = $data['p_type'] ?? 0;      // 积分渠道：1签到 2评论 3分享

        if (!$about_id)
            return r_result(201, '缺少相关id');

        if (!$user_id)
            return r_result(202, '缺少用户id');

        if (!in_array($change_type, [1,2]))
            return r_result(203, '变更方式有误');

        if (!in_array($p_type, [1,2,3]))
            return r_result(204, '积分渠道有误');

        // TODO 目前暂未设置积分规则，都按一分来计算，后续调整
        $p_value = 1;

        $point_log = [
            'about_id'    => $about_id,    // 相关id
            'user_id'     => $user_id,     // 用户id
            'change_type' => $change_type, // 变更方式
            'p_type'      => $p_type,      // 渠道
            'p_value'     => $p_value,     // 积分
        ];

        switch ($p_type) {
            // 签到加积分
            case 1:
                $point_log['msg'] = '签到积分';
                break;
            // 评论加积分
            case 2:
                $point_log['msg'] = '评论积分';
                break;
            // 分享加积分
            case 3:
                $point_log['msg'] = '分享积分';
                break;
        }

        // 使用事务，保证数据一致性
        DB::transaction(function () use ($point_log, $p_value, $user_id) {

            UserPointsLog::query()->create($point_log);
            User::query()->where('id', $user_id)->increment('point_count', $p_value);

        });

        return true;
    }

    /**
     * 获取用户积分列表
     * @param $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getUserPointList($params)
    {
        $this->setSearchParams($params);

        $list = UserPointsLog::query()
            ->where('user_id', $this->user_id)
            ->orderByDesc('updated_at')
            ->paginate($this->perpage)
            ->appends($params);

        return $list;
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


    }

}
