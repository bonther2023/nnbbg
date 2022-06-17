<?php

namespace App\Process;

use App\Model\Flows;
use App\Model\Queues;
use App\Model\Reports;
use App\Queue\ReportQueue;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\ORM\DbManager;
use EasySwoole\Queue\Job;
use EasySwoole\RedisPool\RedisPool;

class ReportProcess extends AbstractProcess
{
    protected function run($arg)
    {
        go(function (){
            $queue = ReportQueue::getInstance();
            $queue->consumer()->listen(function (Job $job){
                $task = $job->getJobData();
                $data = $task['info'];
                try{
                    // 开启事务
                    DbManager::getInstance()->startTransaction();
                    $cache = RedisPool::defer();
                    //报表记录
                    $key = 'report:'.$data['date'].':'.$data['aid'].'_'.$data['cid'].'_'.$data['hour'];
                    $rid = $cache->get($key);
                    $model = Reports::create();
                    if (!$rid) {
                        $rid = $model->data($data)->save();
                        $cache->set($key, $rid, 86400);
                    }
                    //统计安装量
                    if(isset($task['install']) && $task['install']){
                        // 'system' => $system,//1安卓 2IOS
                        if($data['system'] > 1){
                            $model->increase('install_ios',$rid);
                        }else{
                            $model->increase('install_and',$rid);
                        }
                    }
                    //统计订单量
                    if(isset($task['monad']) && $task['monad']){
                        $model->increase('monad',$rid);
                    }
                    //统计支付量
                    if(isset($task['pay']) && $task['pay']){
                        $model->increase('pay',$rid);
                    }
                    // 提交事务
                    DbManager::getInstance()->commit();
                }catch (\Throwable $e){
                    DbManager::getInstance()->rollback();
                    write_log('ReportProcess');
                    write_log($e->getMessage());
//                    Queues::create()->add('ReportProcess', $task, $e->getMessage());
                }
            });
        });
    }


    protected function reload($data){
        // 获取队列
        $queue = ReportQueue::getInstance();
        // 创建任务
        $job = new Job();
        $job->setJobData($data);
        $queue->producer()->push($job);
    }
}
