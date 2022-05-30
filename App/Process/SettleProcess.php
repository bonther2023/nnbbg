<?php

namespace App\Process;

use App\Model\Agents;
use App\Model\Canals;
use App\Model\Queues;
use App\Model\Trades;
use App\Queue\SettleQueue;
use Carbon\Carbon;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\ORM\DbManager;
use EasySwoole\Queue\Job;

class SettleProcess extends AbstractProcess
{
    protected function run($arg)
    {
        go(function (){
            $queue = SettleQueue::getInstance();
            $queue->consumer()->listen(function (Job $job){
                $task = $job->getJobData();
                $data = $task['info'];
                try{
                    // 开启事务
                    DbManager::getInstance()->startTransaction();
                    $trade = setting('trade_money');
                    if(isset($task['canal']) && $task['canal']){
                        //渠道结算数据
                        $model = Canals::create();
                        $role = 1;
                    }
                    if(isset($task['agent']) && $task['agent']){
                        //代理结算数据
                        $model = Agents::create();
                        $role = 2;
                    }
                    $user = $model->get($data['uid']);
                    //银行信息是否填写，没填写不结算，保存到余额
                    if(!$user['card'] && !$user['address']){
                        $user->update(['balance' => QueryBuilder::inc($data['money'])]);
                    }else{
                        $total = $user['balance'] + $data['money'];
                        if($total < $trade){
                            //保存到余额，不结算
                            $user->update(['balance' => $total]);
                        }else{
                            $bank = $user['bank'] ? $user['bank'] : $user['type'];
                            $card = $user['card'] ? $user['card'] : $user['address'];
                            //生成结算记录
                            Trades::create()->data([
                                'date' => $data['date'],
                                'userid' => $data['uid'],
                                'username' => $user['username'],
                                'role' => $role,
                                'money' => $total,
                                'status' => 1,
                                'name' => $user['name'],
                                'bank' => $bank,
                                'card' => $card,
                                'created_at' => Carbon::now(),
                            ])->save();
                            //清0用户余额
                            $user->update(['balance' => 0]);
                        }
                    }
                    // 提交事务
                    DbManager::getInstance()->commit();
                }catch (\Throwable $e){
                    DbManager::getInstance()->rollback();
                    Queues::create()->add('SettleProcess', $task, $e->getMessage());
                }
            });
        });
    }
}
