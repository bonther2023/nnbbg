<?php

namespace App\HttpController\App;

use App\Model\AuthorFocus;
use App\Model\Buys;
use App\Model\Qovds;
use App\Model\Videos;

class QovdController extends AuthController
{

    public function index(){
        try {
            //判断用户VIP等级，返回对应等级的数据
            $uvip = 0;
            $uvip++;
            $model = Videos::create();
            switch ($uvip){
                case 1:
                    //视频推荐20
                    $good = $model->good(20);
                    break;
                case 2:
                    //视频推荐20
                    $good = $model->good(20);
                    break;
                case 3:
                    $good = [];
                    break;
                case 4:
                    $good = [];
                    break;
                case 5:
                    $good = [];
                    break;
                case 6:
                    $good = [];
                    break;
                case 7:
                    $good = [];
                    break;
                case 8:
                    $good = [];
                    break;
                case 9:
                    $good = [];
                    break;
                default:
                    //视频推荐20
                    $good = $model->good(20);
                    break;
            }

            return $this->writeJson(0, encrypt_data($good));
        } catch (\Throwable $e) {
            write_log('Qovd-index:');
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
            $model = Qovds::create();
            $info = $model->info($id);
            if(!$info){
                return $this->writeJson(1);
            }
            //增加浏览量
            $model->increase('view',$id);
            //默认未关注
            $info['follow'] = false;
            //查看会员是否已经购买
            $info['isbuy'] = true;
            if($info['money'] > 0){
                $info['isbuy'] = Buys::create()->buy($this->userid,'qovd', $id);
            }
            //查看是否关注过视频的博主
            $info['author_follow'] = AuthorFocus::create()->focus($info['author']['id'], $this->userid);
            return $this->writeJson(0, encrypt_data($info));
        } catch (\Throwable $e) {
            write_log('Qovd-info:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1);
        }
    }

    //优选
    public function quality()
    {
        try {
            $param = $this->params();
            $param['page'] = $param['page'] ?? 1;
            $param['status'] = 2;
            $param['hot'] = 1;
            $model = Qovds::create();
            $data = $model->app($param);
            return $this->writeJson(0, encrypt_data($data));
        } catch (\Throwable $e) {
            write_log('Qovd-quality:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1);
        }

    }



    //热门
    public function hot()
    {
        try {
            $param = $this->params();
            $param['page'] = $param['page'] ?? 1;
            $param['hot'] = 1;
            $model = Qovds::create();
            $data = $model->app($param);
            return $this->writeJson(0, encrypt_data($data));
        } catch (\Throwable $e) {
            write_log('Qovd-hot:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1);
        }
    }


    //博主视频
    public function author(){
        try {
            $param = $this->params();
            $param['page'] = $param['page'] ?? 1;
            $param['aid'] = $param['aid'] ?? 0;
            if(!$param['aid']){
                return $this->writeJson(1);
            }
            $model = Qovds::create();
            $data = $model->app($param);
            return $this->writeJson(0, encrypt_data($data));
        } catch (\Throwable $e) {
            write_log('Qovd-author:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1);
        }
    }

    //博主视频
    public function focus(){
        try {
            $param = $this->params();
            $param['page'] = $param['page'] ?? 1;
            //查看用户关注的所有博主
            $focus = AuthorFocus::create()->where('uid', $this->userid)->all();
            $aids = [];
            if($focus){
                foreach ($focus as $f){
                    $aids[] = $f['aid'];
                }
            }else{
                $aids = [0];
            }
            $param['aids'] = $aids;
            $model = Qovds::create();
            $data = $model->app($param);
            return $this->writeJson(0, encrypt_data($data));
        } catch (\Throwable $e) {
            write_log('Qovd-focus:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1);
        }
    }


}
