<?php

namespace App\HttpController\App;

use App\Model\Buys;
use App\Model\NovelChapters;
use App\Model\Novels;

class NovelController extends AuthController
{

    public function index()
    {
        try {
            $model = Novels::create();
            $good = $model->good();
            $latest = $model->latest();
            return $this->writeJson(0, encrypt_data([
                'good' => $good,
                'latest' => $latest,
            ]));
        } catch (\Throwable $e) {
            write_log('Novel-index:');
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
            $model = Novels::create();
            $data = $model->app($param);
            return $this->writeJson(0, encrypt_data($data));
        } catch (\Throwable $e) {
            write_log('Novel-list:');
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
            $model = Novels::create();
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
                $buy = Buys::create()->buy($this->userid,'novel', $id);
                $info['isbuy'] = $buy;
                //如果用户购买过，不是月卡及以上也能观看
                $info['isvip'] = $buy ? $buy : $info['isvip'];
            }
            //章节
            $chapter = NovelChapters::create()->app($id);
            return $this->writeJson(0,  encrypt_data(['info' => $info, 'chapter' => $chapter]),$id);
        } catch (\Throwable $e) {
            write_log('Novel-chapter:');
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
            $model = NovelChapters::create();
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
            if($info['novel']['money'] > 0){
                $buy = Buys::create()->buy($this->userid,'novel', $info['nid']);
                $info['isbuy'] = $buy;
                //如果用户购买过，不是月卡及以上也能观看
                $info['isvip'] = $buy ? $buy : $info['isvip'];
            }

            //增加浏览量
            Novels::create()->increase('view', $info['nid']);

            $prev = $model->prev($info['nid'], $id);
            $next = $model->next($info['nid'], $id);

            return $this->writeJson(0, encrypt_data([
                'info' => $info,
                'prev' => $prev,
                'next' => $next,
            ]));
        } catch (\Throwable $e) {
            write_log('Novel-info:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1);
        }
    }


}
