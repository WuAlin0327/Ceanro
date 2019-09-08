<?php


Router::register(
    [
        // 开启app模式后定义路由的方式:app/类名/方法名
        'index/func/\d+'=>'index/index/func'
    ]
);


Router::register([
   'book/list'=>'index/book/get'
]);

Router::register([
    'user/func/\d+'=>'user/replace'
]);
Router::register([
    'user/add'=>'user/add'
]);
Router::register([
    'user/change'=>'user/change'
]);

Router::register([
    'user/remove'=>'user/rm'
]);