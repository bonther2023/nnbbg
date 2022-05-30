<?php

namespace App\HttpController\Admin\System;

use App\HttpController\Admin\Auth\AuthController;
use App\Model\Admins;
use App\Model\Customs;
use App\Model\Messages;

class CustomController extends AuthController
{

    public function index()
    {
        try {
            //客服信息
            $model = Admins::create();
            $fields = 'id,socket_id,online';
            $info = $model->field($fields)->get(1);
            //链接客服的所有用户
            $userList = Customs::create()->order('created_at', 'DESC')->all();
            $msgModel = Messages::create();
            foreach ($userList as $index=>&$item){
                //查看是否有消息记录
                $num = $msgModel->field('id')->where('uid',$item['uid'])->count();
                if($num){
                    //查看未读消息数量
                    $unread = $msgModel->field('id')
                        ->where('uid',$item['uid'])
                        ->where('is_read',Messages::IS_READ_1)
                        ->where('type',Messages::TYPE_1)
                        ->count();
                    $item['unread'] = $unread;
                }else{
                    unset($userList[$index]);
                }
            }
            $userList = array_values($userList);
            $socket = trim(setting('socket_link'),'/');
            $upload = trim(setting('custom_link'),'/').'/api/upload/image';
            return $this->writeJson(0, encrypt_data([
                'info' => $info,
                'socket' => $socket,
                'user' => $userList,
                'upload' => $upload,
                'words' => $this->words()
            ]));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

    protected function words(){
        return [
            '点我下载孔雀视频',
            '请发下支付详情',
            '30元只是一天的VIP，你的VIP信息已过期',
            '如果到账失败，可能是掉单了，请把微信或支付宝的付款记录详情页，截图发来，这边立即处理',
            '这边先为你开通了临时VIP权限，你可以先用着，处理完成后，会自动为你加上VIP时长',
            '请及时绑定手机，在用户中心自行找回VIP信息',
            '您可以使用其他支付方式或者支付的时候多等会儿',
            '如遇视频加载失败，可能是CDN延迟所致，请重启APP或者观看其他视频',
            'VIP没及时到账，可能受支付或者网络延迟的影响，请稍等几分钟',
            '你的订单超时支付，所以无法到账，会在三个工作日原路退回',
            '请稍后，正在处理',
            '请切换下网络或者重启APP',
        ];
    }


}
