<?php


class user extends \core\Core
{

    public $id = 'id';
    public $table = __CLASS__; //
    public $foreign_key = [
        'book_id'=>'book'
    ];


    public function replace($id){
        $callback = isset($_GET['callback'])?$_GET['callback']:'';
//        $data = $this->find($id);
        return json($this->fields,$callback,'201');
    }
}