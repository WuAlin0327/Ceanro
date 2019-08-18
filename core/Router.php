<?php

class Router
{

    /**
     * 路由分发
     * @param $path_info
     */
    public static function distribution($path_info){
        $path = register();
        $request_path = implode('/',$path_info);
        foreach($path as $k=>$v){
            preg_match('/'.self::escape($k).'/',$request_path,$ret);
            if (!empty($ret)){
                $func = explode('/',$v);
                include('./controller/'.$func[0].'.php');
                $obj = new $func[0];
                $response = call_user_func(array($obj,$func[1]),!empty($path_info[2])?$path_info[2]:null);
                echo $response;
                exit;
            }
        }
    }

    /**
     * 入口函数
     */
    public static function main(){
        $middleware = get_config('middleware');

        // 请求中间件处理
        foreach($middleware as $v){
            require_once ROOT_PATH.'/middleware/'.$v.'.php';
            call_user_func([$v,'request']);
        }
        $path_info = explode('/',substr($_SERVER['PATH_INFO'],1));
        if (empty($path_info[0])){
            $path_info[0] = 'index';
        }
        self::distribution($path_info);
        include('./controller/'.$path_info[0].'.php');
        $method = get_config('method');
        $obj = new $path_info[0];
        // $response 为json字符串或者xml字符串，如果需要到中间件中进行处理的话需要先将字符串转成数据进行处理
        $response = call_user_func([$obj,$method[strtolower($_SERVER['REQUEST_METHOD'])]],isset($path_info[1])?$path_info[1]:null);

        // 响应中间件处理
        foreach($middleware as $v){
            $response = call_user_func([$v,'response'],$response);
        }
        echo $response;
    }

    static function escape($str){
        $str = str_replace('/','\/',$str);
        return $str;
    }

}
