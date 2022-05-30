<?php

namespace App\HttpController\App;

use App\Model\Buys;
use App\Model\Articles;
use App\Model\Topics;
use App\Model\TopicVideos;

class TopicController extends AuthController
{


    public function index()
    {
        try {
            $param = $this->params();
            $param['page'] = $param['page'] ?? 1;
            $model = Topics::create();
            $data = $model->app($param);
            return $this->writeJson(0,  encrypt_data($data));
        } catch (\Throwable $e) {
            write_log('Topic-index:');
            write_log($e->getMessage(),4);
            return $this->writeJson(1);
        }
    }


    public function info()
    {
        try {
            $param = $this->params();
            $id = $param['id'] ?? 0;
            if(!$id){
                return $this->writeJson(1);
            }
            $model = Topics::create();
            $info = $model->info($id);
            if(!$info){
                return $this->writeJson(1);
            }
            //增加浏览量
            $model->increase('view',$id);
            //默认未关注
            $info['follow'] = false;
            //查看会员是否已经购买,没有购买，前台阴影解锁全部章节
            $info['isbuy'] = true;
            if($info['money'] > 0){
                $info['isbuy'] = Buys::create()->buy($this->userid,'topic', $id);
            }

            //查该专题所有视频
            $vmodel = TopicVideos::create();
            $data = $vmodel->app($id);
            return $this->writeJson(0,  encrypt_data(['info' => $info, 'chapter' => $data]));
        } catch (\Throwable $e) {
            write_log('Topic-info:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }
    }

}
