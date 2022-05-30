<?php

namespace App\HttpController\App;

use App\Model\Buys;
use App\Model\ComicsChapters;
use App\Model\Comics;

class ComicsController extends AuthController
{

    public function index()
    {
        try {
            $model = Comics::create();
            $good = $model->good();
            $latest = $model->latest();
            return $this->writeJson(0, encrypt_data([
                'good' => $good,
                'latest' => $latest,
            ]));
        } catch (\Throwable $e) {
            write_log('Comics-index:');
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
            $model = Comics::create();
            $data = $model->app($param);
            return $this->writeJson(0, encrypt_data($data));
        } catch (\Throwable $e) {
            write_log('Comics-list:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1);
        }
    }


    public function chapter(){
        try {
            $param = $this->params();
            $id = $param['id'] ?? 0;
            if (!$id) {
                return $this->writeJson(1);
            }
            $model = Comics::create();
            $info = $model->info($id);
            if(!$info){
                return $this->writeJson(1);
            }
            //增加浏览量
            $model->increase('view', $id);

            //用户是否是月卡及以上
            $info['isvip'] = $this->svip();
            //查看会员是否已经购买
            $info['isbuy'] = true;
            if($info['money'] > 0){
                $buy = Buys::create()->buy($this->userid,'comics', $id);
                $info['isbuy'] = $buy;
                //如果用户购买过，不是月卡及以上也能观看
                $info['isvip'] = $buy ? $buy : $info['isvip'];
            }
            //章节
            $chapter = ComicsChapters::create()->app($id);
            return $this->writeJson(0,  encrypt_data(['info' => $info, 'chapter' => $chapter]));
        } catch (\Throwable $e) {
            write_log('Comics-chapter:');
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
            $model = ComicsChapters::create();
            $info = $model->info($id);
            if(!$info){
                return $this->writeJson(1);
            }
            //默认未关注
            $info['follow'] = false;
            //用户是否是月卡及以上
            $info['isvip'] = $this->svip();
            //查看会员是否已经购买
            $info['isbuy'] = true;
            if($info['comics']['money'] > 0){
                $buy = Buys::create()->buy($this->userid,'comics', $info['cid']);
                $info['isbuy'] = $buy;
                //如果用户购买过，不是月卡及以上也能观看
                $info['isvip'] = $buy ? $buy : $info['isvip'];
            }

            //增加浏览量
            Comics::create()->increase('view', $info['cid']);

            $prev = $model->prev($info['cid'], $id);
            $next = $model->next($info['cid'], $id);

            return $this->writeJson(0, encrypt_data([
                'info' => $info,
                'prev' => $prev,
                'next' => $next,
            ]));
        } catch (\Throwable $e) {
            write_log('Comics-info:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1);
        }
    }


}
