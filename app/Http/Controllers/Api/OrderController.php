<?php

namespace App\Http\Controllers\Api;

use App\Enums\Http\ResponseCode;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    /**
     * 获取订单列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrderList(Request $request)
    {
        $params = $request->all();
        $params['user_id'] = $user_id = $this->userinfo['id'] ?? 0; // 当前登录者id

        if (!$user_id)
            return response()->json($this->type(ResponseCode::PARAM_ERROR)->httpResponse('', '请先登录'));

        $service = new OrderService();
        $list = $service->getOrderList($params);

        return response()->json($this->type(ResponseCode::REQUEST_SUCCESS)->httpResponse($list));
    }


}
