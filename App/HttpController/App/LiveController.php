<?php

namespace App\HttpController\App;


use App\HttpController\BaseController;
use App\Model\Lives;

class LiveController extends BaseController
{


    public function index()
    {
        try {
            $model = Lives::create();
            $lists = $model->app();
            return $this->writeJson(0, encrypt_data($lists));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }
    }


}
