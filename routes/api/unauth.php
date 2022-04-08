<?php
/*
 * @Description: 无需鉴权路由组
 * @Version:
 * @Author: LiZhongDa
 * @Date: 2022/3/15 上午10:07
 * @LastEditors: LiZhongDa
 * @LastEditTime: 2022/3/15 上午10:07
 */

// 发起登陆
Route::post('auth/login','AuthController@weappLogin')->name('api.auth.login');

//Route::post('upload','UploadController@uploadFile')->name('api.upload');

// Dcat_Admin 要求的话题分类数据接口路由
Route::get('topics/dcat_category_list','WorksController@getDcatAdminTopicsCategoryList')->name('api.topics.dcat_category_list');
