<?php

namespace App\HttpController\App;


use App\Model\Anchors;
use App\Model\Buys;

class AnchorController extends AuthController
{


    public function index()
    {
        try {
            $param = $this->params();
            $param['page'] = $param['page'] ?? 1;
            $param['lid'] = $param['lid'] ?? 0;
            $model = Anchors::create();
            $lists = $model->app($param);
            return $this->writeJson(0, encrypt_data($lists),$param['lid']);
        } catch (\Throwable $e) {
            write_log('Anchor-index:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
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
            $model = Anchors::create();
            $info = $model->info($id);
            if(!$info){
                return $this->writeJson(1);
            }
            //用户是否是月卡及以上
            $info['isvip'] = $this->svip();
            //查看会员是否已经购买
            $info['isbuy'] = true;
            if($info['money'] > 0){
                $buy = Buys::create()->buy($this->userid,'anchor', $id);
                $info['isbuy'] = $buy;
                //如果用户购买过，不是月卡及以上也能观看
                $info['isvip'] = $buy ? $buy : $info['isvip'];
            }
            return $this->writeJson(0, encrypt_data($info));
        } catch (\Throwable $e) {
            write_log('Anchor-info:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }
    }


}
