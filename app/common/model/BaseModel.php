<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2018/9/22
 * Time: 16:12
 */
namespace app\common\model;


use think\Model;

class BaseModel extends Model
{
    protected $table_name_str;

    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    public function data_filter($data=null){
        if($data==null){
            $data=input("post.");
        }
        $new_data=[];

        $field = $this->getTableInfo($this->table_name_str,'fields');

        foreach($field as $f){
            foreach($data as $key => $value){
                if($f==$key){
                    $new_data[$f]=$value;
                }
            }
        }
        return $new_data;
    }

    protected function same_data_filter($new_data,$old_data){
        $data = array();
        foreach ($old_data as $key => $val){
            if(key_exists($key,$new_data) && $val != $new_data[$key]){
                $data[$key] = $new_data[$key];
            }
        }
        return $data;
    }
}