<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2019/1/20
 * Time: 9:20
 */

namespace app\common\model;


use api\common\map\ErrorCodeMap;

class TaskLogModel extends BaseModel
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }


    public function addOne($data){
        $now = time();
        $time_detail = getDateDetail($now);
        $data = array_merge($data,$time_detail);
        $data = $this->data_filter($data);
        if(!$data){
            return setReturnData(ErrorCodeMap::INVALIDATE_PARAM,"");
        }

        $data['create_time'] = $now;
        $data['create_date'] = date("Y-m-d",$now);
        $ret = $this->insertGetId($data);
        if($ret===false){
            return setReturnData(ErrorCodeMap::SYSTEM_ERROR,"系统异常");
        }

        return setReturnData(true,"",$ret);
    }

    public function setLogData($user_id,$word_count,$book_id,$begin_word_id,$end_word_id,$begin_book_map_id,$end_book_map_id,$real_word_num){
        $this->user_id = $user_id;
        $this->word_count = $word_count;
        $this->book_id = $book_id;
        $this->begin_word_id = $begin_word_id;
        $this->end_word_id = $end_word_id;
        $this->begin_book_map_id = $begin_book_map_id;
        $this->end_book_map_id = $end_book_map_id;
        $this->real_word_num = $real_word_num;
    }

    public function getLogData(){
        return $this->data;
    }


}