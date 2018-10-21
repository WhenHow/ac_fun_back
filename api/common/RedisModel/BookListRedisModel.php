<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2018/10/2
 * Time: 23:28
 */

namespace api\common\RedisModel;


class BookListRedisModel extends BaseRedisModel
{
    const BOOK_LIST_PREFIX = "BOOK_LIST_ID:";

    public function __construct()
    {
        parent::__construct();
    }


    public function getBookList($book_id){
        $key_name = $this->getBookListKey($book_id);
        $ret = $this->get($key_name,null);
        if($ret){
            $ret = json_decode($ret,true);
        }

        return $ret;
    }

    public function setBookList($book_id,$book_list){
        $key_name = $this->getBookListKey($book_id);
        return $this->set($key_name,json_encode($book_list));
    }

    private function getBookListKey($book_id){
        return self::BOOK_LIST_PREFIX.$book_id;
    }
}