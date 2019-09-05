<?php

return[
    'http_response'=>[
        100 => 'HTTP/1.0 100 Continue',
        101 => 'HTTP/1.0 101 Switching Protocols',
        200 => 'HTTP/1.0 101 OK',
        201 => "HTTP/1.1 201 Created",
        202 => "HTTP/1.1 202 Accepted",
        203 => "HTTP/1.1 203 Non-Authoritative Information",
        204 => "HTTP/1.1 204 No Content",
        205 => "HTTP/1.1 205 Reset Content",
        206 => "HTTP/1.1 206 Partial Content",
        300 => "HTTP/1.1 300 Multiple Choices",
        301 => "HTTP/1.1 301 Moved Permanently",
        302 => "HTTP/1.1 302 Found",
        303 => "HTTP/1.1 303 See Other",
        304 => "HTTP/1.1 304 Not Modified",
        305 => "HTTP/1.1 305 Use Proxy",
        307 => "HTTP/1.1 307 Temporary Redirect",
        400 => "HTTP/1.1 400 Bad Request",
        401 => "HTTP/1.1 401 Unauthorized",
        402 => "HTTP/1.1 402 Payment Required",
        403 => "HTTP/1.1 403 Forbidden",
        404 => "HTTP/1.1 404 Not Found",
        405 => "HTTP/1.1 405 Method Not Allowed",
        406 => "HTTP/1.1 406 Not Acceptable",
        407 => "HTTP/1.1 407 Proxy Authentication Required",
        408 => "HTTP/1.1 408 Request Time-out",
        409 => "HTTP/1.1 409 Conflict",
        410 => "HTTP/1.1 410 Gone",
        411 => "HTTP/1.1 411 Length Required",
        412 => "HTTP/1.1 412 Precondition Failed",
        413 => "HTTP/1.1 413 Request Entity Too Large",
        414 => "HTTP/1.1 414 Request-URI Too Large",
        415 => "HTTP/1.1 415 Unsupported Media Type",
        416 => "HTTP/1.1 416 Requested range not satisfiable",
        417 => "HTTP/1.1 417 Expectation Failed",
        500 => "HTTP/1.1 500 Internal Server Error",
        501 => "HTTP/1.1 501 Not Implemented",
        502 => "HTTP/1.1 502 Bad Gateway",
        503 => "HTTP/1.1 503 Service Unavailable",
        504 => "HTTP/1.1 504 Gateway Time-out"
    ], // 响应状态
    'sql'=>[
        'dbms'=>'mysql',
        'host'=>'localhost', // ip
        'port'=>'3307', // 端口
        'user'=>'root', // 用户名
        'password'=>'', // 密码
        'dbname'=>'restful', // 数据库
    ],

    'db_prefix'=>'db_', // 数据库表前缀

    // 请求方法对应的处理函数
    'method'=>[
        'get'=>'get', // 获取列表,如果最后加上id,只会获取一条数据,例如:/user/1
        'post'=>'post',  // 向表中插入数据
        'delete'=>'delete', // delete请求,删除数据,需要传入id,例如:/user/1
        'put'=>'put' // put请求,更新数据，需要传入id,例如:/user/1
    ],

    // 中间件
    'middleware'=>[
        'FirstMiddleware'
    ],

    // url前缀
    'url_prefix'=>'api/',

    // 是否开启多app模式，模式是不开启，不开启的话模块功能放在controller,如果开启可以在controller中开启
    'application'=>false,

    // 是否开启强制路由匹配,也就是说路由必须匹配成功才会返回响应内容,如果不匹配则返回空
    'forced_routing'=>false,

    // 输出完整的sql语句
    'sql_debug'=>false,


];