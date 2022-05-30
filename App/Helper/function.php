<?php

use App\Model\Configs;
use App\Utility\RedisClient;
use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\RedisPool\RedisPool;

/**
 * 返回 dev.php 文件配置
 * @param string $name 配置名称
 * @param string $default 默认值
 * @return array|mixed|null
 */
function config($name = '', $default = '')
{
    return Config::getInstance()->getConf($name) ?: $default;
}


/**
 * 获取配置
 * @param string $name
 * @return array|mixed|string|null
 */
function setting($name = '')
{
    Config::getInstance()->loadFile(EASYSWOOLE_ROOT.'/App/Setting/setting.php');
    return $name ? config('SITE_SETTING')[$name] : config('SITE_SETTING');
}


function settlement($data)
{
    $redis = RedisPool::defer('redis');
    $job = json($data);
    $redis->rPush('queue:user-settlement',$job);
}



/**
 * 日志
 * @param $data
 * @param int $level
 */
function write_log($data,$level = 1)
{
    if (is_object($data) || is_array($data)) {
        $data = json_encode($data);
    }
    switch ($level){
        case 2:
            Logger::getInstance()->notice($data);
            break;
        case 3:
            Logger::getInstance()->waring($data);
            break;
        case 4:
            Logger::getInstance()->error($data);
            break;
        default:
            Logger::getInstance()->info($data);
            break;
    }
}


/**
 * 设置过期时间
 * @param int $time 有效期，单位秒
 * @return int
 */
function expires($time = 0)
{
    return time() + (int)$time;
}

/**
 * json格式化
 * @param $data
 * @return false|string
 */
function json($data)
{
    return json_encode($data);
}

/**
 * json反格式化
 * @param $data
 * @return false|string
 */
function unjson($data)
{
    return json_decode($data,true);
}


/**
 * 返回管理后台url地址
 * @param string $path
 * @return string
 */
function url_admin($path = '')
{
    return trim(trim(config('SERVER_URL'), '/') . '/suibian/' . trim($path, '/'), '/');
}
/**
 * 返回渠道后台url地址
 * @param string $path
 * @return string
 */
function url_canal($path = '')
{
    return trim(trim(config('SERVER_URL'), '/') . '/' . trim($path, '/'), '/');
}

/**
 * 返回代理后台url地址
 * @param string $path
 * @return string
 */
function url_agent($path = '')
{
    return trim(trim(config('SERVER_URL'), '/') . '/agent/' . trim($path, '/'), '/');
}

/**
 * 返回API url地址
 * @param string $path
 * @return string
 */
function url_api($path = '')
{
    return trim(trim(config('SERVER_URL'), '/') . '/api/' . trim($path, '/'), '/');
}

/**
 * 返回静态地址
 * @param string $name
 * @return string
 */
function asset($name = '')
{
    return trim(config('SERVER_URL'), '/') . '/' . $name;
}


/**
 * 状态转换
 * @param $data
 * @param array $map
 * @return mixed
 */
function state_to_text(&$data, $map = [])
{
    foreach ($data as $key => &$row) {
        foreach ($map as $col => $pair) {
            if (isset($row[$col]) && isset($pair[$row[$col]])) {
                $text = $col . '_text';
                $row[$text] = $pair[$row[$col]];
            }
        }
    }
    return $data;
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pk 主键字段
 * @param string $pid parent标记字段
 * @param string $child child名字
 * @param int $root 最顶级id数字
 * @return array
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = 'children', $root = 0)
{
    // 创建Tree
    $tree = [];
    // 创建基于主键的数组引用
    $refer = [];
    foreach ($list as $key => $data) {
        $refer[$data[$pk]] =& $list[$key];
    }
    foreach ($list as $key => $data) {
        // 判断是否存在parent
        $parentId = $data[$pid];
        if ($root == $parentId) {
            $tree[] =& $list[$key];
        } else {
            if (isset($refer[$parentId])) {
                $parent =& $refer[$parentId];
                $parent[$child][] =& $list[$key];
            }
        }
    }
    return $tree;
}

/**
 * 获取扣量信息
 * @param $percent 扣量百分比，必须是10的倍数
 * @return mixed
 */
function deduction($percent = 0)
{
    //设置总概率为10
    $t = 10;

    //计算扣量所占总概率的比重
    $d = $percent / $t;

    //随机从1到总概率之间取一个值
    $r = mt_rand(1, $t);

    if($r > $d){
        return false;//结算
    }
    return true;//扣量
}

/**
 * php加密用于js解密
 * @param $data
 * @return string
 */
function encrypt_data($data)
{
    $config = config('TOKEN');
    $key = $config['key'];
    $iv  = $config['iv'];
    return base64_encode(openssl_encrypt(json_encode($data),"aes-128-cbc",$key,OPENSSL_RAW_DATA,$iv));
}

/**
 * php解密js加密字符串
 * @param $data
 * @return string
 */
function decrypt_data($data)
{
    $config = config('TOKEN');
    $key = $config['key'];
    $iv  = $config['iv'];
    return json_decode(trim(openssl_decrypt($data,"AES-128-CBC",$key,OPENSSL_ZERO_PADDING,$iv)),true);
}

/**
 * 邀请码/白嫖码
 * @param int $length
 * @return string
 */
function invite($length = 5)
{
    $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $rand = $code[rand(0, 25)] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
    $a = md5($rand, true);
    $s = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $d = '';
    for ($f = 0; $f < $length; $f++) {
        $g = ord($a[$f]);
        $d .= $s[($g ^ ord($a[$f + 8])) - $g & 0x1F];
    }
    return $d;
}
