<?php

namespace App\HttpController\App;


use App\Model\Medias;
use App\Model\Videos;

class IndexController extends AuthController
{


    public function index()
    {
        try {
            //判断用户VIP等级，返回对应等级的数据
            $uvip = 0;
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
            write_log('Index-index:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }

    }


    public function check(){
        try {
            $data = $this->params();
            $version = $data['version'] ?? '';
            $link = setting('update_url');
            $_version = setting('app_version');
            if($_version != $version){
                return $this->writeJson(0,encrypt_data($link));
            }else{
                return $this->writeJson(1, null, '版本一致');
            }
        } catch (\Throwable $e) {
            write_log('Index-check:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }
    }


}
