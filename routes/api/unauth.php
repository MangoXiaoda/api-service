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

// 支付回调
Route::post('wechat/pay_notify','WeChatController@wxPayNotify')->name('api.wechat.pay_notify');

// 获取商品列表
Route::get('goods/list','GoodsController@getGoodsList')->name('api.goods.list');
// 获取商品详情
Route::get('goods/detail','GoodsController@getGoodsDetail')->name('api.goods.detail');
// 新增商品
Route::post('goods/add','GoodsController@addGoods')->name('api.goods.add');
// 编辑商品
Route::post('goods/edit','GoodsController@editGoods')->name('api.goods.edit');
// 删除商品
Route::post('goods/del','GoodsController@delGoods')->name('api.goods.del');
