<?php

namespace App\HttpController\App;


use App\HttpController\BaseController;
use App\Model\Ladys;

class LadyController extends AuthController
{

    public function index(){
        try {
            $param = $this->params();
            $param['page'] = $param['page'] ?? 1;
            $model = Ladys::create();
            $data = $model->app($param);
            return $this->writeJson(0, encrypt_data($data));
        } catch (\Throwable $e) {
            write_log('Lady-index:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1);
        }
    }

    public function info(){
        try {
            $param = $this->params();
            $id = $param['id'] ?? 0;
            if(!$id){
                return $this->writeJson(1);
            }
            $model = Ladys::create();
            $info = $model->info($id);
            if(!$info){
                return $this->writeJson(1);
            }
            //增加浏览量
            $model->increase('view',$id);
            //默认未关注
            $info['follow'] = false;
            return $this->writeJson(0, encrypt_data($info));
        } catch (\Throwable $e) {
            write_log('Lady-info:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1);
        }
    }


}
