<?php

namespace App\Crontab;

use App\Model\Flows;
use App\Queue\SettleQueue;
use Carbon\Carbon;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\EasySwoole\Crontab\AbstractCronTask;
use EasySwoole\Queue\Job;


class Settlement extends AbstractCronTask
{
//    *    *    *    *    *
//    -    -    -    -    -
//    |    |    |    |    |
//    |    |    |    |    |
//    |    |    |    |    +----- day of week (0 - 7) (Sunday=0 or 7)
//    |    |    |    +---------- month (1 - 12)
//    |    |    +--------------- day of month (1 - 31)
//    |    +-------------------- hour (0 - 23)
//    +------------------------- min (0 - 59)
    public static function getRule(): string
    {
        return '10 0 * * *';//每天凌晨12点过10分执行一次
    }

    public static function getTaskName(): string
    {
        // 定时任务名称
        return 'Settlement';
    }

    function run(int $taskId, int $workerIndex)
    {
        TaskManager::getInstance()->async(function (){
            $time = Carbon::now();
            $subDate = $time->subDay()->toDateString();
            $model = Flows::create();
            $flows = $model->where('date',$subDate)->where('settle_canal',0, '>')->all();
            $agents = [];
            foreach ($flows as $flow){
                $agents[$flow['aid']][] = $flow['settle_agent'];
                //生成渠道结算队列
                $this->queue([
                    'info' => [
                        'uid' => $flow['cid'],
                        'money' => $flow['settle_canal'],
                        'date' => $subDate,
                    ],
                    'canal' => true,
                ]);
            }
            //特殊处理下代理结算
            foreach ($agents as $aid => $money){
                $total = array_sum($money);
            }
        });
    }

    protected function queue($data){
        // 获取队列
        $queue = SettleQueue::getInstance();
        // 创建任务
        $job = new Job();
        $job->setJobData($data);
        $queue->producer()->push($job);
    }

    function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        write_log($throwable->getMessage());
    }
}
