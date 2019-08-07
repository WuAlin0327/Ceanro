<?php
/**
 * Created by PhpStorm.
 * User: wualin
 * Date: 2019-08-07
 * Time: 23:12
 */
use core\Core;
class Response
{

    public function __construct($data,$type='json',$callback=null,$status=200)
    {
        $this->data = call_user_func([__CLASS__,$type],$data,$callback,$status);
    }

    public function json($data,$callback=null,$status=200){
        $str = json_encode($data);
        if (!empty($callback)){
            $str = $callback.'('.$str.')';
        }
        $obj = new core\Core();
        $config = $obj->config();
        header($config['http_response'][$status]);
        return $str;
    }
}