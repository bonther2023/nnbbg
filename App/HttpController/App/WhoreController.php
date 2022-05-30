<?php

namespace App\HttpController\App;

use App\Model\Buys;
use App\Model\Whores;

class WhoreController extends AuthController
{

    public function index()
    {
        try {
            //推荐20 最新20
            $param = $this->params();
            $good = $param['good'] ?? 0;
            $latest = $param['latest'] ?? 0;
            $model = Whores::create();
            $lists = [];
            if($good){
                $lists = $model->good();
            }
            if($latest){
                $lists = $model->latest();
            }
            return $this->writeJson(0, encrypt_data($lists));
        } catch (\Throwable $e) {
            write_log('Whore-index:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1);
        }
    }

    public function select(){
        try {
            $projects = Whores::PROJECTS;
            $tags = Whores::TAGS;
            $cups = Whores::CUPS;
            return $this->writeJson(0, encrypt_data([
                'projects' => $projects,
                'tags' => $tags,
                'cups' => $cups,
            ]));
        } catch (\Throwable $e) {
            write_log('Whore-select:');
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
            $param['city'] = $param['city'] ?? '';
            $param['project'] = $param['project'] ?? '';
            $param['tag'] = $param['tag'] ?? '';
            $param['cup'] = $param['cup'] ?? '';
            $param['kwd'] = $param['kwd'] ?? '';
            $model = Whores::create();
            $data = $model->app($param);
            return $this->writeJson(0, encrypt_data($data));
        } catch (\Throwable $e) {
            write_log('Whore-list:');
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
            $model = Whores::create();
            $info = $model->info($id);
            if(!$info){
                return $this->writeJson(1);
            }
            //增加浏览量
            $model->increase('view', $id);

            //默认未关注
            $info['follow'] = false;

            //查看会员是否已经购买
            $info['isbuy'] = true;
            if($info['money'] > 0){
                $info['isbuy'] = Buys::create()->buy($this->userid,'whore', $id);
            }
            return $this->writeJson(0, encrypt_data($info));
        } catch (\Throwable $e) {
            write_log('Whore-info:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1);
        }
    }


}
