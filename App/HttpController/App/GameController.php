<?php

namespace App\HttpController\App;

use App\Model\Costs;
use App\Model\Games;
use App\Model\Lotterys;
use App\Model\Users;
use Carbon\Carbon;
use EasySwoole\ORM\DbManager;

class GameController extends AuthController
{


    public function index()
    {
        try {
            $param = $this->params();
            $param['page'] = $param['page'] ?? 1;
            $model = Games::create();
            $lists = $model->app($param);
            return $this->writeJson(0, encrypt_data($lists));
        } catch (\Throwable $e) {
            write_log('Game-index:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }
    }

    public function lottery(){
        try {
            $model = Lotterys::create();
            $lists = $model->app();
            return $this->writeJson(0, encrypt_data($lists));
        } catch (\Throwable $e) {
            write_log('Game-lottery:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }
    }

    public function wheelLottery(){
        try {
            if(!$this->userid){
                return $this->writeJson(1, null, '请求失败，请稍后再试');
            }
            // 开启事务
            DbManager::getInstance()->startTransaction();
            $model = Users::create();
            $user = $model->field('id,username,balance,vip,lottery,lottery_free,lottery_date,vip_at')->get($this->userid);
            if(!$user){
                return $this->writeJson(1, null, '请求失败，请稍后再试');
            }
            if($user['lottery'] < 1){
                return $this->writeJson(1, null, '您的抽奖次数不足');
            }
            $lottery = $user['lottery'];//获取剩余总抽奖次数
            $free = $user['lottery_free'];//免费次数
            //开始抽奖
            if($free > 0){
                //拥有免费次数，则消耗免费次数
                $model->decrease('lottery_free', $user['id']);
                $free--;
            }else{
                //判断余额是否充足lotterys
                $money = setting('lottery_num');
                if($user['balance'] < $money){
                    return $this->writeJson(1, null, '您的钻石余额不足');
                }
                //无免费次数，则消耗付费次数
                $model->decrease('lottery', $user['id']);
                //扣除钻石
                $model->decrease('balance', $user['id'], $money);
                //增加用户消费记录
                Costs::create()->add($user['id'], $money, '幸运抽奖');
                $lottery--;
                $user['balance'] -= $money;
            }
            //设置中奖规则
            //普通规则
            //VIP规则（year_vip和forever_vip）
            $t = setting('lottery_percent') - 1;//设置总概率
            $prizes = [0, 3];//奖池
            if($user['vip']){
                if($user['vip'] != 'free_vip'){
                    $prizes = [0, 1, 3, 4, 5, 6, 7];//奖池
                }
                if($user['vip'] == 'quarter_vip'){
                    $prizes = [0, 1, 2, 3, 4, 5, 6, 7];
                }
                if($user['vip'] == 'year_vip' || $user['vip'] == 'forever_vip'){
                    $prizes = [0, 1, 2, 3, 4, 5, 6, 7, 8];
                }
            }
            //随机从1到总概率之间取一个值
            $win = mt_rand(0, $t);
            if(!in_array($win, $prizes)){
                $win = 3;
            }
            //0  5钻石     1  20钻石   2  100钻石
            //3  谢谢参与   4  5钻石    5  1日VIP
            //6  20钻石    7  5钻石    8  1月VIP
            $lotteryModel = Lotterys::create();
            switch ($win){
                case 0://中奖钻石 5
                case 4://中奖钻石 5
                case 7://中奖钻石 5
                    $insert = [
                        'uid' => $user['id'],
                        'username' => $user['username'],
                        'title' => '恭喜' . $user['username'] . '，中奖 5 钻石',
                        'created_at' => Carbon::now(),
                    ];
                    $lotteryModel->add($insert);
                    //增加用户消费记录
                    Costs::create()->add($user['id'], 5, '幸运中奖', 1);
                    //增加用户钻石
                    $model->increase('balance', $user['id'], 5);
                    break;
                case 1://中奖钻石 20
                case 6://中奖钻石 20
                    $insert = [
                        'uid' => $user['id'],
                        'username' => $user['username'],
                        'title' => '恭喜' . $user['username'] . '，中奖 20 钻石',
                        'created_at' => Carbon::now(),
                    ];
                    $lotteryModel->add($insert);
                    //增加用户消费记录
                    Costs::create()->add($user['id'], 20, '幸运中奖', 1);
                    //增加用户钻石
                    $model->increase('balance', $user['id'], 20);
                    break;
                case 2://中奖钻石 100
                    $insert = [
                        'uid' => $user['id'],
                        'username' => $user['username'],
                        'title' => '恭喜' . $user['username'] . '，中奖 100 钻石',
                        'created_at' => Carbon::now(),
                    ];
                    $lotteryModel->add($insert);
                    //增加用户消费记录
                    Costs::create()->add($user['id'], 100, '幸运中奖', 1);
                    //增加用户钻石
                    $model->increase('balance', $user['id'], 100);
                    break;
                case 5: //中奖VIP 1天
                    $insert = [
                        'uid' => $user['id'],
                        'username' => $user['username'],
                        'title' => '恭喜' . $user['username'] . '，中奖 1 天VIP',
                        'created_at' => Carbon::now(),
                    ];
                    $lotteryModel->add($insert);
                    $time = $user['vip_at'] + 86400;
                    $user->update(['vip' => 'day_vip', 'vip_at' => $time]);
                    break;
                case 8://中奖VIP 1月
                    $insert = [
                        'uid' => $user['id'],
                        'username' => $user['username'],
                        'title' => '恭喜' . $user['username'] . '，中奖 1 月VIP',
                        'created_at' => Carbon::now(),
                    ];
                    $lotteryModel->add($insert);
                    $time = $user['vip_at'] + 86400 * 30;
                    $user->update(['vip' => 'month_vip', 'vip_at' => $time]);
                    break;
                default://未中奖
                    break;
            }
            //重置抽奖次数
            $date = Carbon::now()->toDateString();
            if($date != $user['lottery_date']){
                //不等，则重置抽奖次数
                $free = setting('lottery_nvip');
                if($user['vip'] && ($user['vip'] != 'free_vip')){
                    $free = setting('lottery_vip');
                }
                $lottery = setting('lottery_diamond');
                $user->update(['lottery' => $lottery,'lottery_free' => $free, 'lottery_date' => $date]);
            }
            // 提交事务
            DbManager::getInstance()->commit();
            return $this->writeJson(0, encrypt_data([
                'lottery' => $lottery,
                'free' => $free,
                'win' => $win,
                'balance' => $user['balance'],
            ]));
        } catch (\Throwable $e) {
            DbManager::getInstance()->rollback();
            write_log('Game-wheelLottery:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }
    }


}
