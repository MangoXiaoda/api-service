<?php
/*
 * @Description: 订单服务类
 * @Version:
 * @Author: LiZhongDa
 * @Date: 2022-04-11 15:10:33
 */

namespace App\Services;


use App\Models\OrderSub;

class OrderService extends Service
{

    // 用户id
    private $user_id = 0;

    // 分页参数
    private $perpage = 10;

    /**
     * 获取订单列表
     * @param $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getOrderList($params)
    {
        $this->setSearchParams($params);

        $list = OrderSub::query()
            // 按用户id 搜索
            ->when($this->user_id, function ($query) {
                $query->where('user_id', $this->user_id);
            })
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
