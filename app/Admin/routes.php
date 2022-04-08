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

    // 全局配置 -- 签到设置
    $router->resource('setting/sign', 'Setting\SignSettingController');
    // 全局配置 -- 作品设置
    $router->resource('setting/works', 'Setting\WorksSettingController');

    // 用户列表
    $router->resource('user/list', 'User\UserController');

    // 话题列表
    $router->resource('topics/list', 'Works\TopicsController');
    // 作品列表
    $router->resource('works/list', 'Works\WorksController');
    // 评论列表
    $router->resource('comment/list', 'Works\CommentController');

    // banner列表
    $router->resource('banner/list', 'Operation\BannerController');

    // 积分规则说明
    $router->resource('points/rule_desc', 'Points\RulesDesController');
    // 积分日志列表
    $router->resource('points/log', 'Points\LogController');

});
