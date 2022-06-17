<?php

namespace App\Process;

use App\Model\Canals;
use App\Model\Flows;
use App\Model\Orders;
use App\Model\Queues;
use App\Queue\FlowQueue;
use Carbon\Carbon;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\ORM\DbManager;
use EasySwoole\Queue\Job;

class FlowProcess extends AbstractProcess
{
    protected function run($arg)
    {
        go(function (){
            FlowQueue::getInstance()->consumer()->listen(function (Job $job){
                $task = $job->getJobData();
                $data = $task['info'];
                try{
                    // 开启事务
                    DbManager::getInstance()->startTransaction();
                    // 'system' => $system,//1安卓 2IOS
                    //报表记录
                    $model = Flows::create();
                    $info = $model->where('date',$data['date'])
//                        ->where('aid',$data['aid'])
                        ->where('cid',$data['cid'])->get();
                    if(!$info){
                        $info = [
                            'date' => $data['date'],
                            'aid' => 0,
                            'cid' => $data['cid'],
                            'install_ios_settle' => 0,
                            'install_and_settle' => 0,
                            'install_ios_deduct' => 0,
                            'install_and_deduct' => 0,
                        ];
                        $fid = $model->data($info)->save();
                    }else{
                        $fid = $info['id'];
                    }
                    //订单操作和金额操作一体
                    if(isset($task['order']) && $task['order']){
                        //订单信息
                        $orderModel = Orders::create();
                        $orderInfo = $data;
                        //渠道信息
                        $canalModel = Canals::create();
                        $canalInfo = $canalModel->get($data['cid']);
                        write_log($canalInfo);
                        //计算分成
                        $settle = $this->countSettle($orderInfo,$canalInfo);
                        //更新flow三家分成
                        if($settle['deduct']){
                            //更新订单 settle 为 2
                            $orderModel->update(['settle' => 2, 'status' => 2, 'pay_at' => Carbon::now()],['id' => $data['oid']]);
                            $orderIosSettle = 0;
                            $orderAndSettle = 0;
                            $orderIosDeduct = $data['system'] > 1 ? 1 : 0;
                            $orderAndDeduct = $data['system'] > 1 ? 0 : 1;
                        }else{
                            //更新订单
                            $orderModel->update(['status' => 2, 'pay_at' => Carbon::now()],['id' => $data['oid']]);
                            //更新渠道总订单
                            $canalInfo->update(['num_order' => QueryBuilder::inc()]);
                            $orderIosSettle = $data['system'] > 1 ? 1 : 0;
                            $orderAndSettle = $data['system'] > 1 ? 0 : 1;
                            $orderIosDeduct = 0;
                            $orderAndDeduct = 0;
                        }
                        $model->update([
                            'order_ios_settle' => ["[I]" => "+" . $orderIosSettle],
                            'order_and_settle' => ["[I]" => "+" . $orderAndSettle],
                            'order_ios_deduct' => ["[I]" => "+" . $orderIosDeduct],
                            'order_and_deduct' => ["[I]" => "+" . $orderAndDeduct],
                            'settle_agent' => ["[I]" => "+" . $settle['agent']],
                            'settle_canal' => ["[I]" => "+" . $settle['canal']],
                            'settle_admin' => ["[I]" => "+" . $settle['admin']],
                        ],['id' => $fid]);
                    }

                    //安装操作
                    if(isset($task['install']) && $task['install']){
                        $installFiled = 'install_and';
                        if($data['system'] > 1){
                            $installFiled = 'install_ios';
                        }
                        //每一天设定一个数目为 10 的阈值，小于这个阈值不计算扣量
                        $sill = $info['install_ios_settle'] + $info['install_and_settle'] + $info['install_ios_deduct'] + $info['install_and_deduct'];
                        if($sill <= 10){
                            $installFiled .= '_settle';
                        }else{
                            $deduct = deduction($data['rebate_register']);// true扣量 false结算
                            if($deduct){
                                $installFiled .= '_deduct';
                            }else{
                                $installFiled .= '_settle';
                            }
                        }
                        $model->increase($installFiled,$fid);
                    }
                    // 提交事务
                    DbManager::getInstance()->commit();
                }catch (\Throwable $e){
                    DbManager::getInstance()->rollback();
//                    Queues::create()->add('FlowProcess', $task, $e->getMessage());
                }
            });
        });
    }

    //计算三家分成
    protected function countSettle($orderInfo,$canalInfo){
        write_log($orderInfo); write_log($canalInfo);
        //渠道分成
        $canalSettle = number_format($orderInfo['money'] * $canalInfo['percent_canal'] * 0.01,2);
        //代理分成
        $agentSettle = 0;
        //平台分成
        $adminSettle = $orderInfo['money'] - $agentSettle - $canalSettle;
        //免单内不扣量 num_free  num_order
        $deduct = false;
        if($canalInfo['num_order'] > $canalInfo['num_free']){
            //计算是否扣量
            $rebate = $canalInfo['rebate_'.$orderInfo['mtype']];
            $deduct = deduction($rebate);// true扣量 false结算
            if($deduct){
                //渠道分成
                $canalSettle = 0;
                //代理分成
                $agentSettle = 0;
                //平台分成
                $adminSettle = $orderInfo['money'];
            }
        }
        return [
            'canal' => $canalSettle,
            'agent' => $agentSettle,
            'admin' => $adminSettle,
            'deduct' => $deduct
        ];
    }

    protected function reload($data){
        // 获取队列
        $queue = FlowQueue::getInstance();
        // 创建任务
        $job = new Job();
        $job->setJobData($data);
        $queue->producer()->push($job);
    }
}
