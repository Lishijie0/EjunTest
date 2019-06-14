<?php

return [
    //开启redis扩展
    'cache'                  => [
        // 驱动方式
        'type'   => 'redis',
        'path'   => CACHE_PATH,
        // 'host'   => '127.0.0.1',
        // 'port'   => '6379',
        // 'password' => 'eboss',
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],
    /*微信公众号配置*/
    'appId'=>'wxbee928eab0ee939f',
    'appSecret'=>'80be089e40474374a986d6b8b56b03c8',
    
    //默认错误跳转对应的模板文件
    'dispatch_error_tmpl' => 'default/base/dispatch_jump',
    //默认成功跳转对应的模板文件
    'dispatch_success_tmpl' => 'default/base/dispatch_jump',
    
    
    'session' => [
        'id' => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix' => 'home',
        // 驱动方式 支持redis memcache memcached
        'type' => '',
        // 是否自动开启 SESSION
        'auto_start' => true,
    ],
    /*   //图片路径
     'IMAGE_URL' => [
        0 =>'http://hz.ebosserp.com/eboss_yun/server',  //图片访问 网络地址
        2 =>'http://hz.ebosserp.com/v2/server',  //图片访问 网络地址
        3 =>'http://hz.ebosserp.com/v3/server',  //图片访问 网络地址
    ],*/    
    'IMAGE_URL'  => 'http://116.ebosserp.com/eboss_yun/server',  //图片访问 网络地址  http://116.90.87.49/eboss_yun/server
	'serverInfo' => [
		0 => [
			'server' => '116.90.87.49',
			'appid'	=> '160101',
			'appkey' => '2E496EB1482E453A97570C705456834A',
			'user' => '001',
			'userpwd' => '001'
		],        
        1 => [
            'server' => 'yun4.ebosserp.com',
            'appid' => '181220',
            'appkey' => '348DDF7BD61A4623A0A45F35D00C1864',
            'user' => '000',
            'userpwd' => '654321'
        ],
        2 => [
            'server' => 'yun4.ebosserp.com',
            'appid' => '160101',
            'appkey' => '2E496EB1482E453A97570C705456834A',
            'user' => '000',
            'userpwd' => 'Burgeon@123'
        ],
        3 => [
            'server' => 'yun4.ebosserp.com',
            'appid' => '181224',
            'appkey' => '042A2179787B47DB9E88CE72883F855A',
            'user' => '000',
            'userpwd' => '888888'
        ],
        4 => [
            'server' => 'yun4.ebosserp.com',
            'appid' => '160209',
            'appkey' => '1F3D5EF5CABD44BDA816DD5348D49CAF',
            'user' => '000',
            'userpwd' => '000'
        ],
        5 => [
            'server' => 'hnzz.ebosserp.com/v4',
            'appid' => '190102',
            'appkey' => 'D5808FEBD1EB4BD3A67A1562E78070FF',
            'user' => '000',
            'userpwd' => '888888'
        ],
	],
    'yun4' => 'oracle://eboss:abc123@yun4.ebosserp.com:1521/orcl',
    '116' => 'oracle://eboss:abc123@116.90.87.49:1521/orcl',
    'hz6' => 'oracle://eboss6:abc123@116.62.65.161:1521/orcl',
];
