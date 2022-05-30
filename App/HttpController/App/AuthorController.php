<?php

namespace App\HttpController\App;

use App\HttpController\BaseController;
use App\Model\AuthorFocus;
use App\Model\Authors;
use App\Model\Qovds;

class AuthorController extends AuthController
{


    public function index()
    {
        try {
            $model = Authors::create();
            $saleParam['sale'] = 1;
            $sale = $model->app($saleParam);
            $hotParam['hot'] = 1;
            $hot = $model->app($hotParam);
            $all = $model->app();
            return $this->writeJson(0,  encrypt_data([
                'sale' => $sale,
                'hot' => $hot,
                'all' => $all,
            ]));
        } catch (\Throwable $e) {
            write_log('Author-index:');
            write_log($e->getMessage(),4);
            return $this->writeJson(1);
        }
    }


    public function savor(){
        try {
            $model = AuthorFocus::create();
            //查看用户关注的博主
            $focus = $model->where('uid', $this->userid)->all();
            $aids = [];
            if($focus){
                foreach ($focus as $f){
                    $aids[] = $f['aid'];
                }
            }
            $param['aids'] = $aids;
            //查看用户没关注的博主
            $amodel = Authors::create();
            $param['savor'] = 1;
            $data = $amodel->app($param);
            return $this->writeJson(0,  encrypt_data($data));
        } catch (\Throwable $e) {
            write_log('Author-savor:');
            write_log($e->getMessage(),4);
            return $this->writeJson(1);
        }
    }



}
