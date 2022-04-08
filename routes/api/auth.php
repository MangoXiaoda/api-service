<?php
/*
 * @Description: 需鉴权路由组
 * @Author: LiZhongDa
 * @Date: 2022/3/15 上午11:40
 * @LastEditors: LiZhongDa
 * @LastEditTime: 2022/3/15 上午11:40
 */

Route::group(['middleware' => 'auth:api'],function (){
    // 获取个人信息
    Route::get('auth/me','AuthController@me')->name('api.auth.me');
    // 刷新token
    Route::put('auth/refresh','AuthController@refresh')->name('api.auth.refresh');
    // 退出登录
    Route::delete('auth/logout','AuthController@logout')->name('api.auth.logout');

    // 轮播图
    Route::get('index/banner_list','BannerController@getBannerList')->name('api.index.banner_list');

    // 获取用户个人信息
    Route::get('me/info','MeController@getUserInfo')->name('api.me.info');
    // 编辑用户个人信息
    Route::post('me/edit','MeController@editUserInfo')->name('api.me.edit');
    // 获取用户消息列表
    Route::get('me/message_list','MeController@getMessageList')->name('api.me.message_list');
    // 获取用户消息详情
    Route::get('me/message_detail','MeController@getMessageDetail')->name('api.me.message_detail');

    // 获取系统设置value值
    Route::get('system/get_value','MeController@getSystemSettingValue')->name('api.system.get_value');

    // 通用上传资源接口
    Route::post('upload','UploadController@uploadFile')->name('api.upload');

});
