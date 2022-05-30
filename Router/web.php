<?php

use \App\HttpController\Router;


Router::group(['namespace' => 'Home', 'prefix' => ''], function () {
    Router::get('', 'IndexController/index');
    Router::get('list', 'IndexController/list');
});






















