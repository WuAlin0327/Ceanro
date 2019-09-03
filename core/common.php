<?php
/**
 * Created by PhpStorm.
 * User: wualin
 * Date: 2019-08-13
 * Time: 20:26
 */

use core\Request;
use core\Config;
function get_config($name=null){
    $config_obj = Config::instance();
    if (!empty($name)){

        $config = $config_obj->get($name);
    }else{
        $config =$config_obj->config;
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

function get_put(){
    try{
        $putData = file_get_contents("php://input");
        $resultData = json_decode($putData,true);
        if(is_array($resultData)){
            //解析IOS提交的PUT数据
            return $resultData;
        }
        if(!strstr($putData,"\r\n")){
            //解析本地测试工具提交的PUT数据
            parse_str($putData,$putData);
            return $putData;
        }
        //解析PHP CURL提交的PUT数据
        $putData = explode("\r\n",$putData);
        $resultData = [];
        foreach($putData as $key=>$data){
            if(substr($data,0,20) == 'Content-Disposition:'){
                preg_match('/.*\"(.*)\"/',$data,$matchName);
                $resultData[$matchName[1]] = $putData[$key+2];
            }
        }
        return $resultData;
    }catch (Exception $e){
        return [];
    }
}

/**
 * 根据字符串导入php文件
 * @param string $dir path.dir
 */
function import($dir){
    list($path,$dirname) = explode('.',$dir);
    require_once ROOT_PATH.'/'.$path.'/'.$dirname.'.php';
}


/**
 * 快速方法
 */
function _get($key){
    return Request::instance()->_get($key);
}

function _post($key){
    return Request::instance()->_post($key);
}

function _put($key){
    return Request::instance()->_put($key);
}

function request(){
    return Request::instance();
}