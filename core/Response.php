<?php
/**
 * Created by PhpStorm.
 * User: wualin
 * Date: 2019-08-07
 * Time: 23:12
 */
namespace core;
class Response
{
    private static $instance = null;
    private function __construct(){}
    private function __clone(){}
    public static function instance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function json($data,$callback=null,$status=200){
        $str = json_encode($data);
        if (!empty($callback)){
            $str = $callback.'('.$str.')';
        }
        $config = get_config('http_response');
        header($config[$status]);
        return $str;
    }

    public function xml( $data,$status,$wrap= 'xml' ){
        header(get_config('http_response')[$status]);
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

}