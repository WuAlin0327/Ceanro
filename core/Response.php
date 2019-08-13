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

}