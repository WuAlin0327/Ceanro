<?php
/**
 * Created by PhpStorm.
 * User: wualin
 * Date: 2019-08-13
 * Time: 20:26
 */



function get_config($name=null){
    $config = require ROOT_PATH . '/config/settings.php';
    if (!empty($name)){

        $config = $config[$name];
    }
    return $config;
}


/**
 * 返回Json数据
 * @param array $data 需要打包的数据
 * @param null $callback 回调参数
 * @param int $status 响应状态
 * @return false|string 返回json字符串
 */
function json($data,$callback=null,$status=200){
    $data['url'] = $_SERVER['PATH_INFO'];
    $data['method'] = $_SERVER['REQUEST_METHOD'];
    $str = json_encode($data);
    if (!empty($callback)){
        $str = $callback.'('.$str.')';
    }
    $config = get_config('http_response');
    header($config[$status]);
    return $str;
}

function hasIndex( $arr ){
    return array_keys($arr) !== range(0, count($arr) - 1);
}


/**
 * 返回xml数据
 * @param array $data 需要打包的数据
 * @param string $wrap
 * @return string xml格式数据
 */
function xml( $data, $wrap= 'xml' ){
    $data['url'] = $_SERVER['PATH_INFO'];
    $data['method'] = $_SERVER['REQUEST_METHOD'];
    $str = "<{$wrap}>";
    if( is_array( $data ) ){
        if( hasIndex( $data ) ){
            foreach( $data as $k=>$v ){
                $str .= xml( $v, $k );
            }
        }else{
            foreach( $data as $v ){
                foreach( $v as $k1=>$v1 )
                    $str .= xml( $v1, $k1 );
            }
        }
    }else
        $str .= $data;
    $str .= "</{$wrap}>";
    return $str;
}