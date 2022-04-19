<?php

namespace App\Http\Controllers\Api;

use App\Enums\Http\ResponseCode;
use App\Http\Requests\Api\GoodsRequest;
use App\Services\GoodsService;
use Illuminate\Http\Request;

class GoodsController extends Controller
{

    /**
     * 获取商品列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGoodsList(Request $request)
    {
        $params = $request->all();
//        $params['user_id'] = $this->userinfo['id'] ?? 1; // 当前登录者id

        $service = new GoodsService();
        $list = $service->getGoodsList($params);

        return response()->json($this->type(ResponseCode::REQUEST_SUCCESS)->httpResponse($list));
    }

    /**
     * 获取商品详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGoodsDetail(Request $request)
    {
        $params = $request->all();

        $service = new GoodsService();
        $res = $service->getGoodsDetail($params);

        if($res['code'] != 200)
            return response()->json($this->type(ResponseCode::PARAM_ERROR)->httpResponse('',$res['desc']));

        return response()->json($this->type(ResponseCode::REQUEST_SUCCESS)->httpResponse($res['data'],'操作成功'));
    }

    /**
     * 新增商品
     * @param GoodsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addGoods(GoodsRequest $request)
    {
        $data = $request->all();
        $data['user_id'] = $this->userinfo['id'] ?? 1; // 当前登录者id

        $service = new GoodsService();
        $res = $service->updateOrCreateGoods($data);

        if($res['code'] != 200)
            return response()->json($this->type(ResponseCode::PARAM_ERROR)->httpResponse('',$res['desc']));

        return response()->json($this->type(ResponseCode::REQUEST_SUCCESS)->httpResponse('','操作成功'));
    }

    /**
     * 编辑商品
     * @param GoodsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editGoods(GoodsRequest $request)
    {
        $data = $request->all();
        $data['user_id'] = $this->userinfo['id'] ?? 1; // 当前登录者id

        $service = new GoodsService();
        $res = $service->updateOrCreateGoods($data);

        if($res['code'] != 200)
            return response()->json($this->type(ResponseCode::PARAM_ERROR)->httpResponse('',$res['desc']));

        return response()->json($this->type(ResponseCode::REQUEST_SUCCESS)->httpResponse('','操作成功'));
    }

    /**
     * 删除商品
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delGoods(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = $this->userinfo['id'] ?? 0; // 当前登录者id

        $service = new GoodsService();
        $res = $service->delGoods($data);

        if($res['code'] != 200)
            return response()->json($this->type(ResponseCode::PARAM_ERROR)->httpResponse('',$res['desc']));

        return response()->json($this->type(ResponseCode::REQUEST_SUCCESS)->httpResponse('','操作成功'));
    }


}
