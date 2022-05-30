<?php

namespace App\HttpController\Admin\User;

use App\HttpController\Admin\Auth\AuthController;
use App\Model\Costs;
use App\Model\Users;
use Carbon\Carbon;

class CostController extends AuthController
{

    public function list()
    {
        try {
            $param = $this->params();
            $param['page'] = (int)$param['page'] ?? 1;
            $param['title'] = (string)$param['title'] ?? '';
            $param['uid'] = (int)$param['uid'] ?? 0;
            $model = Costs::create();
            $lists = $model->list($param);
            return $this->writeJson(0,encrypt_data($lists));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }



}
