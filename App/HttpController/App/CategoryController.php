<?php

namespace App\HttpController\App;


use App\HttpController\BaseController;
use App\Model\Categorys;
use Carbon\Carbon;

class CategoryController extends AuthController
{
    //类目
    public function index()
    {
        try {
            $param = $this->params();
            $model = Categorys::create();
            $param['type'] = $param['type'] ?? 0;
            $lists = $model->app($param['type']);
            return $this->writeJson(0, encrypt_data($lists));
        } catch (\Throwable $e) {
            write_log('Category-index:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1);
        }
    }

}
