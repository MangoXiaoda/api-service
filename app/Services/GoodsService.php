<?php
/*
 * @Description: 商品服务类
 * @Version:
 * @Author: LiZhongDa
 * @Date: 2022-03-16 15:10:33
 */

namespace App\Services;

use App\Models\Goods;
use App\Models\GoodsImg;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoodsService extends Service
{
    // 用户id
    private $user_id = 0;

    // 搜索
    private $keyword = '';

    // 每页数据条数
    private $perpage = 10;

    /**
     * 获取商品列表
     * @param $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getGoodsList($params)
    {
        $this->setSearchParams($params);

        $list = Goods::query()
            // 搜索
            ->when($this->keyword, function ($query) {
                $query->where('content', 'like', '%'. $this->keyword . '%');
            })
            // 关联用户表
            ->with('user:id,name,nickname,avatar')
            // 关联商品分类表
            ->with('goods_category:id,c_name')
            // 关联商品图片表
            ->with('goods_images:goods_id,image')
            ->orderByDesc('view_count')
            ->orderByDesc('updated_at')
            ->paginate($this->perpage)
            ->appends($params);

        return $list;
    }

    /**
     * 获取商品详情
     * @param $params
     * @return array|false|string
     */
    public function getGoodsDetail($params)
    {
        $gs_id = $params['gs_id'] ?? 0;

        if (!$gs_id)
            return r_result(201, '缺少商品id');

        $info = self::queryOneGoods($gs_id, true);

        if (!$info)
            return r_result(202, '数据不存在，请重试');

        // 处理商品详情图片
        $goods_images = $info['goods_images'];
        $info['images'] = [];
        if ($goods_images)
            $info['images'] = array_column($goods_images, 'image');

        return r_result(200, '获取成功', $info);
    }

    /**
     * 更新或新增商品
     * @param $data
     * @return array|false|string
     */
    public function updateOrCreateGoods($data)
    {
        $id        = $data['gs_id'] ?? 0;      // 商品id
        $user_id   = $data['user_id'];         // 用户id
        $gs_pics   = $data['gs_pics'] ?? [];   // 商品图片

        if (!$user_id)
            return r_result(201, '请先登录');

        // 兼容数据格式
        $gs_pics = is_array($gs_pics) ? $gs_pics : explode(',', $gs_pics);

        // 过滤无用数据，只更新数据库有的字段
        $data = collect($data)->only([
            'category_id', 'title', 'thumb', 'unit', 'content', 'goods_sn',
            'price', 'cost_price', 'total', 'sales', 'status', 'view_count'
        ]);

        $data = $data->toArray();

        // 商品主图
        $thumb = $gs_pics[0] ?? '';
        $data['thumb'] = delWebPrefixUrl($thumb);

        try {
            DB::beginTransaction();

            $re = Goods::query()->updateOrCreate(['id' => $id], $data);

            if (($gs_id = $re->id) && $gs_pics) {
                // 先删除旧的资源地址
                GoodsImg::query()->where('goods_id', $gs_id)->delete();
                // 插入新的资源地址
                $resourceArr = [];
                // 去除数组内空元素
                array_filter($gs_pics);
                foreach ($gs_pics as $key => $url) {

                    $resourceArr[$key]['goods_id'] = $gs_id;
                    // 检测资源地址是否有前缀域名，去除前缀域名
                    $resourceArr[$key]['image'] = delWebPrefixUrl($url);
                    GoodsImg::query()->create($resourceArr[$key]);

                }
            }

            DB::commit();

            return r_result(200, '操作成功');

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error('新增或更新商品失败', ['message' => $e->getMessage()]);

            return r_result(203, '操作失败');
        }
    }

    /**
     * 删除商品
     * @param $data
     * @return array|false|string
     */
    public function delGoods($data)
    {
        $gs_id = $data['gs_id'] ?? 0; // 商品id

        if (!$gs_id)
            return r_result(201, '缺少商品id');

        // 使用事务，保证数据一致性
        DB::transaction(function () use ($gs_id) {

            // 删除商品图片
            GoodsImg::query()->where('goods_id', $gs_id)->delete();

            // 删除商品
            Goods::query()->where('id', $gs_id)->delete();

        });

        return r_result(200, '操作成功');
    }

    /**
     * 查询一条商品信息
     * @param $id
     * @param false $is_with
     * @return array
     */
    public static function queryOneGoods($id, $is_with = false)
    {
        if (!$id)
            return [];

        $info = Goods::query()
            ->where('id', $id)
            ->when($is_with, function ($query) {
                $query->with('goods_images:goods_id,image');
            })
            ->first();

        return $info ? $info->toArray() : [];
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

        // 搜索
        if (isset($params['keyword']))
            $this->keyword = $params['keyword'];
    }


}
