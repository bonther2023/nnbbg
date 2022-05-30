<?php

use \App\HttpController\Router;


Router::group(['namespace' => 'App', 'prefix' => 'app'], function () {

    //登录注册
    Router::post('login', 'LoginController/index');

    //主页
    Router::get('good', 'IndexController/index');

    //检查版本是否是最新
    Router::post('check', 'IndexController/check');

    //广告
    Router::get('ad', 'AdController/index');

    //客服
    Router::post('custom', 'CustomController/index');

    //类目
    Router::get('category', 'CategoryController/index');

    //关注
    Router::post('follow', 'FollowController/index');

    //解锁
    Router::post('buy', 'BuyController/index');

    //视频
    Router::group(['prefix' => 'video'], function () {
        Router::get('', 'VideoController/index');//列表
        Router::get('info', 'VideoController/info');//详情
    });

    //媒体
    Router::group(['prefix' => 'media'], function () {
        Router::get('', 'MediaController/index');//列表
        Router::get('info', 'MediaController/info');//详情
        Router::get('latest', 'MediaController/latest');//最新
    });

    //快播
    Router::group(['prefix' => 'qovd'], function () {
        Router::get('', 'QovdController/index');//推荐列表 推荐
        Router::get('info', 'QovdController/info');//详情
        Router::get('quality', 'QovdController/quality');//优选 推荐，关注度排序
        Router::get('hot', 'QovdController/hot');//热门 关注度排序
        Router::get('author', 'QovdController/author');//博主视频
        Router::get('focus', 'QovdController/focus');//关注了的博主的视频
    });

    //博主
    Router::group(['prefix' => 'author'], function () {
        Router::get('', 'AuthorController/index');//列表
        Router::get('savor', 'AuthorController/savor');//兴趣
    });
    //免费视频(楼凤视频)
    Router::group(['prefix' => 'lady'], function () {
        Router::get('', 'LadyController/index');//列表
        Router::get('info', 'LadyController/info');//详情
    });

    //直播
    Router::get('live', 'LiveController/index');//列表
    //主播
    Router::group(['prefix' => 'anchor'], function () {
        Router::get('', 'AnchorController/index');//列表
        Router::get('info', 'AnchorController/info');//兴趣
    });

    //消息
    Router::get('news', 'NewsController/index');//列表

    //性闻
    Router::group(['prefix' => 'article'], function () {
        Router::get('', 'ArticleController/index');//列表
        Router::get('info', 'ArticleController/info');//详情
    });

    //专题
    Router::group(['prefix' => 'topic'], function () {
        Router::get('', 'TopicController/index');//列表
        Router::get('info', 'TopicController/info');//详情
    });

    //游戏
    Router::group(['prefix' => 'game'], function () {
        Router::get('', 'GameController/index');//列表
        Router::get('lottery', 'GameController/lottery');//中奖记录
        Router::post('wheel/lottery', 'GameController/wheelLottery');//抽奖
    });

    //活动
    Router::group(['prefix' => 'doing'], function () {
        Router::get('', 'DoingController/index');//主页
        Router::get('whore', 'DoingController/whore');//白嫖活动
        Router::post('receive', 'DoingController/receive');//领取白嫖码
    });

    //楼凤
    Router::group(['prefix' => 'whore'], function () {
        Router::get('', 'WhoreController/index');//主页
        Router::get('list', 'WhoreController/list');//列表
        Router::get('info', 'WhoreController/info');//详情
        Router::get('select', 'WhoreController/select');//筛选项
    });

    //电台
    Router::group(['prefix' => 'sound'], function () {
        Router::get('', 'SoundController/index');//主页
        Router::get('list', 'SoundController/list');//列表
        Router::get('chapter', 'SoundController/chapter');//章节
        Router::get('info', 'SoundController/info');//详情
    });

    //短篇小说
    Router::group(['prefix' => 'conte'], function () {
        Router::get('', 'ConteController/index');//主页
        Router::get('list', 'ConteController/list');//列表
        Router::get('info', 'ConteController/info');//详情
    });

    //长篇
    Router::group(['prefix' => 'novel'], function () {
        Router::get('', 'NovelController/index');//主页
        Router::get('list', 'NovelController/list');//列表
        Router::get('chapter', 'NovelController/chapter');//章节
        Router::get('info', 'NovelController/info');//详情
    });

    //套图
    Router::group(['prefix' => 'image'], function () {
        Router::get('', 'ImageController/index');//主页
        Router::get('list', 'ImageController/list');//列表
        Router::get('info', 'ImageController/info');//详情
    });

    //漫画
    Router::group(['prefix' => 'comics'], function () {
        Router::get('', 'ComicsController/index');//主页
        Router::get('list', 'ComicsController/list');//列表
        Router::get('chapter', 'ComicsController/chapter');//章节
        Router::get('info', 'ComicsController/info');//详情
    });


    //配置
    Router::group(['prefix' => 'config'], function () {
        Router::get('charge', 'ConfigController/charge');//VIP
        Router::get('diamond', 'ConfigController/diamond');//钻石
    });

    //用户
    Router::group(['prefix' => 'user'], function () {
        Router::get('', 'UserController/index');//用户
        Router::post('code', 'UserController/code');//绑定邀请码
        Router::post('mobile', 'UserController/mobile');//绑定手机号
        Router::post('find', 'UserController/find');//找回账号信息
        Router::post('lock', 'UserController/lock');//检查用户是否被锁定
        Router::get('record', 'UserController/record');//消费记录
        Router::get('buy', 'UserController/buy');//解锁记录
        Router::get('rank', 'UserController/rank');//会员等级
        Router::post('check', 'UserController/check');
    });

    //订单
    Router::group(['prefix' => 'order'], function () {
        Router::post('', 'OrderController/index');//创建
        Router::get('check', 'OrderController/check');//检查
    });


    //支付
    Router::group(['prefix' => 'pay'], function () {
        Router::get('', 'PayController/index');
        Router::get('test', 'PayController/test');
    });


    //回调
    Router::group(['prefix' => 'notify'], function () {
        Router::any('fuxi', 'NotifyController/fuxi');//伏羲支付
        Router::any('yangyu', 'NotifyController/yangyu');//洋芋支付
        Router::any('cang', 'NotifyController/cang');//小苍支付
        Router::any('bossyun', 'NotifyController/bossyun');
        Router::any('rongyi', 'NotifyController/rongyi');
        Router::any('xinke', 'NotifyController/xinke');
        Router::any('guazi', 'NotifyController/guazi');
        Router::any('minsen', 'NotifyController/minsen');
    });

});






















