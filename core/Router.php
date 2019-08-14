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
        $path_info = explode('/',substr($_SERVER['PATH_INFO'],1));
        if (empty($path_info[0])){
            $path_info[0] = 'index';
        }
        self::distribution($path_info);
        include('./controller/'.$path_info[0].'.php');
        $method = get_config('method');
        $obj = new $path_info[0];
        $response = call_user_func([$obj,$method[strtolower($_SERVER['REQUEST_METHOD'])]],isset($path_info[1])?$path_info[1]:null);
        echo $response;
    }

    static function escape($str){
        $str = str_replace('/','\/',$str);
        return $str;
    }

}
