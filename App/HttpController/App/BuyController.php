<?php

namespace App\HttpController\App;

use App\Model\Anchors;
use App\Model\Articles;
use App\Model\Authors;
use App\Model\Buys;
use App\Model\Comics;
use App\Model\Costs;
use App\Model\Novels;
use App\Model\Qovds;
use App\Model\Sounds;
use App\Model\Topics;
use App\Model\Users;
use App\Model\Videos;
use App\Model\Whores;
use Carbon\Carbon;
use EasySwoole\ORM\DbManager;

class BuyController extends AuthController
{


    public function index()
    {
        try {
            $param = $this->params();
            $id = $param['id'] ?? 0;//带章节的都是传上一及ID
            $type = $param['type'] ?? '';
            if(!$id || !$type || !$this->userid){
                return $this->writeJson(1);
            }
            // 开启事务
            DbManager::getInstance()->startTransaction();
            switch ($type){
                case 'qovd':
                    $title = '快播解锁';
                    $model = Qovds::create();
                    break;
                case 'anchor':
                    $title = '主播解锁';
                    $model = Anchors::create();
                    break;
                case 'article':
                    $title = '性闻解锁';
                    $model = Articles::create();
                    break;
                case 'topic':
                    $title = '专题解锁';
                    $model = Topics::create();
                    break;
                case 'whore':
                    $title = '约啪解锁';
                    $model = Whores::create();
                    break;
                case 'sound':
                    $title = '电台解锁';
                    $model = Sounds::create();
                    break;
                case 'novel':
                    $title = '长篇解锁';
                    $model = Novels::create();
                    break;
                case 'comics':
                    $title = '漫画解锁';
                    $model = Comics::create();
                    break;
                default:
                    $title = '未知解锁';
                    $model = Videos::create();
                    break;
            }
            //获取资源信息
            $resource = $model->get($id);
            if(!$resource){
                return $this->writeJson(1);
            }
            //查看用户余额
            $uModel = Users::create();
            $user = $uModel->get($this->userid);
            if($user && $resource['money'] > $user['balance']){
                return $this->writeJson(1, null, 'ERROR');
            }
            if($type == 'qovd') {
                //增加博主销售量
                Authors::create()->increase('sale',$resource['aid']);
            }
            //增加资源销售量
            $model->increase('sale',$resource['id']);
            //增加购买记录
            $buyData = [
                'uid' => $this->userid,
                'rid' => $resource['id'],
                'title' => $resource['title'],
                'thumb' => $resource['thumb'],
                'type' => $type,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            Buys::create()->add($buyData);

            //更新用户余额
            $uModel->decrease('balance',$this->userid,$resource['money']);
            //增加用户消费记录
            Costs::create()->add($this->userid, $resource['money'], $title);
            // 提交事务
            DbManager::getInstance()->commit();
            return $this->writeJson(0);
        } catch (\Throwable $e) {
            DbManager::getInstance()->rollback();
            write_log('Buy-index:');
            write_log($e->getMessage(),4);
            return $this->writeJson(1);
        }
    }

}
