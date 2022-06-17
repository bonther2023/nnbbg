<?php

namespace App\HttpController\Admin\Fiscal;

use App\HttpController\Admin\Auth\AuthController;
use App\Model\Flows;
use App\Model\Orders;
use App\Queue\FlowQueue;
use App\Queue\ReportQueue;
use App\Queue\UserQueue;
use Carbon\Carbon;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\Queue\Job;
use EasySwoole\RedisPool\RedisPool;

class OrderController extends AuthController
{

    public function list()
    {
        try {
            $param = $this->params();
            $param['page'] = (int)$param['page'] ?? 1;
            $param['status'] = (int)$param['status'] ?? 0;
            $param['type'] = (int)$param['type'] ?? 0;
            $param['share'] = (int)$param['share'] ?? 0;
            $param['kwd'] = (string)$param['kwd'] ?? '';
            $param['start'] = (string)$param['start'] ?? '';
            $param['end'] = (string)$param['end'] ?? '';
            $param['payment'] = (int)$param['payment'] ?? 0;
            $param['cid'] = (int)$param['cid'] ?? 0;
            $param['system'] = (int)$param['system'] ?? 0;
            $param['platform'] = (string)$param['platform'] ?? '';
            $model = Orders::create();
            $data = $model->list($param);
            return $this->writeJson(0, encrypt_data($data));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }




    public function update()
    {
        try {
            $data = $this->params();
            $id = (int)$data['id'] ?? 0;
            $model = Orders::create();
            $info  = $model->get($id);
            if ($info) {
                //更新flow
                $this->queueFlow([
                    'info' => [
                        'aid' => 0,
                        'cid' => $info['cid'],
                        'date' => Carbon::parse($info['created_at'])->toDateString(),
                        'system' => $info['system'],
                        'money' => $info['money'],
                        'mtype' => $info['mtype'],
                        'oid' => $info['id'],
                    ],
                    'order' => true,
                ]);
                //更新report
                $this->queueReport([
                    'info' => [
                        'aid' => 0,
                        'cid' => $info['cid'],
                        'date' => Carbon::parse($info['created_at'])->toDateString(),
                        'hour' => Carbon::parse($info['created_at'])->hour,
                    ],
                    'pay' => true,
                ]);
                return $this->writeJson(0, $info, '操作补单成功');
            } else {
                return $this->writeJson(1,null,'抱歉，你要操作的信息不存在');
            }
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }
}
