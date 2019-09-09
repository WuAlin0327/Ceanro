<?php
/**
 * Created by PhpStorm.
 * User: wualin
 * Date: 2019-08-18
 * Time: 20:31
 */

use core\Response;
class FirstMiddleware
{

    /**
     * 请求进来时处理
     */
    static public function request(){

    }

    /**
     * @param \core\Response $response 响应类
     * @return \core\Response mixed 处理后的响应数据
     */
    static public function response(){
        $data = [];
        $response = Response::instance();
        // 这个中间件是如果返回的数据是json数据的话会加上url和请求方法
        if ($response->data_type == 'json'){
            $response->response = json_decode($response->response,true);
            $data['url'] = $_SERVER['PATH_INFO'];
            $data['method'] = $_SERVER['REQUEST_METHOD'];
            $data['data'] = $response->response;
            $response->response = json_encode($data);
        }
        return $response;
    }
}