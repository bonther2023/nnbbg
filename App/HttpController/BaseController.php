<?php

namespace App\HttpController;

use App\Queue\FlowQueue;
use App\Queue\ReportQueue;
use App\Queue\UserQueue;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Queue\Job;

abstract class BaseController extends Controller
{

    /**
     * 不要打开不网站
     */
    public function index()
    {
        $this->actionNotFound('index');
    }

    /**
     * 重置writeJson 方法
     * @param int $statusCode 0成功 1失败
     * @param null $result 结果
     * @param null $msg 消息提示
     * @return bool
     */
    protected function writeJson($statusCode = 0, $result = null, $msg = null)
    {
        if (!$this->response()->isEndResponse()) {
            $data = [
                "code" => $statusCode,
                "data" => $result,
                "msg" => $msg ?? 'SUCCESS'
            ];
            $this->response()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response()->withStatus(200);
            return true;
        } else {
            return false;
        }
    }

    protected function writeEcho($msg){
        if(!$this->response()->isEndResponse()){
            $this->response()->write($msg);
            $this->response()->withHeader('Content-type','application/json;charset=utf-8');
            $this->response()->withStatus(200);
            return true;
        }else{
            return false;
        }
    }

    protected function writeHtml($msg)
    {
        if (!$this->response()->isEndResponse()) {
            $this->response()->write($msg);
//            $this->response()->withHeader('Content-type','charset=utf-8');
            $this->response()->withStatus(200);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取IP
     * @return string
     */
    protected function getIp()
    {
        $ip = $this->request()->getHeaders();
        $str = $ip['x-forwarded-for'][0];
        if(strpos($str,',') > 25){
            $ip = trim(trim(substr($str,strpos($str,',')),','));
        }else{
            $ip = substr($str,0,strpos($str,','));
        }
        return  $ip;
    }

    /**
     * 判断请求是否事ajax请求
     * @return bool
     */
    protected function ajax()
    {
        $requested = $this->request()->getHeader('x-requested-with');
        if (head($requested) == 'XMLHttpRequest') {
            return true;
        }
        return false;
    }

    protected function params(){
        $request = $this->request();
        $data = $request->getRequestParam('params');
        return decrypt_data($data);
    }


    protected function queueUser($data){
        $queue = UserQueue::getInstance();
        $job = new Job();
        $job->setJobData($data);
        $queue->producer()->push($job);
    }

    protected function queueReport($data){
        $queue = ReportQueue::getInstance();
        $job = new Job();
        $job->setJobData($data);
        $queue->producer()->push($job);
    }

    protected function queueFlow($data){
        $queue = FlowQueue::getInstance();
        $job = new Job();
        $job->setJobData($data);
        $queue->producer()->push($job);
    }


    protected function parseData($data){
        $list = explode("\r\n", $data);
        foreach($list as $value){
            if($value){
                if(strstr($value, '--')) continue;
                if(strpos($value, '-')){
                    $key = str_replace('"', '', strchr($value, '"'));
                    continue;
                };
                if($value){
                    $array[$key] = $value;
                }
            }
        }
        return $array;
    }


    public function vtag(){
        return [
            ['name' => '地点', 'tags' => [
                '精品','车震', '户外', '宾馆', '浴室'
            ]],
            ['name' => '人物', 'tags' => [
                '主播','护士', '嫩模', '女同', '职场','姐妹花','闺蜜', '情侣', '人妻', '妹妹','老师','技师', '少妇', '幼女', '萝莉',
                '大学生','学妹', '美少女', '小姨子', '女神', '母狗'
            ]],
            ['name' => '方式', 'tags' => [
                '内射','后入式', '口爆', 'SM', '颜射','肛交','自慰', '潮吹', '足交', '捆绑','中出','3P', '双飞', '群交',
            ]],
            ['name' => '原因', 'tags' => [
                '强奸','迷奸', '约炮', '偷情', '乱伦','调教','诱惑', '交换', '潜规则',
            ]],
            ['name' => '美女', 'tags' => [
                '巨乳','制服', '翘臀', '清纯', '白虎','黑丝','性感', '长腿', '美乳', '极品','淫荡',
            ]],
            ['name' => '其他', 'tags' => [
                '偷拍','自拍', '剧情', '福利', '无码','动漫','香港', '韩国', '三级', '日本','有码','欧美', '洋妞', '大鸡巴', 'AI换脸', '网爆门',
            ]],
        ];
    }

    public function wtag(){
        return ['空姐','学生', '少妇', '处女', '洋妞','演员','嫩模', '护士', '网红', '老师','姐妹','可爱','甜美','淑女','美少女','高冷',
            '萝莉', '混血', '巨乳', '制服', '小姐','少爷', '教练', '持久', '大屌','小清新','传媒学校','极品','端庄大气','天然'];
    }

    public function project(){
        return ['胸推','指滑', '吹箫', '69式', '啪啪','鸳鸯浴','冰火两重天', 'SM', '臀推',
            'SPA','漫游','毒龙', '打飞机', '空中飞人', '观音坐莲', '沙漠风暴','帝王浴'];
    }


}
