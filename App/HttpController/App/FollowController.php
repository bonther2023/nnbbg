<?php

namespace App\HttpController\App;


use App\Model\Articles;
use App\Model\AuthorFocus;
use App\Model\Authors;
use App\Model\Comics;
use App\Model\Contes;
use App\Model\Images;
use App\Model\Ladys;
use App\Model\Medias;
use App\Model\News;
use App\Model\Novels;
use App\Model\Qovds;
use App\Model\Sounds;
use App\Model\Topics;
use App\Model\TopicVideos;
use App\Model\Videos;
use App\Model\Whores;

class FollowController extends AuthController
{


    public function index()
    {
        try {
            $param = $this->params();
            $id = $param['id'] ?? 0;//带章节的都是传上一及ID
            $type = $param['type'] ?? '';
            if(!$id || !$type){
                return $this->writeJson(1);
            }
            switch ($type){
                case 'media':
                    $model = Medias::create();
                    break;
                case 'qovd':
                    $model = Qovds::create();
                    break;
                case 'author':
                    $model = Authors::create();
                    //新增用户关注博主数据
                    AuthorFocus::create()->add($id, $this->userid);
                    break;
                case 'lady':
                    $model = Ladys::create();
                    break;
                case 'news':
                    $model = News::create();
                    break;
                case 'article':
                    $model = Articles::create();
                    break;
                case 'topic':
                    $model = Topics::create();
                    break;
                case 'whore':
                    $model = Whores::create();
                    break;
                case 'sound':
                    $model = Sounds::create();
                    break;
                case 'conte':
                    $model = Contes::create();
                    break;
                case 'novel':
                    $model = Novels::create();
                    break;
                case 'image':
                    $model = Images::create();
                    break;
                case 'comics':
                    $model = Comics::create();
                    break;
                default:
                    $model = Videos::create();
                    break;
            }
            //关注量自增
            $result = $model->increase('focus',$id);
            return $this->writeJson(0,$result);
        } catch (\Throwable $e) {
            write_log('Follow-index:');
            write_log($e->getMessage(),4);
            return $this->writeJson(1);
        }
    }

}
