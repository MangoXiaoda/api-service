<?php

namespace App\Http\Controllers\Api;

use App\Enums\Http\ResponseCode;
use App\Http\Requests\Api\UserFansRequest;
use App\Services\MessageService;
use App\Services\PointService;
use App\Services\SettingService;
use App\Services\UserService;
use App\Services\WorksService;
use Illuminate\Http\Request;

class MeController extends Controller
{

    /**
     * 获取用户信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserInfo()
    {
        $params['user_id'] = $this->userinfo['id'] ?? 0; // 当前登录者id

        $service = new UserService();
        $res = $service->getUserInfo($params);

        if($res['code'] != 200)
            return response()->json($this->type(ResponseCode::PARAM_ERROR)->httpResponse('',$res['desc']));

        return response()->json($this->type(ResponseCode::REQUEST_SUCCESS)->httpResponse($res['data'],'获取成功'));
    }

    /**
     * 编辑用户信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editUserInfo(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = $this->userinfo['id'] ?? 0; // 当前登录者id

        $service = new UserService();
        $res = $service->editUserInfo($data);

        if($res['code'] != 200)
            return response()->json($this->type(ResponseCode::PARAM_ERROR)->httpResponse('',$res['desc']));

        return response()->json($this->type(ResponseCode::REQUEST_SUCCESS)->httpResponse($res['data'],'操作成功'));
    }

    /**
     * 用户 关注/取消关注 粉丝
     * @param UserFansRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function UserFans(UserFansRequest $request)
    {
        $data = $request->all();
        $data['fans_id'] = $this->userinfo['id'] ?? 0; // 当前登录者id

        $service = new UserService();
        $res = $service->UserFans($data);

        if($res['code'] != 200)
            return response()->json($this->type(ResponseCode::PARAM_ERROR)->httpResponse('',$res['desc']));

        return response()->json($this->type(ResponseCode::REQUEST_SUCCESS)->httpResponse($res['data'],'操作成功'));
    }

    /**
     * 获取用户粉丝 我关注的/关注我的 列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFansList(Request $request)
    {
        $params = $request->all();
        $params['user_id'] = $this->userinfo['id'] ?? 0; // 当前登录者id

        $service = new UserService();
        $list = $service->getFansList($params);

        return response()->json($this->type(ResponseCode::REQUEST_SUCCESS)->httpResponse($list));
    }

    /**
     * 获取收藏列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCollectionList(Request $request)
    {
        $params = $request->all();
        $params['user_id'] = $this->userinfo['id'] ?? 0; // 当前登录者id

        $service = new WorksService();
        $list = $service->getCollectionList($params);

        return response()->json($this->type(ResponseCode::REQUEST_SUCCESS)->httpResponse($list));
    }

    /**
     * 获取用户积分列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserPointList(Request $request)
    {
        $params = $request->all();
        $params['user_id'] = $this->userinfo['id'] ?? 0; // 当前登录者id

        $service = new PointService();
        $list = $service->getUserPointList($params);

        return response()->json($this->type(ResponseCode::REQUEST_SUCCESS)->httpResponse($list));
    }

    /**
     * 获取消息列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMessageList(Request $request)
    {
        $params = $request->all();
        $params['user_id'] = $this->userinfo['id'] ?? 0;

        $service = new MessageService();
        $list = $service->getMessageList($params);

        return response()->json($this->type(ResponseCode::REQUEST_SUCCESS)->httpResponse($list));
    }

    /**
     * 获取消息详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMessageDetail(Request $request)
    {
        $params = $request->all();
        $params['user_id'] = $this->userinfo['id'] ?? 0; // 当前登录用户id

        $service = new MessageService();
        $res = $service->getMessageDetail($params);

        if ($res['code'] != 200)
            return response()->json($this->type(ResponseCode::PARAM_ERROR)->httpResponse('', $res['desc']));

        return response()->json($this->type(ResponseCode::REQUEST_SUCCESS)->httpResponse($res['data']));
    }

    /**
     * 获取系统设置value值
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSystemSettingValue(Request $request)
    {
        $code = $request->code ?? '';

        $res = SettingService::getSystemSettingValue($code);

        return response()->json($this->type(ResponseCode::REQUEST_SUCCESS)->httpResponse($res));
    }

}
