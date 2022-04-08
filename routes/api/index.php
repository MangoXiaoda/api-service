<?php
/*
 * @Description: 微信相关路由组
 * @Author: LiZhongDa
 * @Date: 2022/3/15 上午10:07
 * @LastEditors: LiZhongDa
 * @LastEditTime: 2022/3/15 上午10:07
 */

Route::group(['namespace' => 'Api'], function () {

    /**
     * 通用节流路由组
     */
    Route::middleware('throttle:' . config('wechat.rate_limits.normal'))->group(function () {
        /**
         * 无需登录路由组
         */
        require_once base_path('routes/api/unauth.php');
        /**
         * 需登录的路由组
         */
        require_once base_path('routes/api/auth.php');
    });

    /**
     * 严格节流路由组
     */
    Route::middleware('throttle:' . config('wechat.rate_limits.strict'))->group(function () {

    });
});
