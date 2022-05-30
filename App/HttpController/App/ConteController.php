<?php

namespace App\HttpController\App;

use App\Model\Buys;
use App\Model\Contes;

class ConteController extends AuthController
{

    public function index()
    {
        try {
            $model = Contes::create();
            $good = $model->good();
            $latest = $model->latest();
            return $this->writeJson(0, encrypt_data([
                'good' => $good,
                'latest' => $latest,
            ]));
        } catch (\Throwable $e) {
            write_log('Conte-index:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1);
        }
    }


    public function list()
    {
        try {
            $param = $this->params();
            $param['page'] = $param['page'] ?? 1;
            $param['cid'] = $param['cid'] ?? 0;
            $param['kwd'] = $param['kwd'] ?? '';
            $model = Contes::create();
            $data = $model->app($param);
            return $this->writeJson(0, encrypt_data($data));
        } catch (\Throwable $e) {
            write_log('Conte-list:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1);
        }
    }


    public function info()
    {
        try {
            $param = $this->params();
            $id = $param['id'] ?? 0;
            if (!$id) {
                return $this->writeJson(1);
            }
            $model = Contes::create();
            $info = $model->info($id);
            if(!$info){
                return $this->writeJson(1);
            }
            //默认未关注
            $info['follow'] = false;
            //用户是否是月卡及以上
            $info['isvip'] = $this->svip();

            //增加浏览量
            $model->increase('view', $id);

            return $this->writeJson(0, encrypt_data($info));
        } catch (\Throwable $e) {
            write_log('Conte-info:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1);
        }
    }


}
