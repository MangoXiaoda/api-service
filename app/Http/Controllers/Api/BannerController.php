<?php

namespace App\Http\Controllers\Api;

use App\Enums\Http\ResponseCode;
use App\Services\OperationService;
use Illuminate\Http\Request;

class BannerController extends Controller
{

    /**
     * 获取 banner 列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBannerList()
    {
        $service = new OperationService();
        $list = $service->getBannerList();

        return response()->json($this->type(ResponseCode::REQUEST_SUCCESS)->httpResponse($list));
    }

}
