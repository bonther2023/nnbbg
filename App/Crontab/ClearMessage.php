<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-6
 * Time: 下午3:30
 */

namespace App\Crontab;

use App\Model\Customs;
use App\Model\Messages;
use Carbon\Carbon;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\EasySwoole\Crontab\AbstractCronTask;

class ClearMessage extends AbstractCronTask
{

    public static function getRule(): string
    {
        return '*/10 * * * *';//每10分钟执行一次
    }

    public static function getTaskName(): string
    {
        // 定时任务名称
        return 'ClearMessage';
    }

    function run(int $taskId, int $workerIndex)
    {
        TaskManager::getInstance()->async(function (){
            $model = Customs::create();
            $users = $model->all();
            $now = Carbon::now();
            foreach ($users as $user){
                $current = $now->diffInHours($user['created_at']);
                //最近登录时间超过1小时则删除信息
                if($current >= 12){
                    //删除消息数据
                    Messages::create()->destroy(['uid' => $user['uid']]);
                    //删除用户数据
                    $model->destroy(['id' => $user['id']]);
                }
            }
        });

    }

    function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        write_log($throwable->getMessage());
    }
}
