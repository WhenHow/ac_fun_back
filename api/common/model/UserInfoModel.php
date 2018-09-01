<?php
/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2018/8/20
 * Time: 21:39
 */

namespace api\common\model;
use api\common\map\ErrorCodeMap;

class UserInfoModel extends CommonModel
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    public function addOne($data){
        $is_validate = $this->isAddDataValidate($data);
        $data = $this->data_filter($data);

        if($is_validate !== true){
            return $is_validate;
        }

        $uid = $this->insertGetId($data);
        if(!$uid){
            return setReturnData(ErrorCodeMap::SYSTEM_ERROR);
        }

        return setReturnData(true,'',$uid);
    }



    private function isAddDataValidate($data){

        $rule = array(
            'user_id' => 'require|number',
            'agency_id' => 'number',
            'avatar_img_id' => 'number',
            'appid' => 'number',
            'real_name' => 'chs'
        );

        $is_validate = $this->validate($rule);
        if(!$is_validate){
            return setReturnData(ErrorCodeMap::INVALIDATE_PARAM,'','');
        }

        if($this->isUserExist($data['user_id'])){
            return setReturnData(ErrorCodeMap::USER_ALREADY_EXIST,'','');
        }

        return true;
    }


    private function isUserExist($user_id)
    {
        $where['user_id'] = $user_id;

        return $this->where($where)->find();
    }

}