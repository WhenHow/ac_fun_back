<?php
/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2018/8/20
 * Time: 14:23
 */

namespace api\common\model;


use api\common\map\ErrorCodeMap;
use api\common\map\UserDataTypeMap;

class UserModel extends CommonModel
{


    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    public function addOne($data){
        $data = $this->data_filter($data);

        $is_data_validate = $this->isAddDataValidate($data);
        if($is_data_validate !== true){
            return $is_data_validate;
        }

        $data = $this->autoFillAddData($data);
        $uid = $this->insertGetId($data);
        if(!$uid){
            return setReturnData(ErrorCodeMap::SYSTEM_ERROR);
        }

        return setReturnData(true,'',$uid);
    }

    private function isAddDataValidate($data)
    {
        $user_type_str = UserDataTypeMap::USER_TYPE_ADMIN.",".UserDataTypeMap::USER_TYPE_NORMAL;
        $sex_str = UserDataTypeMap::SEX_MALE.",".UserDataTypeMap::SEX_FEMALE;

        $rule = array(
            'user_type' => "require|in:{$user_type_str}",
            'sex' => array('in'=>$sex_str),
        );

        $message = array(
            'user_type.require' =>'用户类型不能为空',
            'user_type.in' => '用户类型不合法',
            'sex.in' => '性别不合法'
        );

        $is_validate = $this->validate($rule,$message);
        if(!$is_validate){
            return setReturnData(ErrorCodeMap::INVALIDATE_PARAM,'','');
        }
        //检查唯一性
        $is_user_exist = $this->isUserExist($data);
        if($is_user_exist){
            return $is_user_exist;
        }

        return true;
    }


    private function isUserExist($data,$filter_id = 0){
        $map = array();

        $unique_index = ['mobile'=>ErrorCodeMap::MOBILE_EXIST, 'user_login'=>ErrorCodeMap::LOGIN_NAME_EXIST, 'user_email'=>ErrorCodeMap::EMAIL_EXIST];
        foreach ($unique_index as $index => $error_code){
            if(key_exists($index,$data)){
                $map[$index] = $data[$index];
            }
        }

        if(!$map){
            return false;
        }

        $map['id'] = array('neq',$filter_id);
        $ret = $this->where($map)->find();
        if(!$ret){
            return false;
        }

        foreach ($unique_index as $index => $error_code){
            if(key_exists($index,$data) && $ret[$index] == $data[$index]){
                return setReturnData($error_code,'','');
            }
        }

        return false;
    }


    private function autoFillAddData($data)
    {
        $data['create_time'] = time();
        $data['user_status'] = isset($data['user_status']) ? $data['user_status'] : UserDataTypeMap::STATUS_NORMAL;
        $data['user_pass'] = isset($data['user_pass']) ? cmf_password($data['user_pass']) : "";
        $data['last_login_ip'] = get_client_ip(0,true);
        return $data;

    }
}
