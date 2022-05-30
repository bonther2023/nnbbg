<?php

namespace App\HttpController\Agent;

use App\Model\Agents;
use App\Model\Canals;
use EasySwoole\Utility\Hash;

class CanalController extends AuthController
{

    public function index()
    {
        try {
            $data = $this->params();
            $param = [
                'page' => $data['page'] ?? 1,
                'id' => $data['id'] ?? 0,
                'aid' => $this->userid,
            ];
            $model = Canals::create();
            $fields = 'id,username,percent_canal,percent_agent,password,balance,status,login_at';
            $lists = $model->list($param, $fields);
            return $this->writeJson(0, encrypt_data($lists));
        } catch (\Throwable $e) {
            return $this->writeJson(1);
        }
    }

    public function info(){
        try {
            $data = $this->params();
            $id = $data['id'] ?? 0;
            if(!$id){
                return $this->writeJson(1, null, '参数错误');
            }
            $model = Canals::create();
            $fields = 'id,username,percent_canal,password';
            $info = $model->field($fields)->get($id);
            if(!$info){
                return $this->writeJson(1, null, '数据不存在');
            }
            $info['password'] = '';
            return $this->writeJson(0, encrypt_data($info));
        } catch (\Throwable $e) {
            return $this->writeJson(1);
        }
    }



    public function update()
    {
        try {
            $data = $this->params();
            $id = $data['id'] ?? 0;
            $model = Canals::create();
            $info = $model->get($id);
            if (empty($info)) return $this->writeJson(1,null,'抱歉，你要操作的信息不存在');
            //判断分成设置是不是5的倍数  percent_canal   percent_agent
            if($data['percent_canal']%5 || $data['percent_canal'] > 95){
                return $this->writeJson(1,null, '请设置渠道分成为5的倍数且最大数不能超过95');
            }
            $data['percent_agent'] = 95 - $data['percent_canal'];
            if($data['password']){
                $data['password'] = Hash::makePasswordHash($data['password']);
            }else{
                unset($data['password']);
            }
            $info->update($data);
            return $this->writeJson(0, null, '更新成功');
        } catch (\Throwable $e) {
            return $this->writeJson(1);
        }
    }




}
