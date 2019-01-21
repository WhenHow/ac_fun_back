<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2019/1/20
 * Time: 13:24
 */

namespace app\common\model;


use api\common\map\ErrorCodeMap;

class UserTaskDetailModel extends BaseModel
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }


    public function multiAdd($data_list){

        $now = time();
        foreach ($data_list as $key => $val){
            $data = $this->data_filter($val);
            $data['create_time'] = $now;
            if(!$data){
                unset($data_list[$key]);
                continue;
            }
            $data_list[$key];
        }

        if(!$data_list){
            return setReturnData(ErrorCodeMap::INVALIDATE_PARAM,"");
        }

        $this->insertAll($data_list);
        return setReturnData(true);
    }

}