<?php

namespace App\HttpController\App;


class ConfigController extends AuthController
{


    public function charge()
    {
        try {
            $config = setting();
            $vip = [
                'config' => [
                    [
                        'title' => '黄金日卡',
                        'subTitle' => '小撸怡情',
                        'money' => $config['day_vip'],
                        'yuan' => 58,
                        'diamond' => $config['day_gift'],
                        'date' => '1天',
                    ],
                    [
                        'title' => '铂金月卡',
                        'subTitle' => '大撸怡精',
                        'money' => $config['month_vip'],
                        'yuan' => 98,
                        'diamond' => $config['month_gift'],
                        'date' => '30天',
                    ],
                    [
                        'title' => '钻石季卡',
                        'subTitle' => '春夏秋冬手不闲',
                        'money' => $config['quarter_vip'],
                        'yuan' => 198,
                        'diamond' => $config['quarter_gift'],
                        'date' => '90天',
                    ],
                    [
                        'title' => '至尊年卡',
                        'subTitle' => '撸到至尊撸自吟',
                        'money' => $config['year_vip'],
                        'yuan' => 398,
                        'diamond' => $config['year_gift'],
                        'date' => '365天',
                    ],
                    [
                        'title' => '超级永久',
                        'subTitle' => 'SVIP超级会员',
                        'money' => $config['forever_vip'],
                        'yuan' => 998,
                        'diamond' => $config['forever_gift'],
                        'date' => '永久',
                    ],
                ],
                'wechat' => $config['payment_wechat'],
                'alipay' => $config['payment_alipay'],
                'default' => $config['default_price'],
                'payment' => $config['payment_default'],

            ];
            return $this->writeJson(0, encrypt_data($vip));
        } catch (\Throwable $e) {
            write_log('Config-charge:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }

    }


    public function diamond()
    {
        try {
            $config = setting();
            $rank = $this->rank();
            $rebate = $config['vip'.$rank.'_rebate'];
            $vip = [
                'config' => [
                    [
                        'title' => '加个钟',
                        'money' => $config['day_vip'],
                        'zuan' => $config['day_buy'],
                        'diamond' => $config['day_buy'] * $rebate * 0.01 + $config['day_buy'],
                    ],
                    [
                        'title' => '调个情',
                        'money' => $config['month_vip'],
                        'zuan' => $config['month_buy'],
                        'diamond' => $config['month_buy'] * $rebate * 0.01 + $config['month_buy'],
                    ],
                    [
                        'title' => '来半套',
                        'money' => $config['quarter_vip'],
                        'zuan' => $config['quarter_buy'],
                        'diamond' => $config['quarter_buy'] * $rebate * 0.01 + $config['quarter_buy'],
                    ],
                    [
                        'title' => '整全套',
                        'money' => $config['year_vip'],
                        'zuan' => $config['year_buy'],
                        'diamond' => $config['year_buy'] * $rebate * 0.01 + $config['year_buy'],
                    ],
                    [
                        'title' => '哆嗦中',
                        'money' => $config['forever_vip'],
                        'zuan' => $config['forever_buy'],
                        'diamond' => $config['forever_buy'] * $rebate * 0.01 + $config['forever_buy'],
                    ],
                ],
                'wechat' => $config['payment_wechat'],
                'alipay' => $config['payment_alipay'],
                'default' => $config['default_price'],
                'payment' => $config['payment_default'],
                'rebate' => $rebate,
            ];
            return $this->writeJson(0, encrypt_data($vip));
        } catch (\Throwable $e) {
            write_log('Config-diamond:');
            write_log($e->getMessage(), 4);
            return $this->writeJson(1, null, '请求失败，请稍后再试');
        }

    }


}
