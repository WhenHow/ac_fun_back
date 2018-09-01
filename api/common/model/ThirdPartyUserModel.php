<?php
/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2018/8/20
 * Time: 19:52
 */

namespace api\common\model;


use api\common\map\ErrorCodeMap;
use api\common\map\UserDataTypeMap;

class ThirdPartyUserModel extends CommonModel
{
    const THIRD_PARTY_WXAPP = 'wxapp';


    public function __construct($data = [])
    {
        parent::__construct($data);
    }


    public function addOne($data){
        $data = $this->data_filter($data);
        $is_validate = $this->isAddDataValidate($data);
        if($is_validate !== true){
            return $is_validate;
        }

        $data = $this->autoFillAddData($data);
        $uid = $this->insertGetId($data);
        if(!$uid){
            return setReturnData(ErrorCodeMap::SYSTEM_ERROR);
        }

        return setReturnData(true,'',$uid);
     }


    private function isAddDataValidate($data){

        $rule = array(
            'user_id' => 'require|number',
            'status' => array('in'=>[UserDataTypeMap::STATUS_NORMAL,UserDataTypeMap::STATUS_DISABLE]),
            'third_party' => array('in'=>self::THIRD_PARTY_WXAPP),
            'openid' => 'require',
            'app_id' => 'require'
        );

        $is_validate = $this->validate($rule);
        if(!$is_validate){
            return setReturnData(ErrorCodeMap::INVALIDATE_PARAM,'','');
        }

        if($this->isUserExist($data['third_party'],$data['app_id'],$data['openid'],$data['union_id'])){
            return setReturnData(ErrorCodeMap::USER_ALREADY_EXIST,'','');
        }

        return true;
    }


    private function isUserExist($third_party_type,$appid,$openid,$union_id='')
    {
        $where['third_party'] = $third_party_type;
        $where['app_id'] = $appid;
        if($union_id){
            $where['union_id'] = $union_id;
        }else{
            $where['openid'] = $openid;
        }

        return $this->where($where)->find();
    }

    private function autoFillAddData($data)
    {
        $data['create_time'] = time();
        $data['status'] = isset($data['status']) ? $data['status'] : UserDataTypeMap::STATUS_NORMAL;
        return $data;

    }

}