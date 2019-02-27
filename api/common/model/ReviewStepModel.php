<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2019/2/10
 * Time: 13:20
 */

namespace api\common\model;


use app\common\model\BaseModel;

class ReviewStepModel extends BaseModel
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }


    public function getAll(){
        $where['is_using'] = 1;
        $ret = $this->where($where)->field("*")->order('day_span asc')->select();
        return $ret ? $ret->toArray() : null;
    }


    public function getNextStep($current_day,$all_step=null){
        $all_step = $all_step ? $all_step : $this->getAll();
        $key = array_column_search($all_step,'day_span',$current_day);
        if($key === false){
            return false;
        }

        return isset($all_step[$key+1]) ? $all_step[$key+1] : false;
    }

    public function getNextStepById($id,$all_step = null){
        $all_step = $all_step ? $all_step : $this->getAll();
        $key = array_column_search($all_step,'id',$id);
        if($key === false){
            return false;
        }

        return isset($all_step[$key+1]) ? $all_step[$key+1] : false;
    }

    public function getStepByOrderNumber($number,$all_step=null){
        $all_step = $all_step ? $all_step : $this->getAll();
        return isset($all_step[$number]) ? $all_step[$number] : false;
    }

}