# 项目介绍
## 项目名:Ceanro(暂时)
![avatar](https://camo.githubusercontent.com/a72e7743f15db219a6aba534f9de456e86268dd6/68747470733a2f2f696d672e736869656c64732e696f2f62616467652f6c6963656e73652d416e74692532303939362d626c75652e7376673f7374796c653d666c61742d737175617265)
![avatar](https://img.shields.io/badge/language-PHP5.6-red.svg)
![avatar](https://img.shields.io/badge/name-Ceanro-blue.svg)

这个项目是一个遵循restful api 接口规范的php轻量级后端框架，适合小型网站以及熟悉php练手使用
## 请求流程
<img src="https://raw.githubusercontent.com/WuAlin0327/Ceanro/master/Ceanro_1.png" width="400" hegiht="313" align="center" />

## 下载:
```
git clone https://github.com/WuAlin0327/Ceanro.git
```

## 目录结构:
```
├── README.md
├── config // 配置文件目录
│   └── settings.php // 配置文件
├── controller // 控制器
│   └── user.php // 视图类
├── core
│   ├── Core.php // 被视图类继承的核心类
│   ├── DataBase.php // SQL相关的类
│   ├── Request.php // 请求相关的类
│   ├── Response.php // 响应相关的类
│   ├── Router.php // 进行路由分发的类
│   ├── Config.php // 配置文件类
│   └── common.php // 公共方法
├── middleware //中间件
│   └── FirstMiddleware.php 
├── include // 第三方模块
├── index.php // 入口函数
└── router.php // 注册路由
```

## 配置文件:config/settings.php
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
    'db_prefix'=>'db_',
    
    // 请求方法对应的处理函数
    'method'=>[
        'get'=>'get', // 获取列表,如果最后加上id,只会获取一条数据,例如:/user/1
        'post'=>'post',  // 向表中插入数据
        'delete'=>'delete', // delete请求,删除数据,需要传入id,例如:/user/1
        'put'=>'put' // put请求,更新数据，需要传入id,例如:/user/1
    ],
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

## 路由:
第一个路由:
```
// 在使用apache的时候最好是把路由绑定到index.php上
GET http://serverName/index.php
```

路由目前有两种方式:
1. 根据请求方法进行路由
2. 注册路由


### 根据请求方法进行路由
遵循restful api接口规范，一个URI对应一个资源，一个请求方式对应一个处理请求的方法:

controller/user.php
```
class user
{

    public $id = 'id';
    public $table = __CLASS__; //

    public function get($id=null){
        // get业务逻辑
    }
    public function post(){
        // post业务逻辑
    }

    public function put($id)
    {
        // put业务逻辑
    }

    public function delete($id)
    {
        // delete业务逻辑
    }
}
```
1. get请求会执行user.php中的get方法
```
GET http://serverName/user   // 获取user表中所有数据
GET http://serverName/user/1  // 获取id为1的一行数据
```


2. post请求会执行user.php中post方法
```
POST http://serverName/user
```

3. put请求会执行user.php中put方法
```
PUT http://serverName/user
```

4. delete 请求会执行user.php中delete方法
```
DELETE http://serverName/user/1
```
PS:这种方式逻辑都是可以自定义的,为了后续更好的维护代码，应遵循restful api规范，一个URI对应一个资源，在后面会介绍继承Core类对一张数据库表进行快速的增删改查
### 注册路由
```
<?php
Router::register([
    'user/func/'=>'user/replace'
]);
```
如果路由需要匹配动态参数,比如传入id,可以使用正则表达式去匹配,并且在对应方法中接收该参数
router.php
```
Router::register([
    'user/func/\d+'=>'user/replace'
]);
```
controller/user.php
```
class user extends \core\Core
{

    public $id = 'id';
    public $table = __CLASS__; //

    public function replace($id){
        // 可以拿id进行处理你的业务逻辑了
    }
}
```
## 中间件
在settings.php中可以配置中间件(middleware)，中间件可以自定义，在请求进来的时候会执行中间件中的request方法，在响应的时候会执行中间件中的response方法

例如:

settings.php
```
...
'middleware'=>[
        // 配置了一个名为FirstMiddleware的中间件
        'FirstMiddleware'
    ]
...
```

在根目录下middleware中新建FirstMiddleware.php类

middleware/FirstMiddleware.php
```
<?php
/**
 * Created by PhpStorm.
 * User: wualin
 * Date: 2019-08-18
 * Time: 20:31
 */

class FirstMiddleware
{

    /**
     * 请求进来时处理
     */
    static public function request(){

    }

    /**
     * @param string $response 响应的json字符串数据
     * @return mixed 处理后的响应数据
     */
    static public function response($response){
        // 这个中间件是如果返回的数据是json数据的话会加上url和请求方法
        $data = json_decode($response,true);
        if (!$data)return $response;
        $data['url'] = $_SERVER['PATH_INFO'];
        $data['method'] = $_SERVER['REQUEST_METHOD'];
        return json_encode($data);
    }
}
```
在这个中间件中request没有进行处理,response方法在返回的数据增加了url,method参数.

PS:中间件的作用非常大，可以在中间件中实现很多功能(例如权限控制，反爬，登陆验证，请求/响应内容处理)，但是中间件太多也会影响数据请求以及响应的时间，后续会在中间件中实现权限控制

## 控制器
### 控制器的简单使用
Ceanro的控制器是使用的类中定义方法的模式，在根目录下的controller文件夹中可以定义PHP类

在config.php中可以开启多APP模式，因为多APP模式可能存在问题，所以本文档中使用的是直接在controller文件夹下新建PHP类的方法控制
```
<?php

class user 
{
    public function get(){
        // get请求逻辑
        echo '这是一个GET请求';
    }
}
```
如果要访问我们定义的user类中的get方法，只需要访问以下链接(ServerName是你自己的域名),在路由中已经介绍了，如果没有在根目录下的router.php中定义路由，默认使用的是请求方法进行路由匹配，使用get请求访问以下链接会执行user类中的get方法
```
http://ServerName/index.php/user/
```

### 控制器继承Core类
Core类是core中的核心类，继承该类后可以对一个数据库表进行快速增删该查，Core类中默认有4个处理请求的方法:get|post|put|delete，分别实现了获取数据库表列表、新增一行数据、修改数据、删除数据
```
<?php

use core\Request;
class user extends \core\Core
{

    public $id = 'id'; // 数据库表的主键
    public $table = __CLASS__; // 数据库表名
    
}
```


# 核心类库介绍

## 请求类
Ceanro内置了一个请求类，可以通过请求类获取到对应请求的参数以及一些常用的请求头属性，请求类使用了单例模式

### 调用请求类:
```
use core\Request;
$request = Request::instance()
```

### 获取请求参数:
```
use core\Request;
use core\Config;
class user extends \core\Core
{
    public $id = 'id';
    public $table = __CLASS__; //
    public $foreign_key = [
        'book_id'=>'book'
    ];
    public function replace($id){
      
        echo Request::instance()->_get('name'); // 获取get请求参数
        // 获取get请求参数的简写方法,_get('name') == Request::instance()->_get('name');
        echo _get('name');

        echo Request::instance()->_post('username'); // 获取post请求参数
        echo _post('username');

        echo Request::instance()->_put('password'); // 获取put请求参数
        echo _put('password');
        
        return json(['data'=>'1']);
    }

}
```

### 常用的请求头属性
```
use core\Request;
use core\Config;
class user extends \core\Core
{
    public $id = 'id';
    public $table = __CLASS__; //
    public $foreign_key = [
        'book_id'=>'book'
    ];
    public function replace($id){
        echo Request::instance()->ip; // 请求ip
        echo Request::instance()->domain; // 完整域名,http://xxx.xx
        echo Request::instance()->method; // 请求方法  
        return json(['data'=>'1']);
    }

}

```
