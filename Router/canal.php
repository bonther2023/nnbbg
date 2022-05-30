<?php

use \App\HttpController\Router;

Router::group(['namespace' => 'Canal', 'prefix' => 'canal'], function () {
    Router::post('authorize', 'LoginController/authorize');//授权登录
    Router::post('login', 'LoginController/login');//登录

    Router::get('user', 'IndexController/user');//基本信息


    Router::get('main', 'IndexController/index');//首页

    Router::post('update', 'IndexController/update');//修改基本信息
    Router::post('password', 'IndexController/password');//修改密码
    Router::get('flow', 'IndexController/flow');//效果报表
    Router::get('trade', 'IndexController/trade');//效果报表
    Router::get('sort', 'IndexController/sort');//生成推广链接
});

