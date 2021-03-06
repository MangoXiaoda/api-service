<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Dcat\Admin\Admin;

Admin::routes();

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    // 用户列表
    $router->resource('user/list', 'User\UserController');

    // 商品列表
    $router->resource('goods/list', 'Product\GoodsController');
    // 商品分类列表
    $router->resource('goods/category_list', 'Product\GoodsCategoryController');

});
