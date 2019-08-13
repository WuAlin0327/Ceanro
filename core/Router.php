<?php

class Router
{

    public function distribution($path_info){
        $path = register();
        $request_path = implode('/',$path_info);
        foreach($path as $k=>$v){
            preg_match('/'.self::escape($k).'/',$request_path,$ret);
            if (!empty($ret)){
                $func = explode('/',$v);
                include('./controller/'.$func[0].'.php');
                $response = call_user_func(array_slice($func,0,2),!empty($func[2])?$func[2]:null);
                echo $response;
                exit;
            }
        }
    }

    public function main(){
        $path_info = explode('/',substr($_SERVER['PATH_INFO'],1));
        self::distribution($path_info);
        include('./controller/'.$path_info[0].'.php');
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        $response = call_user_func([$path_info[0],$method]);
        echo $response;
    }

    static function escape($str){
        $str = str_replace('/','\/',$str);
        return $str;
    }

}
