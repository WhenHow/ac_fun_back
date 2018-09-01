<?php
/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2018/8/8
 * Time: 17:51
 */
namespace api\common\logic;

use api\common\map\DeviceTypeMap;
use api\common\map\ErrorCodeMap;
use api\common\model\RoleUserModel;
use api\common\model\ThirdPartyUserModel;
use api\common\model\User;
use api\common\model\UserInfoModel;
use api\common\model\UserModel;
use think\Db;
class UserLogic extends BaseLogic
{
    /**
     * 更新并返回用户的token
     * @param $user_id
     * @param $device_type
     * @return bool|string
     */
    public function updateUserToken($user_id,$device_type)
    {
        if(!DeviceTypeMap::isTypeValidate($device_type)){
            return false;
        }

        $user_id = intval($user_id);
        if($user_id <= 0){
            return false;
        }

        return cmf_generate_user_token($user_id,$device_type);
    }


    /**
     * 执行登录操作
     * @param $user_id
     * @param $device_type
     * @return bool|string
     */
    public function doLogin($user_id,$device_type)
    {
        //更新并获得token
        $token = $this->updateUserToken($user_id,$device_type);
        if(!$token){
            return false;
        }
        $data['token'] = $token;
        //获得用户相关信息
        $data['user_roles'] = $this->getUserRoles($user_id);
        $data['third_user_info'] = $this->getThirdUserInfo($user_id,['openid','union_id','third_party'],$device_type);
        $data['user_base'] = $this->getUserInfoById($user_id,['avatar','mobile','sex','user_nickname','user_status','user_type']);
        $data['user_extra'] = $this->getUserExtraInfo($user_id,['address','agency_id','avatar_img_id','contact_tel','job_title','real_name','section_name','unit_id']);

        return $data;
    }


    /**
     * 获得用户信息
     * @param $id
     * @return array|false|null|PDOStatement|string|\think\Model
     */
    public function getUserInfoById($id,$fields = "*")
    {
        $id = intval($id);
        if($id <= 0){
            return null;
        }

        $user_map['id'] = $id;
        return Db::name('user')->where($user_map)->field($fields)->find();
    }

    public function getThirdUserInfo($base_id,$fields = "*",$third_party_type = ''){
        $id = intval($base_id);
        if($id <= 0){
            return null;
        }
        $user_map['user_id'] = $id;
        if($third_party_type){
            $user_map['third_party'] = $third_party_type;
        }

        return Db::name('ThirdPartyUser')->field($fields)->where($user_map)->find();
    }

    public function getUserExtraInfo($base_id,$fields = "*"){
        $id = intval($base_id);
        if($id <= 0){
            return null;
        }

        $user_map['user_id'] = $id;
        return Db::name('UserInfo')->field($fields)->where($user_map)->find();
    }


    /**
     * 获得用户角色
     * @param $id
     * @return false|null|PDOStatement|string|\think\Collection
     */
    public function getUserRoles($id)
    {
        $id = intval($id);
        if($id <= 0){
            return null;
        }

        $role_map['user_id'] = $id;
        $ret = Db::name('RoleUser')->where($role_map)->select();
        return $ret ? reduce_array_dimen($ret,'role_id') : [];
    }


    /**
     * 第三方用户是否存在
     * @param $app_id
     * @param $open_id
     * @param $union_id
     * @return array|bool|false|\PDOStatement|string|\think\Model
     */
    public function isWxUserExist($app_id,$open_id,$union_id)
    {
        if(!$open_id && !$union_id)
        {
            return false;
        }

        $map['app_id'] = $app_id;
        if($union_id){
            $map['union_id'] = $union_id;
        }
        else{
            $map['openid'] = $open_id;
        }

        $ret = Db::name('ThirdPartyUser')->where($map)->find();
        if(!$ret){
            return false;
        }

        $data['third_user_info'] = $ret;
        $data['user_info'] = $this->getUserInfoById($ret['user_id']);
        return $ret ? $data : false;
    }

    public function getAgencyId($appid){
        $where['wxapp_appid'] = $appid;
        $where['is_flag'] = 1;
        $where['is_using'] = 1;
        $ret = Db::name('Agency')->where($where)->find();
        if($ret){
            return $ret['id'];
        }
        return 0;
    }

    public function addUser($user_base,$user_info,$role_id,$third_user = null){
        Db::startTrans();

        $user_base_ret = $this->addUserBase($user_base);
        if($user_base_ret['code']!=ErrorCodeMap::SUCCESS){
            Db::rollback();
            return $user_base_ret;
        }
        $user_base_id = $user_base_ret['data'];

        $user_info_ret = $this->addUserInfo($user_info,$user_base_id);
        if($user_info_ret['code']!=ErrorCodeMap::SUCCESS){
            Db::rollback();
            return $user_info_ret;
        }

        $third_user_ret = $this->addThirdUser($third_user,$user_base_id);
        if($third_user_ret['code']!=ErrorCodeMap::SUCCESS){
            Db::rollback();
            return $third_user_ret;
        }

        $role_ret = $this->addRoleUser($role_id,$user_base_id);
        if($role_ret['code']!=ErrorCodeMap::SUCCESS){
            Db::rollback();
            return $role_ret;
        }

        cmf_generate_user_token($user_base_id, $third_user['third_party']);
        Db::commit();

        return $user_base_ret;
    }

    private function addUserBase($user_data){
        $model = new UserModel();
        return $model->addOne($user_data);
    }

    private function addUserInfo($data,$user_id){
        $data['user_id'] = $user_id;
        $model = new UserInfoModel();
        return $model->addOne($data);
    }

    private function addThirdUser($data,$user_id){
        $data['user_id'] = $user_id;
        $model = new ThirdPartyUserModel();
        return $model->addOne($data);
    }

    private function addRoleUser($role_id,$user_id){
        $model = new RoleUserModel();
        return $model->addOne(['role_id'=>$role_id,'user_id'=>$user_id]);
    }


}