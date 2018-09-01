<?php
/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2018/8/20
 * Time: 22:14
 */

namespace api\common\model;


use api\common\map\ErrorCodeMap;
use api\common\map\UserRoleMap;

class RoleUserModel extends CommonModel
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
        $role_type = implode(',',UserRoleMap::getAllRoleIds());

        $rule = array(
            'user_id' => 'require|number',
            'role_id' => "require|number|in:{$role_type}",
        );

        $is_validate = $this->validate($rule);
        if(!$is_validate){
            return setReturnData(ErrorCodeMap::INVALIDATE_PARAM,'','');
        }

        if($this->isExist($data['user_id'],$data['role_id'])){
            return setReturnData(ErrorCodeMap::USER_ALREADY_EXIST,'','');
        }

        return true;
    }


    private function isExist($user_id,$role_id)
    {
        $where['user_id'] = $user_id;
        $where['role_id'] = $role_id;

        return $this->where($where)->find();
    }
}