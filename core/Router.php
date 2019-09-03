<?php

class Router
{
    static $register_url = [];

    // 后续可以实现app前缀和模块前缀修改
    static $app_prefix = [];

    static $module_prefix = [];


    public static function register($url){
        self::$register_url = $url;
        foreach($url as $k=>$v){
            self::$register_url[$k]=$v;
        }
    }


    /**
     * 未开启多App模式的路由分发功能
     * @param array $path_info
     */
    public static function distribution($path_info){

        $request_path = implode('/',$path_info);

        foreach(self::$register_url as $k=>$v){
            preg_match('/'.self::escape($k).'/',$request_path,$ret);
            if (!empty($ret)){
                $func = explode('/',$v);
                require_once './controller/'.$func[0].'.php';
                $obj = new $func[0];
                $response = call_user_func(array($obj,$func[1]),!empty($path_info[2])?$path_info[2]:null);
                return $response;
            }
        }
        return null;
    }

    /**
     * 开启多App模的路由分发功能
     */
    public static function application ($path_info){
//        echo json(self::$register_url);
//        echo json($path_info);
//        list($app,$class,$func) = $path_info;
        $request_path = implode('/',$path_info);

        // 对路由进行匹配
        foreach(self::$register_url as $k=>$v){
            preg_match('/'.self::escape($k).'/',$request_path,$ret);
            if (!empty($ret)){
                list($app,$class,$func) = explode('/',$v);
                require_once ROOT_PATH.'/controller/'.$app.'/'.$class.'.php';
                $obj = new $class;
                $response = call_user_func(array($obj,$func),!empty($path_info[2])?$path_info[2]:null);
                return $response;
            }
        }
        return null;
    }

    /**
     * 入口函数
     */
    public static function main(){
        // 请求中间件处理
        self::request_middleware();
        $url_prefix = get_config('url_prefix');
        $path = str_replace($url_prefix,'',substr($_SERVER['PATH_INFO'],1));

        $path_info = explode('/',$path);
        if (empty($path_info[0])){
            $path_info[0] = 'index';
        }
        $method = get_config('method');

        // 判断是否开启多app模式
        if (get_config('application')){
            $response = self::application($path_info);
            if (!$response && !get_config('forced_routing')){

                require_once ROOT_PATH.'/controller/'.$path_info[0].'/'.$path_info[1].'.php';
                $obj = new $path_info[1];
                $response = call_user_func([$obj,$method[strtolower($_SERVER['REQUEST_METHOD'])]],isset($path_info[2])?$path_info[2]:null);
            }
        }else{
            $response = self::distribution($path_info);
            if (!$response && !get_config('forced_routing')){
                require_once ROOT_PATH.'/controller/'.$path_info[0].'.php';
                $obj = new $path_info[0];
                // $response 为json字符串或者xml字符串，如果需要到中间件中进行处理的话需要先将字符串转成数据进行处理
                $response = call_user_func([$obj,$method[strtolower($_SERVER['REQUEST_METHOD'])]],isset($path_info[1])?$path_info[1]:null);
            }
        }

        // 响应中间件处理
        self::response_middleware($response);
        echo $response;

    }

    static function escape($str){
        $str = str_replace('/','\/',$str);
        return $str;
    }

    /**
     * @param array $middleware 中间件
     * @param string $response 响应的json字符串内容
     */
    static function response_middleware(&$response){
        $middleware = get_config('middleware');
        foreach($middleware as $v){
            $response = call_user_func([$v,'response'],$response);
        }
    }

    static function request_middleware(){
        $middleware = get_config('middleware');
        foreach($middleware as $v){
            import('middleware.'.$v);
            call_user_func([$v,'request']);
        }
    }
}
