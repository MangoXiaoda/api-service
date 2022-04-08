<?php


namespace App\Traits;


trait UserTrait
{
    /**
     * 登录用户
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    protected function user()
    {
        return auth('api')->user();
    }

    /**
     * 登录用户信息
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    protected function userInfo()
    {
        $user = $this->user();
        $user->getPermissionsViaRoles();
        return $user;
    }
}
