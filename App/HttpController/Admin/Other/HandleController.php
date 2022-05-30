<?php

namespace App\HttpController\Admin\Other;

use App\HttpController\Admin\Auth\AuthController;
use App\Model\Categorys;
use App\Model\Images;
use App\Model\Medias;
use App\Model\Videos;
use Carbon\Carbon;
use EasySwoole\HttpClient\HttpClient;
use EasySwoole\Mysqli\QueryBuilder;

class HandleController extends AuthController
{

    public function target()
    {
        try {

            $data = $this->getParams();
            $model = $this->getVideoModel($data['source']);
            $res = $model->func(function ($builder) use($data){
                return $builder->raw("UPDATE `video_".$data['source']."s` SET `target` = replace(`target`, '".$data['url_old']."', '".$data['url_new']."')");
            });
            if($res){
                return $this->writeJson(0, $res, '批量操作成功');
            }else{
                return $this->writeJson(1, $res, '批量操作失败');
            }
        } catch (\Throwable $e) {
            write_log($e->getMessage());
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

    public function thumb(){
        try {

            $data = $this->getParams();
            $model = $this->getVideoModel($data['source']);
            $res = $model->func(function ($builder) use($data){
                return $builder->raw("UPDATE `video_".$data['source']."s` SET `thumb` = replace(`thumb`, '".$data['url_old']."', '".$data['url_new']."')");
            });
            if($res){
                return $this->writeJson(0, $res, '批量操作成功');
            }else{
                return $this->writeJson(1, $res, '批量操作失败');
            }
        } catch (\Throwable $e) {
            write_log($e->getMessage());
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

    public function image(){
        try {
            $data = $this->getParams();
            $model = Images::create();
            $res = $model->func(function ($builder) use($data){
                $builder->raw("UPDATE `images` SET `content` = replace(`content`, '".$data['url_old']."', '".$data['url_new']."')");
            });
            if($res){
                return $this->writeJson(0, $res, '批量操作成功');
            }else{
                return $this->writeJson(1, $res, '批量操作失败');
            }
        } catch (\Throwable $e) {
            write_log($e->getMessage());
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

    public function video(){
        try {
            $param = $this->params();
            $page = $param['page'] ?? 1;
            $request = new HttpClient('https://shayuapi.com/api.php/provide/vod/at/json/?ac=detail&pg='.$page.'&h=24');
            //设置等待超时时间
            $request->setTimeout(120);
            //设置连接超时时间
            $request->setConnectTimeout(120);
            $response = $request->get();
            $body = $response->getBody();
            $result = unjson($body);
            return $this->writeJson(0, encrypt_data($result));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }

    }

    public function update(){
        try {
            $data = $this->params();
            $type = $data['type'] ?? '';
            unset($data['type']);
            $model = Videos::create();
            if($type == 'media'){
                $model = Medias::create();
            }
            $data['created_at'] = Carbon::now();
            $model->data($data)->save();
            return $this->writeJson(0);
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

}
