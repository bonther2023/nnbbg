<?php

namespace App\HttpController\Admin\Fiscal;

use App\HttpController\Admin\Auth\AuthController;
use App\Model\Reports;
use Carbon\Carbon;
use ZxInc\Zxipdb\IPTool;

class ReportController extends AuthController
{

    public function list()
    {
        try {
            $param = $this->params();
            $param['date'] = (string)$param['date'] ?? '';
            $param['cid'] = (int)$param['cid'] ?? 0;
            $model = Reports::create();
            $fields = 'SUM(`install_ios`) as install_ios,SUM(`install_and`) as install_and,SUM(`monad`) as monad,SUM(`pay`) as pay,date,hour';
            $lists = $model->list($param,$fields);
            $installAnd = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
            $installIos = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
            $monads = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
            $pays = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
            foreach ($lists as $list) {
                for ($i = 0; $i < 24; $i++) {
                    if ($i == $list['hour']) {
                        $installAnd[$i] = $list['install_and'];
                        $installIos[$i] = $list['install_ios'];
                        $monads[$i] = $list['monad'];
                        $pays[$i] = $list['pay'];
                    }
                }
            }
            $statis = [
                'install_and' => $installAnd,
                'install_ios' => $installIos,
                'monad' => $monads,
                'pay' => $pays,
            ];
            return $this->writeJson(0, encrypt_data(['lists' => $lists, 'statis' => $statis]));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

    public function compare(){
        try {
            $param = $this->params();
            $param['cid'] = (int)$param['cid'] ?? 0;
            $param['date'] = Carbon::now()->format('Y-m-d');
            $model = Reports::create();
            $fields = 'SUM(`install_ios`) as install_ios,SUM(`install_and`) as install_and,SUM(`monad`) as monad,SUM(`pay`) as pay,date,hour';
            $data = [];
            $date = [];
            for($i = 0;$i < 7; $i++){
                $d = Carbon::now()->subDays($i)->format('Y-m-d');
                $param['date'] = $d;
                $date[] = $d;
                $data[] = $model->list($param, $fields);
            }
            $_data = [];
            foreach ($data as $item) {
                foreach ($item as $val){
                    for ($i = 0; $i < 24; $i++) {
                        if ($i == $val['hour']) {
                            $_data[23-$i]['hour'] = $val['hour'];
                            $_data[23-$i][$val['date']] = ($val['install_ios'] + $val['install_and']).'【'.$val['pay'].'】';
                            break;
                        }
                    }
                }
            }
            ksort($_data);
            return $this->writeJson(0, encrypt_data(['lists' => array_column($_data,null), 'dates' => $date]));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }


}
