<?php

namespace App\Crontab;

use App\Model\Users;
use Carbon\Carbon;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\EasySwoole\Crontab\AbstractCronTask;

class ClearUser extends AbstractCronTask
{

    public static function getRule(): string
    {
        return '0 0 * * *';//每天凌晨12点执行一次
    }

    public static function getTaskName(): string
    {
        return 'ClearUser';
    }

    function run(int $taskId, int $workerIndex)
    {
        // 定时任务处理逻辑：删除10天之前未登录的所有未进行充值过并且连钻石都没有的用户
        TaskManager::getInstance()->async(function (){
            Users::create()->where('money',0)
                ->where('balance',0)
                ->where('login_at',Carbon::now()->subDays(10)->toDateString(),'<')
                ->destroy(null,true);
        });
    }

    function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        write_log($throwable->getMessage());
    }
}
