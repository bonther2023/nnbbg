<?php

namespace App\HttpController;



use App\Model\Canals;
use App\Utility\Captcha;
use EasySwoole\LinuxDash\LinuxDash;
use EasySwoole\Queue\Job;
use App\Utility\MyQueue;
use ZxInc\Zxipdb\IPTool;

class IndexController extends BaseController {
    public function index(){
        return $this->writeJson(1,12312332);
    }

    public function aaaa(){
//        $run = new \Swoole\Coroutine\Scheduler();
//        $run->add(function () {
//            //获取ip地址网卡缓冲信息
//            $data = LinuxDash::arpCache();
//            var_dump($data);
//            //获取当前带宽数据
//            $data = LinuxDash::bandWidth();
//            var_dump($data);
//            //获取cpu进程占用排行信息
//            $data = LinuxDash::cpuIntensiveProcesses();
//            var_dump($data);
//            //获取磁盘分区信息
//            $data = LinuxDash::diskPartitions();
//            var_dump($data);
//            //获取当前内存使用信息
//            $data = LinuxDash::currentRam();
//            var_dump($data);
//            //获取cpu信息
//            //获取cpu信息
//            $data = LinuxDash::cpuInfo();
//            var_dump($data);
//            //获取当前系统信息
//            $data = LinuxDash::generalInfo();
//            var_dump($data);
//            //获取当前磁盘io统计
//            $data = LinuxDash::ioStats();
//            var_dump($data);
//            //获取ip地址
//            $data = LinuxDash::ipAddresses();
//            var_dump($data);
//            //CPU负载信息
//            $data = LinuxDash::loadAvg();
//            var_dump($data);return $this->writeJson(1,$data);
//        });
//        $run->start();
    }

    public function op(){
        return $this->writeJson(1);
    }

    public function uo(){
        // 获取队列
        $queue = MyQueue::getInstance();
        // 创建任务
        $job = new Job();
        for($i = 1 ;$i <= 3000 ; $i++){
            $job->setJobData($i);
            $queue->producer()->push($job);
        }
    }

    public function captcha()
    {
        try {
            $captcha = (new Captcha())->create();
            return $this->writeJson(0, encrypt_data($captcha));
        } catch (\Throwable $e) {
            write_log($e->getMessage());
            return $this->writeJson(1, null, $e->getMessage());
        }
    }


}
