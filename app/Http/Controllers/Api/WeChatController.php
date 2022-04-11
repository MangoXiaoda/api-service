<?php

namespace App\Http\Controllers\Api;

use App\Enums\Http\ResponseCode;
use App\Services\WeChatService;
use Illuminate\Http\Request;

class WeChatController extends Controller
{

    /**
     * 微信支付
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function wxPay(Request $request)
    {
        $data = $request->all();
        $data['user_info'] = $this->userinfo ?? [];

        $service = new WeChatService();
        $res = $service->pay($data);

        if ($res['code'] != 200)
            return response()->json($this->type(ResponseCode::PARAM_ERROR)->httpResponse('', $res['desc']));

        return response()->json($this->type(ResponseCode::REQUEST_SUCCESS)->httpResponse($res['data'], '操作成功'));
    }


}
