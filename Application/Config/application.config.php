<?php

return [
    //数据库链接信息
    'db'=>[
        'host'=>'127.0.0.1',
        'user'=>'root',
        'password'=>'12345',
        'dbname'=>'new_shop',
        'charset'=>'utf8',
        'port'=>3306,
        'prefix'=>''
    ],
    //默认的url访问控制参数
    'default'=>[
        'default_platform'=>'Admin',
        'default_controller'=>'Admin',
        'default_action'=>'index'
    ],
    //上传文件配置
    'upload'=>[
        'max_size'=>1024*1024*2,
        'allow_types'=>['image/jpeg','image/png','image/gif','image/bmp']
    ]
];