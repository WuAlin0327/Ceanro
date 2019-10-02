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

    public $response;
    public $data_type;
    private static $instance = null;
    private function __construct(){}
    private function __clone(){}

    /**
     * @return Response|null
     */
    public static function instance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param $data
     * @param null $callback
     * @param int $status
     * @return string
     */
    public function json($data,$callback=null,$status=200){
        $str = json_encode($data);
        if (!empty($callback)){
            $str = $callback.'('.$str.')';
        }
        self::$instance->data_type = 'json';
        return $str;
    }

    /**
     * @param $data
     * @param $status
     * @param string $wrap
     * @return string
     */
    public function xml( $data,$status,$wrap= 'xml' ){
        $str = to_xml($data,$wrap);
        $str = '<!--?xml version="1.0"?--> '.$str;
        self::$instance->data_type = 'xml';
        return $str;
    }

}