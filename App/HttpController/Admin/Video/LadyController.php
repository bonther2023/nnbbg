<?php

namespace App\HttpController\Admin\Video;

use App\HttpController\Admin\Auth\AuthController;
use App\Model\Ladys;
use Carbon\Carbon;

class LadyController extends AuthController
{

    public function list()
    {
        try {
            $param = $this->params();
            $param['page'] = (int)$param['page'] ?? 1;
            $param['kwd'] = (string)$param['kwd'] ?? '';
            $param['status'] =(int)$param['status'] ?? 0;
            $model = Ladys::create();
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
            $model = Ladys::create();
            $data['updated_at'] = Carbon::now();
            if ($data['id']) {
                //编辑
                $model->update($data,['id' => $data['id']]);
                return $this->writeJson(0, null, '编辑楼凤视频信息成功');
            } else {
                //新增
                $data['created_at'] = Carbon::now();
                $model->data($data)->save();
                return $this->writeJson(0, null, '新增楼凤视频信息成功');
            }
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

    public function info()
    {
        try {
            $data = $this->params();
            $id = (int)$data['id'] ?? 0;
            $model = Ladys::create();
            $info = $model->get($id);
            if (empty($info)) return $this->writeJson(1,null,'抱歉，你要操作的信息不存在');
            return $this->writeJson(0, encrypt_data($info));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

    public function lock()
    {
        try {
            $data = $this->params();
            $id = (int)$data['id'] ?? 0;
            $model = Ladys::create();
            $info = $model->get($id);
            if (empty($info)) return $this->writeJson(1,null,'抱歉，你要操作的信息不存在');
            $info->update(['status' => Ladys::STATUS_3]);
            return $this->writeJson(0);
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

    public function active()
    {
        try {
            $data = $this->params();
            $id = (int)$data['id'] ?? 0;
            $model = Ladys::create();
            $info = $model->get($id);
            if (empty($info)) return $this->writeJson(1,null,'抱歉，你要操作的信息不存在');
            $info->update(['status' => Ladys::STATUS_1]);
            return $this->writeJson(0);
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }


    public function destroy()
    {
        try {
            $data = $this->params();
            $id = (int)$data['id'] ?? 0;
            $model = Ladys::create();
            $info = $model->get($id);
            if (empty($info)) return $this->writeJson(1,null,'抱歉，你要操作的信息不存在');
            $info->destroy();
            return $this->writeJson(0);
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }



}
