<?php

namespace App\HttpController\App;


use App\Model\Medias;
use App\Model\Videos;

class IndexController extends AuthController
{


    public function index()
    {
        try {
            // TODO 这里新增一个专题推荐，未做
            //普通视频、媒体视频、快播视频 推荐
            $video = Videos::create();
            $media = Medias::create();
            $vGood = $video->good(14);
            $vGood = array_chunk($vGood, 7);

            $vMedia = $media->good(14);
            $vMedia = array_chunk($vMedia, 7);

            $good = array_merge($vMedia, $vGood);
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
