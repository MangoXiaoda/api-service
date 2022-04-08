<?php
/*
 * @Description: 运营服务类
 * @Version:
 * @Author: LiZhongDa
 * @Date: 2022-03-29 11:42:33
 */

namespace App\Services;

use App\Models\Banners;

class OperationService extends Service
{

    /**
     * 获取 banner 列表
     * @return array
     */
    public function getBannerList()
    {
        $list = Banners::query()
            ->select('id', 'name', 'image', 'url')
            // 状态正常的轮播图
            ->where('status', 1)
            ->orderByDesc('sort')
            ->get()
            ->toArray();

        return $list;
    }

}
