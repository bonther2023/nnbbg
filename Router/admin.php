<?php

use \App\HttpController\Router;

Router::group(['namespace' => 'Admin', 'prefix' => 'admin'], function () {
    //权限
    Router::group(['namespace' => 'Auth'], function () {
        Router::post('login', 'LoginController/login');//登录
    });

    //主页
    Router::group(['namespace' => 'Main'], function () {
        Router::get('online', 'IndexController/online');//online
        Router::get('monitor', 'IndexController/monitor');//monitor

    });

    //系统
    Router::group(['namespace' => 'System'], function () {
        //设置
        Router::group(['prefix' => 'config'], function () {
            Router::get('', 'ConfigController/setting');//配置
            Router::post('update', 'ConfigController/update');//更新配置
        });
        //支付
        Router::group(['prefix' => 'pay'], function () {
            Router::get('list', 'PayController/list');//列表
            Router::get('select', 'PayController/select');//支付
            Router::post('update', 'PayController/update');//更新
            Router::post('lock', 'PayController/lock');//锁定
            Router::post('active', 'PayController/active');//激活
            Router::post('destroy', 'PayController/destroy');//删除
        });

        //客服
        Router::get('custom', 'CustomController/index');//客服

        //推送消息
        Router::group(['prefix' => 'news'], function () {
            Router::get('list', 'NewsController/list');//列表
            Router::post('update', 'NewsController/update');//更新
            Router::post('destroy', 'NewsController/destroy');//删除
        });
    });

    //财务
    Router::group(['namespace' => 'Fiscal'], function () {
        //订单
        Router::group(['prefix' => 'order'], function () {
            Router::get('list', 'OrderController/list');//列表
            Router::post('update', 'OrderController/update');//补单
        });

        //流量趋势
        Router::group(['prefix' => 'flow'], function () {
            Router::get('list', 'FlowController/list');//列表
            Router::get('count', 'FlowController/count');//统计
        });

        //报表统计
        Router::group(['prefix' => 'report'], function () {
            Router::get('list', 'ReportController/list');//列表
            Router::get('compare', 'ReportController/compare');//对比（最近一周）
        });

        //结算
        Router::group(['prefix' => 'trade'], function () {
            Router::get('list', 'TradeController/list');//列表
            Router::post('update', 'TradeController/update');//更新结算状态
        });
    });

    //用户
    Router::group(['namespace' => 'User'], function () {
        //管理员
        Router::group(['prefix' => 'manager'], function () {
            Router::get('list', 'ManagerController/list');//列表
            Router::post('update', 'ManagerController/update');//更新
            Router::post('lock', 'ManagerController/lock');//锁定
            Router::post('active', 'ManagerController/active');//激活
            Router::post('destroy', 'ManagerController/destroy');//删除
        });
        //代理
        Router::group(['prefix' => 'agent'], function () {
            Router::get('list', 'AgentController/list');//列表
            Router::get('select', 'AgentController/select');//所有
            Router::post('update', 'AgentController/update');//更新
            Router::post('lock', 'AgentController/lock');//锁定
            Router::post('active', 'AgentController/active');//激活
            Router::post('destroy', 'AgentController/destroy');//删除
            Router::get('login', 'AgentController/login');//登录
        });
        //渠道
        Router::group(['prefix' => 'canal'], function () {
            Router::get('list', 'CanalController/list');//列表
            Router::get('select', 'CanalController/select');//所有
            Router::get('agent', 'CanalController/agent');//代理
            Router::post('update', 'CanalController/update');//更新
            Router::post('lock', 'CanalController/lock');//锁定
            Router::post('active', 'CanalController/active');//激活
            Router::post('destroy', 'CanalController/destroy');//删除
            Router::get('login', 'CanalController/login');//登录
        });
        //博主
        Router::group(['prefix' => 'author'], function () {
            Router::get('list', 'AuthorController/list');//列表
            Router::get('select', 'AuthorController/select');//选择
            Router::post('update', 'AuthorController/update');//更新
            Router::post('destroy', 'AuthorController/destroy');//删除
        });
        //用户
        Router::group(['prefix' => 'user'], function () {
            Router::get('list', 'UserController/list');//列表
            Router::post('update', 'UserController/update');//更新
            Router::post('lock', 'UserController/lock');//锁定
            Router::post('active', 'UserController/active');//激活
            Router::post('destroy', 'UserController/destroy');//删除
        });
        //消费记录
        Router::get('cost/list', 'CostController/list');//列表
    });

    //视频
    Router::group(['namespace' => 'Video'], function () {
        //普通
        Router::group(['prefix' => 'video'], function () {
            Router::get('list', 'VideoController/list');//列表
            Router::get('tag', 'VideoController/tag');//标签
            Router::get('info', 'VideoController/info');//详情
            Router::post('update', 'VideoController/update');//更新
            Router::post('lock', 'VideoController/lock');//锁定
            Router::post('active', 'VideoController/active');//激活
            Router::post('destroy', 'VideoController/destroy');//删除
        });
        //媒体
        Router::group(['prefix' => 'media'], function () {
            Router::get('list', 'MediaController/list');//列表
            Router::get('info', 'MediaController/info');//详情
            Router::post('update', 'MediaController/update');//更新
            Router::post('lock', 'MediaController/lock');//锁定
            Router::post('active', 'MediaController/active');//激活
            Router::post('destroy', 'MediaController/destroy');//删除
        });
        //快播
        Router::group(['prefix' => 'qovd'], function () {
            Router::get('list', 'QovdController/list');//列表
            Router::get('info', 'QovdController/info');//详情
            Router::post('update', 'QovdController/update');//更新
            Router::post('lock', 'QovdController/lock');//锁定
            Router::post('active', 'QovdController/active');//激活
            Router::post('destroy', 'QovdController/destroy');//删除
        });
        //专题
        Router::group(['prefix' => 'topic'], function () {
            Router::get('list', 'TopicController/list');//列表
            Router::post('update', 'TopicController/update');//更新
            Router::post('destroy', 'TopicController/destroy');//删除
            Router::get('chapter', 'TopicController/chapter');//章节
            Router::get('chapter/info', 'TopicController/chapterInfo');//详情
            Router::post('chapter/update', 'TopicController/chapterUpdate');//章节更新
            Router::post('chapter/destroy', 'TopicController/chapterDestroy');//章节删除
        });
        //楼凤
        Router::group(['prefix' => 'lady'], function () {
            Router::get('list', 'LadyController/list');//列表
            Router::get('info', 'LadyController/info');//详情
            Router::post('update', 'LadyController/update');//更新
            Router::post('lock', 'LadyController/lock');//锁定
            Router::post('active', 'LadyController/active');//激活
            Router::post('destroy', 'LadyController/destroy');//删除
        });
        //直播
        Router::group(['prefix' => 'live'], function () {
            Router::get('list', 'LiveController/list');//列表
            Router::get('select', 'LiveController/select');//所有
            Router::post('update', 'LiveController/update');//更新
            Router::post('destroy', 'LiveController/destroy');//删除
        });
        //主播
        Router::group(['prefix' => 'anchor'], function () {
            Router::get('list', 'AnchorController/list');//列表
            Router::get('info', 'AnchorController/info');//详情
            Router::post('update', 'AnchorController/update');//更新
            Router::post('destroy', 'AnchorController/destroy');//删除
        });
    });

    //内容
    Router::group(['namespace' => 'Content'], function () {
        //楼凤
        Router::group(['prefix' => 'whore'], function () {
            Router::get('category', 'WhoreController/category');//分类
            Router::get('list', 'WhoreController/list');//列表
            Router::get('info', 'WhoreController/info');//详情
            Router::get('tag', 'WhoreController/tag');//标签和项目
            Router::post('update', 'WhoreController/update');//更新
            Router::post('lock', 'WhoreController/lock');//锁定
            Router::post('active', 'WhoreController/active');//激活
            Router::post('destroy', 'WhoreController/destroy');//删除
        });
        //短篇小说
        Router::group(['prefix' => 'conte'], function () {
            Router::get('list', 'ConteController/list');//列表
            Router::post('update', 'ConteController/update');//更新
            Router::post('destroy', 'ConteController/destroy');//删除
        });
        //性闻
        Router::group(['prefix' => 'article'], function () {
            Router::get('list', 'ArticleController/list');//列表
            Router::post('update', 'ArticleController/update');//更新
            Router::post('destroy', 'ArticleController/destroy');//删除
        });

        //长篇小说
        Router::group(['prefix' => 'novel'], function () {
            Router::get('list', 'NovelController/list');//列表
            Router::post('update', 'NovelController/update');//更新
            Router::post('destroy', 'NovelController/destroy');//删除
            Router::get('chapter', 'NovelController/chapter');//章节
            Router::post('chapter/update', 'NovelController/chapterUpdate');//章节更新
            Router::post('chapter/destroy', 'NovelController/chapterDestroy');//章节删除
        });

        //图片
        Router::group(['prefix' => 'image'], function () {
            Router::get('list', 'ImageController/list');//列表
            Router::post('update', 'ImageController/update');//更新
            Router::post('destroy', 'ImageController/destroy');//删除
        });

        //漫画
        Router::group(['prefix' => 'comics'], function () {
            Router::get('list', 'ComicsController/list');//列表
            Router::post('update', 'ComicsController/update');//更新
            Router::post('destroy', 'ComicsController/destroy');//删除
            Router::get('chapter', 'ComicsController/chapter');//章节
            Router::post('chapter/update', 'ComicsController/chapterUpdate');//章节更新
            Router::post('chapter/destroy', 'ComicsController/chapterDestroy');//章节删除
        });

        //有声小说
        Router::group(['prefix' => 'sound'], function () {
            Router::get('list', 'SoundController/list');//列表
            Router::post('update', 'SoundController/update');//更新
            Router::post('destroy', 'SoundController/destroy');//删除
            Router::get('chapter', 'SoundController/chapter');//章节
            Router::post('chapter/update', 'SoundController/chapterUpdate');//章节更新
            Router::post('chapter/destroy', 'SoundController/chapterDestroy');//章节删除
        });

    });

    //其他
    Router::group(['namespace' => 'Other'], function () {
        //类目
        Router::group(['prefix' => 'category'], function () {
            Router::get('list', 'CategoryController/list');//列表
            Router::get('type', 'CategoryController/type');//类型
            Router::get('select', 'CategoryController/select');//分类
            Router::post('update', 'CategoryController/update');//更新
            Router::post('destroy', 'CategoryController/destroy');//删除
        });
        Router::group(['prefix' => 'handle'], function () {
            Router::post('target', 'HandleController/target');//更新视频连接
            Router::post('thumb', 'HandleController/thumb');//更新视频封面
            Router::post('image', 'HandleController/image');//更新图片地址
            Router::get('video', 'HandleController/video');//获取视频采集
            Router::post('update', 'HandleController/update');//获取视频采集
        });
        //活动
        Router::group(['prefix' => 'doing'], function () {
            Router::get('list', 'DoingController/list');//列表
            Router::post('update', 'DoingController/update');//更新
            Router::post('lock', 'DoingController/lock');//锁定
            Router::post('active', 'DoingController/active');//激活
            Router::post('destroy', 'DoingController/destroy');//删除
            Router::get('whore', 'DoingController/whore');//白嫖
        });

        //广告
        Router::group(['prefix' => 'ad'], function () {
            Router::get('list', 'AdController/list');//列表
            Router::post('update', 'AdController/update');//更新
            Router::post('destroy', 'AdController/destroy');//删除
        });
        //游戏
        Router::group(['prefix' => 'game'], function () {
            Router::get('list', 'GameController/list');//列表
            Router::post('update', 'GameController/update');//更新
            Router::post('lock', 'GameController/lock');//锁定
            Router::post('active', 'GameController/active');//激活
            Router::post('destroy', 'GameController/destroy');//删除
        });
        //应用
        Router::group(['prefix' => 'apply'], function () {
            Router::get('list', 'ApplyController/list');//列表
            Router::post('update', 'ApplyController/update');//更新
            Router::post('lock', 'ApplyController/lock');//锁定
            Router::post('active', 'ApplyController/active');//激活
            Router::post('destroy', 'ApplyController/destroy');//删除
        });

        //上传
        Router::group(['prefix' => 'upload'], function () {
            Router::post('image', 'UploadController/image');//图片
            Router::post('file', 'UploadController/file');//文件
        });
    });

});
