<?php

namespace App\HttpController\App;

use App\Model\Qovds;
use App\Model\Videos;

class VideoController extends AuthController
{

    public function index()
    {
        try {
            $param = $this->params();
            $param['page'] = $param['page'] ?? 1;
            $param['cid'] = $param['cid'] ?? 0;
            $param['kwd'] = $param['kwd'] ??  '';
            $model = Videos::create();
            $data = $model->app($param);
            return $this->writeJson(0, encrypt_data($data));
        } catch (\Throwable $e) {
            write_log('Video-index:');
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
            $model = Videos::create();
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
            write_log('Video-info:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1);
        }
    }


}
