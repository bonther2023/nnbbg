<?php

use \App\HttpController\Router;

Router::group(['namespace' => 'Agent', 'prefix' => 'agent'], function () {
    //登录
    Router::post('login', 'LoginController/login');//登录
    Router::post('authorize', 'LoginController/authorize');//授权登录

    Router::get('main', 'IndexController/index');//首页
    Router::get('user', 'IndexController/user');//基本信息
    Router::get('flow', 'IndexController/flow');//效果报表
    Router::get('trade', 'IndexController/trade');//我的结算
    Router::post('update', 'IndexController/update');//修改基本信息
    Router::post('password', 'IndexController/password');//修改密码

    //渠道
    Router::group(['prefix' => 'canal'], function () {
        Router::get('', 'CanalController/index');//代理下渠道列表
        Router::get('info', 'CanalController/info');//代理下渠道详情
        Router::post('update', 'CanalController/update');//更新
    });
});
