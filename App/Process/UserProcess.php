<?php

namespace App\Process;

use App\Model\Costs;
use App\Model\Queues;
use App\Model\Users;
use App\Queue\UserQueue;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\Queue\Job;
use ZxInc\Zxipdb\IPTool;
use EasySwoole\ORM\DbManager;

class UserProcess extends AbstractProcess
{
    protected function run($arg)
    {
        go(function (){
            $queue = UserQueue::getInstance();
            $queue->consumer()->listen(function (Job $job){
                $task = $job->getJobData();
                $data = $task['info'];
                try{
                    // 开启事务
                    DbManager::getInstance()->startTransaction();
                    //更新VIP信息
                    if(isset($task['vip']) && $task['vip']){
                        $model = Users::create();
                        $user = $model->get($data['uid']);
                        $vipAt = $this->vipExpired($data['mtype'],$user['vip_at']);
                        $vipRank = $this->vipRank($user['money'],$data['money']);
                        $user->update(['vip' => $data['mtype'],
                            'balance' => QueryBuilder::inc($data['diamond']),
                            'vip_at' => $vipAt,
                            'vip_rank' => $vipRank,
                            'money' => QueryBuilder::inc($data['money'])]);
                        // 增加钻石消费记录
                        Costs::create()->add($data['uid'], $data['diamond'], 'VIP购买', 1);
                    }
                    //更新钻石信息
                    if(isset($task['diamond']) && $task['diamond']){
                        $model = Users::create();
                        $user = $model->get($data['uid']);
                        $vipRank = $this->vipRank($user['money'],$data['money']);
                        $user->update(['balance' => QueryBuilder::inc($data['diamond']), 'vip_rank' => $vipRank, 'money' => QueryBuilder::inc($data['money'])]);
                        // 增加钻石消费记录
                        Costs::create()->add($data['uid'], $data['diamond'], '钻石充值', 1);
                    }
                    //更新登录信息
                    if(isset($task['login']) && $task['login']){
                        $ipInfo = IPTool::query($data['ip']);
                        $model = Users::create();
                        $user = $model->get($data['uid']);
                        $user->update([
                            'address' => $ipInfo['disp'],
                            'ip' => $data['ip'],
                            'app_release'=> $data['release'],
                            'app_version'=> $data['version'],
                            'app_vendor' => $data['vendor'],
                            'app_model'  => $data['model'],
                            'app_network'=> $data['network'],
                            'app_system' => $data['system'],
                            'login_at' => $data['time']
                        ]);
                    }
                    //更新登录信息
                    if(isset($task['register']) && $task['register']){
                        $model = Users::create();
                        //生成唯一邀请码
                        $card = $this->inviteCode();
                        $user = $model->get($data['uid']);
                        $ipInfo = IPTool::query($data['ip']);
                        $user->update([
                            'address' => $ipInfo['disp'],
                            'ip' => $data['ip'],
                            'card' => $card,
                            'app_release'=> $data['release'],
                            'app_version'=> $data['version'],
                            'app_vendor' => $data['vendor'],
                            'app_model'  => $data['model'],
                            'app_network'=> $data['network'],
                            'app_system' => $data['system'],
                            'login_at' => $data['time']
                        ]);
                    }
                    // 提交事务
                    DbManager::getInstance()->commit();
                }catch (\Throwable $e){
                    DbManager::getInstance()->rollback();
                    Queues::create()->add('UserProcess', $task, $e->getMessage());
                }
            });
        });
    }

    protected function inviteCode()
    {
        $code = invite();
        //去重
        $model = Users::create();
        $info = $model->field('card')->where('card',$code)->get();
        if($info){
            write_log($info);
            return $this->inviteCode();
        }
        return $code;
    }

    protected function vipRank($balance,$money){
        $total = $balance + $money;
        $setting = setting();
        $rank = 0;
        if($total >= $setting['vip1_price']){
            $rank = 1;
        }
        if($total >= $setting['vip2_price']){
            $rank = 2;
        }
        if($total >= $setting['vip3_price']){
            $rank = 3;
        }
        if($total >= $setting['vip4_price']){
            $rank = 4;
        }
        if($total >= $setting['vip5_price']){
            $rank = 5;
        }
        if($total >= $setting['vip6_price']){
            $rank = 6;
        }
        if($total >= $setting['vip7_price']){
            $rank = 7;
        }
        return $rank;
    }

    protected function vipExpired($mtype, $old){
        $old = (int)$old ?: time();
        switch ($mtype) {
            case 'month_vip':
                $time = $old + 30 * 86400;
                break;
            case 'quarter_vip':
                $time = $old + 120 * 86400;
                break;
            case 'year_vip':
                $time = $old + 365 * 86400;
                break;
            case 'forever_vip':
                $time = $old + 3 * 365 * 86400;
                break;
            default:
                $time = $old + 86400;
                break;
        }
        return $time;
    }


    protected function reload($data){
        // 获取队列
        $queue = UserQueue::getInstance();
        // 创建任务
        $job = new Job();
        $job->setJobData($data);
        $queue->producer()->push($job);
    }
}
