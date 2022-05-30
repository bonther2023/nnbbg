<?php

namespace App\HttpController\Admin\Fiscal;

use App\HttpController\Admin\Auth\AuthController;
use App\Model\Trades;

class TradeController extends AuthController
{

    public function list()
    {
        try {
            $param = $this->params();
            $params['page'] = (int)$param['page'] ?? 1;
            $params['status'] = (int)$param['status'] ?? 0;
            $params['kwd'] = (string)$param['kwd'] ?? '';
            $model = Trades::create();
            $lists = $model->list($param);
            return $this->writeJson(0, encrypt_data($lists));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

    public function update()
    {
        try {
            $data = $this->params();
            $id = (int)$data['id'] ?? 0;
            $model = Trades::create();
            $info = $model->get($id);
            if (empty($info)) return $this->writeJson(1,null,'抱歉，你要操作的信息不存在');
            $info->update(['status' => Trades::STATUS_2]);
            return $this->writeJson(0);
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }
}
