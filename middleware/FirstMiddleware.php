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