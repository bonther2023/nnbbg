<?php

namespace App\HttpController\App;

use App\Model\Ads;

class AdController extends AuthController
{


    public function index()
    {
        try {
            $param = $this->params();
            $name = $param['name'] ?? 'banner';
            $model = Ads::create();
            $ad = $model->app($name);
            return $this->writeJson(0,  encrypt_data($ad));
        } catch (\Throwable $e) {
            write_log('Ad-index:');
            write_log($e->getMessage(),4);
            return $this->writeJson(1);
        }
    }

}
