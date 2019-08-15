### 项目名:restful(暂时)
![avatar](https://img.shields.io/badge/donate-paypal-blue.svg?style=flat-square)
![avatar](https://camo.githubusercontent.com/a72e7743f15db219a6aba534f9de456e86268dd6/68747470733a2f2f696d672e736869656c64732e696f2f62616467652f6c6963656e73652d416e74692532303939362d626c75652e7376673f7374796c653d666c61742d737175617265)
### 目录结构:
```
├── README.md
├── config // 配置文件目录
│   └── settings.php // 配置文件
├── controller // 控制器
│   └── user.php // 视图类
├── core
│   ├── Core.php // 被视图类继承的核心类
│   ├── MySql.php // MySQL相关的类
│   ├── Request.php // 请求相关的类
│   ├── Response.php // 响应相关的类
│   ├── Router.php // 进行路由分发的类
│   └── common.php // 公共方法
├── include // 第三方模块
├── index.php // 入口函数
└── router.php // 注册路由
```

### 配置文件:config/settings.php
```
return[

    // 响应状态
    'http_response'=>[
        100 => 'HTTP/1.0 100 Continue',
        200 => 'HTTP/1.0 101 OK',
        201 => "HTTP/1.1 201 Created",
        202 => "HTTP/1.1 202 Accepted",
        ...

    ], 
    
    'mysql'=>[
        // ip 
        'host'=>'localhost',
        
        // 端口 
        'port'=>'3306',
        
        // 用户名
        'user'=>'root',
        
        // 密码 
        'password'=>'', 
        
        // 数据库名
        'database'=>'restful', 
    ],
    
    // 数据库表前缀
    'db_prefix'=>'db_' 
];
```
如果需要使用配置settings.php中的配置可以使用公共方法调用:
```
$config = get_config()
```
也可以传入$name参数取出对应的配置,如取出mysql的配置:
```
$mysql = get_config('mysql')
```

### 路由:
未完待续...
