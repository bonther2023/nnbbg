<?php

namespace App\Crontab;

use App\Model\Bais;
use App\Model\Costs;
use App\Model\Users;
use Carbon\Carbon;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\EasySwoole\Crontab\AbstractCronTask;
use EasySwoole\Mysqli\QueryBuilder;


class WhoreDraw extends AbstractCronTask
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
        return '0 6 1 * *';//每月1号6点执行一次
    }

    public static function getTaskName(): string
    {
        // 定时任务名称
        return 'WhoreDraw';
    }

    function run(int $taskId, int $workerIndex)
    {
        TaskManager::getInstance()->async(function (){
            $time = Carbon::now();
            $date = $time->subMonth()->format('Y-m');
            $model = Bais::create();
            $all = $model->where('date',$date)->all();
            if(count($all) >= 5){
                $keys = array_rand($all,5);
                foreach ($keys as $key){
                    $info = $all[$key];
                    //更新中奖状态
                    $model->update(['status' => 2],['id' => $info['id']]);
                    $userModel = Users::create();
                    $user = $userModel->get($info['uid']);
                    if($user){
                        //更新用户钻石
                        $user->update(['balance' => QueryBuilder::inc(1000)]);
                        //增加消费记录
                        Costs::create()->add($info['uid'], 1000, '白嫖中奖', 1);
                    }
                }
            }
        });
    }

    function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        write_log($throwable->getMessage());
    }
}
