<?php

namespace App\HttpController\Admin\System;

use App\HttpController\Admin\Auth\AuthController;
use App\Model\Configs;

class ConfigController extends AuthController
{

    public function setting()
    {
        try {
            $configs = setting();
            return $this->writeJson(0, encrypt_data($configs));
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

    public function update()
    {
        try {
            $data = $this->params();
//            $model = Configs::create();
//            foreach ($data as $key => $value) {
//                $info = $model->get(['config_key' => $key]);
//                if($info){
//                    $info->update(['config_value' => $value]);
//                }else{
//                    $model->data(['config_key' => $key,'config_value' => $value,'id' => 0])->save();
//                }
//            }
            $remark = $this->remark();
            $str = "<?php"."\r\n\r\n";
            $str .= "    return [\r\n";
            $str .= "        'SITE_SETTING' => [\r\n";
            foreach ($remark as $key => $val){
                if(isset($data[$key])){
                    $value = is_numeric($data[$key]) ? $data[$key] : "'".$data[$key]."'";
                    $str .= "            '".$key."' => ".$value.",  //".$val."    \r\n";
                }
            }
            $str .= "        ]\r\n";
            $str .= "    ];\r\n";
            $res = file_put_contents(EASYSWOOLE_ROOT.'/App/Setting/setting.php',$str);
            return $this->writeJson(0, $res, '更新配置信息成功');
        } catch (\Throwable $e) {
            return $this->writeJson(1, null, $e->getMessage());
        }
    }

    protected function remark(){
        return [
            //基础设置
            'login_limit' => '登录限制: 0关 1开',
            'warning_ios' => 'IOS预警: 0关 1开',
            'warning_android' => '安卓预警: 0关 1开',
            'warning_order' => '订单预警：订单成功率预警',
            'online_time' => '在线统计：*分钟内的在线人数统计',
            'trade_money' => '结算金额：只有满足条件才会生成结算记录',
            'email' => '官方邮箱',
            //APP设置
            'limit_order_time' => '订单限制：分钟',
            'limit_order_num' => '订单限制：订单数量',
            'invite_gift' => '邀请奖励：钻石数量',
            'app_version' => 'APP版本号：APP内部版本号如此不同则提示更新',
            'free_vip_time' => '赠送时间：用户注册免费赠送vip时长单位分钟',
            'lottery_nvip' => '普通用户每天赠送抽奖次数',
            'lottery_vip' => 'VIP用户每天赠送抽奖次数',
            'lottery_diamond' => '每天消耗钻石所抽奖的次数',
            'lottery_num' => '抽奖消耗钻石数量',
            'lottery_percent' => '抽奖总概率',
            //域名设置
            'canal_admin' => '渠道域名',
            'agent_admin' => '代理域名',
            'custom_link' => '客服域名',
            'socket_link' => '客服通信：客服通信地址',
            'web_link' => '跳转域名：跳转到落地页的域名',
            'notify_url' => '回调域名',
            'update_url' => 'APP更新域名',
            'forever_url' => '永久域名',
            'apk_link' => 'APK域名：用于下载',
            //价格设置
            'default_price' => '默认价格：默认选中的价格',
            'day_vip' => '日卡价格',
            'day_gift' => '日卡赠送钻石数量',
            'day_buy' => '日卡购买钻石数量',
            'month_vip' => '月卡价格',
            'month_gift' => '月卡赠送钻石数量',
            'month_buy' => '月卡购买钻石数量',
            'quarter_vip' => '季卡价格',
            'quarter_gift' => '季卡赠送钻石数量',
            'quarter_buy' => '季卡购买钻石数量',
            'year_vip' => '年卡价格',
            'year_gift' => '年卡赠送钻石数量',
            'year_buy' => '年卡购买钻石数量',
            'forever_vip' => '终生价格',
            'forever_gift' => '终生卡赠送钻石数量',
            'forever_buy' => '终生卡购买钻石数量',
            //支付设置
            'payment_default' => '默认支付：1微信2支付宝',
            'payment_wechat' => '微信支付： 0关 1开',
            'payment_type_wechat_day' => '微信日卡渠道',
            'payment_type_wechat_month' => '微信月卡渠道',
            'payment_type_wechat_quarter' => '微信季卡渠道',
            'payment_type_wechat_year' => '微信年卡渠道',
            'payment_type_wechat_forever' => '微信终生卡渠道',
            'payment_alipay' => '支付宝支付： 0关 1开',
            'payment_type_alipay_day' => '支付宝日卡渠道',
            'payment_type_alipay_month' => '支付宝月卡渠道',
            'payment_type_alipay_quarter' => '支付宝季卡渠道',
            'payment_type_alipay_year' => '支付宝年卡渠道',
            'payment_type_alipay_forever' => '支付宝终生卡渠道',

            //用户设置
            'vip0_price' => '用户等级-普通-需要消费的金额',
            'vip0_rebate' => '用户等级-普通-返利百分比',
            'vip1_price' => '用户等级-青铜-需要消费的金额',
            'vip1_rebate' => '用户等级-青铜-返利百分比',
            'vip2_price' => '用户等级-白银-需要消费的金额',
            'vip2_rebate' => '用户等级-白银-返利百分比',
            'vip3_price' => '用户等级-黄金-需要消费的金额',
            'vip3_rebate' => '用户等级-黄金-返利百分比',
            'vip4_price' => '用户等级-铂金-需要消费的金额',
            'vip4_rebate' => '用户等级-铂金-返利百分比',
            'vip5_price' => '用户等级-钻石-需要消费的金额',
            'vip5_rebate' => '用户等级-钻石-返利百分比',
            'vip6_price' => '用户等级-星耀-需要消费的金额',
            'vip6_rebate' => '用户等级-星耀-返利百分比',
            'vip7_price' => '用户等级-王者-需要消费的金额',
            'vip7_rebate' => '用户等级-王者-返利百分比',
        ];
    }

}
