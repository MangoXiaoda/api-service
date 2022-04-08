<?php

namespace App\Http\Controllers\Api;

use App\Enums\Http\ResponseCode;
use App\Handlers\WechatConfigHandler;
use App\Http\Requests\Api\AuthRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api', ['except' => ['weappLogin']]);
    }

    /**
     * 小程序发起登录
     * @bodyParam code string required 微信 code
     * @param AuthRequest $request
     * @return JsonResponse
     */
    public function weappLogin(AuthRequest $request)
    {
        $code = $request->code;

        try {
            // 根据 code 获取微信 openid、unionid 和 session key
            // $miniProgram = Factory::miniProgram(config('wechat.mini_program'));
            $miniProgram = (new WechatConfigHandler())->miniProgram();
            $data = $miniProgram->auth->session($code);

            // 结果有误则表示code过期和无效
            if (isset($data['errcode'])) {
                return response()->json($this->type(ResponseCode::DATA_ERROR)->httpResponse($request->code, 'code 无效'));
            }

            // 找到openid对应用户
            $user = User::whereWeappOpenid($data['openid'])->first();
            $attributes['wx_session_key'] = $data['session_key'];
            $attributes['wx_unionid'] = $data['wx_unionid'] ?? '';

            // 未找到用户则绑定用户
            if (!$user) {
                $attributes['weapp_openid'] = $data['openid'];
                $user = User::query()->create($attributes);
            } else {

                // 更新用户数据
                $user->update($attributes);
            }

            $token = auth('api')->login($user);

            // 为用户创建token
            return $this->respondWithToken($token);
        } catch (\Exception $e) {
            return response()->json($this->type(ResponseCode::REQUEST_FAILS)->httpResponse($request->code,$e->getMessage()));
        }
    }

    /**
     * 获取登录用户信息
     * @return JsonResponse
     */
    public function me()
    {
        $user = auth('api')->user();
        return response()->json($this->type(ResponseCode::REQUEST_SUCCESS)->httpResponse($user));
    }

    /**
     * 退出登录
     * @return JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();
        return response()->json($this->type(ResponseCode::REQUEST_SUCCESS)->httpResponse('', '退出成功'));
    }

    /**
     * 刷新 jwt token
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * 返回token信息
     * @param $token
     * @return JsonResponse
     */
    public function respondWithToken($token)
    {
        return response()->json($this->type(ResponseCode::REQUEST_SUCCESS)->httpResponse([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'guard' => 'api',
            'expires_in' => auth('api')->factory()->getTTL()
        ]));
    }

}
