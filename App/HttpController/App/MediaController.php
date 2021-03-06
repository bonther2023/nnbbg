<?php

namespace App\HttpController\App;


use App\Model\Medias;
use App\Model\Qovds;

class MediaController extends AuthController
{

    public function index()
    {
        try {
            $param = $this->params();
            $param['page'] = $param['page'] ?? 1;
            $param['cid'] = $param['cid'] ?? 0;
            $param['kwd'] = $param['kwd'] ?? '';
            $model = Medias::create();
            $data = $model->app($param);
            return $this->writeJson(0, encrypt_data($data));
        } catch (\Throwable $e) {
            write_log('Media-index:');
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
            $model = Medias::create();
            $info = $model->info($id);
            if(!$info){
                return $this->writeJson(1);
            }
            //增加浏览量
            $model->increase('view', $id);
            //会员VIP
            $info['isvip'] = $this->vip();
            //默认未关注
            $info['follow'] = false;
            //快播推荐20
            $qovd = Qovds::create()->good(20);
            //视频推荐20
            $good = $model->good(20);
            //随机读取20
            $love = $model->love(20);
            return $this->writeJson(0, encrypt_data([
                'info' => $info,
                'qovd' => $qovd,
                'good' => $good,
                'love' => $love
            ]));
        } catch (\Throwable $e) {
            write_log('Media-info:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1);
        }
    }

    //最新
    public function latest()
    {
        try {
            $param = $this->params();
            $param['page'] = $param['page'] ?? 1;
            $param['new'] = 1;
            $model = Medias::create();
            $data = $model->app($param);
            return $this->writeJson(0, encrypt_data($data));
        } catch (\Throwable $e) {
            write_log('Media-latest:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1);
        }

    }


}
