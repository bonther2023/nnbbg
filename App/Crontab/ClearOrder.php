<?php

namespace App\Crontab;

use App\Model\Orders;
use App\Model\Trades;
use Carbon\Carbon;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\EasySwoole\Crontab\AbstractCronTask;


class ClearOrder extends AbstractCronTask
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
        return '0 0 * * *';//每天凌晨12点执行一次
    }

    public static function getTaskName(): string
    {
        // 定时任务名称
        return 'ClearOrder';
    }

    function run(int $taskId, int $workerIndex)
    {
        TaskManager::getInstance()->async(function (){
            //删除7天之前所有未支付的订单数据
            Orders::create()->where('status',Orders::STATUS_1)
                ->where('created_at',Carbon::now()->subDays(7)->toDateString(),'<')
                ->destroy(null,true);
            //删除15天之前所有订单数据
            Orders::create()->where('created_at',Carbon::now()->subDays(15)->toDateString(),'<')
                ->destroy(null,true);
            //删除15天之前所有结算数据
            Trades::create()->where('created_at',Carbon::now()->subDays(15)->toDateString(),'<')
                ->destroy(null,true);
        });
    }

    function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        write_log($throwable->getMessage());
    }
}
