<?php
/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2018/8/8
 * Time: 10:08
 */

namespace api\wxapp\controller;

use api\common\logic\UserLogic;
use api\common\map\ErrorCodeMap;
use api\common\service\WxService;
use cmf\controller\RestBaseController;
use think\Db;
use think\Validate;
use wxapp\aes\ErrorCode;

class BaseController extends RestBaseController
{
    /**
     * 获得小程序header信息
     * @param null $request
     * @return mixed
     */

    protected $current_agency_id = 0;
    protected $current_user_info = null;

    protected function getHeader($request = null)
    {
        $request = !$request ? $this->request : null;

        $ret['appid'] = $request->header('XX-Wxapp-AppId', '');
        $ret['api_version'] = $request->header('XX-Api-Version', '1.1.0');
        $ret['device_type'] = $request->header('XX-Device-Type', '');
        $ret['token'] = $request->header('XX-Token', '');

        return $ret;
    }

    /**
     * 获得header的一个索引
     * @param $index
     * @return string
     */
    protected function getOneHeader($index)
    {
        $header = $this->getHeader();
        return isset($header[$index]) ? $header[$index] : "";
    }

    /**
     * @param bool $auto_show_error
     * @param bool $check_token
     * @return array|bool
     */
    protected function checkHeader($auto_show_error = true, $check_token = false)
    {
        $header = $this->getHeader();

        $validate = new Validate([
            'appid' => 'require|app_id_validate',
            'device_type' => 'require|in:wxapp,webapp'
        ]);

        $validate->message([
            'appid.require' => ErrorCodeMap::MISS_APPID,
            'appid.app_id_validate' => ErrorCodeMap::BAD_WXAPP_ID,
            'device_type.require' => ErrorCodeMap::MISS_DEVICE_TYPE,
            'device_type.in' => ErrorCodeMap::INVALIDATE_DEVICE_TYPE
        ]);

        $validate->extend('app_id_validate', function ($value) {
            $app_info = WxService::getWxAppInfo($value);
            return $app_info ? true : ErrorCodeMap::BAD_WXAPP_ID;
        });


        if ($check_token) {
            $validate->rule(['token' => 'require']);
        }

        $error = $validate->check($header);
        if ($error) {
            return true;
        }


        $error_msg = $validate->getError();
        if ($auto_show_error) {
            $this->error(['code' => $error_msg, 'msg' => '']);
        }
        return $error_msg;
    }


    protected function getUserIdByToken($token)
    {
        $map['token'] = $token;
        $map['expire_time'] = array('egt',time());
        $ret = Db::name('UserToken')->where($map)->find();
        return $ret ? $ret['user_id'] : 0;
    }

    protected function checkToken(){
        $header = $this->getHeader();
        $user_id = $this->getUserIdByToken($header['token']);
        if(!$user_id)
        {
            $this->error(setReturnData(ErrorCodeMap::USER_TOKEN_EXPIRE));
        }
        return $user_id;
    }

    /**
     * 根据token获得用户信息
     * @param $token
     * @return null
     */
    protected function getUserInfoByToken($token){
        $header = $this->getHeader();
        if(!$header){
            return null;
        }
        $user_logic = new UserLogic();
        return $user_logic->getUserInfoByToken($token,$header['device_type']);
    }

    /**
     * 权限校验
     * @param $role_ids
     */
    protected function rightCheck($role_ids){
        $user_id = $this->checkToken();
        //获得用户角色
        $where['user_id'] = $user_id;
        $user_roles = Db::name('RoleUser')->where($where)->column('role_id');
        if(!$user_roles){
            $this->error(setReturnData(ErrorCodeMap::HAVE_NO_RIGHT));
        }
        if(!is_array($role_ids)){
            $role_ids = [$role_ids];
        }

        if(!array_intersect($user_roles,$role_ids)){
            $this->error(setReturnData(ErrorCodeMap::HAVE_NO_RIGHT));
        }
    }

    protected function getCurrentUserInfo(){
        if($this->current_user_info){
            return $this->current_user_info;
        }

        $user_id = $this->checkToken();
        $user_logic = new UserLogic();
        $this->current_user_info = $user_logic->getAllUserInfo($user_id);
        return $this->current_user_info;
    }

    protected function getCurrentAgencyId(){
        if($this->current_agency_id){
            return $this->current_agency_id;
        }

        $user_info = $this->getCurrentUserInfo();
        return $this->current_agency_id = $user_info['user_extra']['agency_id'];
    }

    protected function getPageParam(){
        $data['p'] = intval($this->request->param('p',1));
        $data['l'] = intval($this->request->param('l',10));
        return $data;
    }
}