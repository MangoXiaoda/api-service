<?php
/*
 * @Description: 作品权限验证 中间件
 * @Author: lizhongda
 * @Date: 2022/3/24 上午10:48
 */

namespace App\Http\Middleware;

use App\Enums\Http\ResponseCode;
use App\Services\UserService;
use App\Traits\HttpResponseTrait;
use Closure;
use Illuminate\Http\Request;

class EnsureWorksIsValid
{
    use HttpResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $works_id = $request->works_id ?? 0; // 作品id
        $user_id  = auth()->user()->id ?? 0; // 当前登录用户id

        // 检测作品权限
        $check = UserService::hasWorksPermission($works_id, $user_id);
        if (!$check)
            return response($this->type(ResponseCode::REQUEST_WITHOUT_WORKS)->httpResponse());

        return $next($request);
    }
}
