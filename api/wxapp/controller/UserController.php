<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace api\wxapp\controller;

use api\common\logic\UserLogic;
use api\common\map\ErrorCodeMap;
use api\common\map\UserDataTypeMap;
use api\common\map\UserRoleMap;
use api\common\model\ThirdPartyUserModel;
use api\common\service\WxService;
use cmf\controller\RestBaseController;
use think\Validate;
use wxapp\aes\WXBizDataCrypt;

class UserController extends BaseController
{
    // 获取用户信息
    public function getUserInfo()
    {

    }

    /**
     * 前台登录
     */
    public function login(){
        $this->loginReqParamCheck();
        //获得用户的微信小程序信息
        $wx_user_info = $this->getWxAppUserInfo();
        if(!$wx_user_info){
            $this->error(['code'=>ErrorCodeMap::GET_WX_USER_INFO_FAILED,'msg'=>'']);
        }

        $app_id = $this->getOneHeader('appid');
        $open_id = $wx_user_info['openId'];
        $union_id = $wx_user_info['union_id'];
        //检查用户是否存在
        $user_logic = new UserLogic();
        $current_user = $user_logic->isWxUserExist($app_id,$open_id,$union_id);
        if(!$current_user){
            $this->error(['code'=>ErrorCodeMap::USER_NOT_EXIST,'msg'=>'']);
        }
        //执行登陆
        $user_id = $current_user['user_info']['id'];
        $device_type = $this->getOneHeader('device_type');
        $user_data = $user_logic->doLogin($user_id,$device_type);
        if(!$user_data){
            $this->error(['code'=>ErrorCodeMap::SYSTEM_ERROR,'msg'=>'']);
        }

        $this->success('',$user_data);
    }


    /**
     * 用户注册
     */
    public function normalReg(){
        $this->regParamCheck();
        //获得用户的微信小程序信息
        $wx_user_info = $this->getWxAppUserInfo();
        if(!$wx_user_info){
            $this->error(['code'=>ErrorCodeMap::GET_WX_USER_INFO_FAILED,'msg'=>'']);
        }
        $app_id = $this->getOneHeader('appid');
        $open_id = $wx_user_info['openId'];
        $union_id = $wx_user_info['union_id'];

        //获得用户的机构id
        $user_logic = new UserLogic();
        $agency_id = $user_logic->getAgencyId($app_id);
        if(!$agency_id){
            $this->error(setReturnData(ErrorCodeMap::AGENCY_NOT_EXIST));
        }
        //执行注册
        $user_data = $this->buildRegData($agency_id,$app_id,$wx_user_info);
        $ret = $user_logic->addUser($user_data['user_base'],$user_data['user_info'],UserRoleMap::FRONT_USER,$user_data['third_user']);
        if($ret['code'] == ErrorCodeMap::SUCCESS){
            $this->success();
        }

        $this->error($ret);
    }

    private function buildRegData($agency_id,$app_id,$wx_user_info,$is_front = true)
    {

        $data = $this->request->param();

        $open_id = $wx_user_info['openId'];
        $union_id = $wx_user_info['union_id'];

        $user_base['user_nickname'] = $data['real_name'];
        $user_base['user_status'] = UserDataTypeMap::STATUS_NORMAL;
        $user_base['user_type'] = $is_front ? UserDataTypeMap::USER_TYPE_NORMAL : UserDataTypeMap::USER_TYPE_ADMIN;
        $user_base['avatar'] = $wx_user_info['avatarUrl'];

        $user_info['unit_id'] = $data['unit_id'];
        $user_info['real_name'] = $data['real_name'];
        $user_info['contact_tel'] = $data['tel'];
        $user_info['agency_id'] = $agency_id;
        $user_info['avatar_img_id'] = isset($data['img_id']) ? $data['img_id'] : 0;
        $user_info['section_name'] = isset($data['section_name']) ? $data['section_name'] : '';
        $user_info['job_title'] = isset($data['job_title']) ? $data['job_title'] : '';
        $user_info['address'] = isset($data['address']) ? $data['address'] : '';

        $third_user['third_party'] = ThirdPartyUserModel::THIRD_PARTY_WXAPP;
        $third_user['app_id'] = $app_id;
        $third_user['openid'] = $open_id;
        $third_user['union_id'] = $union_id;


        return ["user_base"=>$user_base,"user_info"=>$user_info,"third_user"=>$third_user];
    }

    private function regParamCheck(){
        //检查header
        $this->checkHeader();
        //检查参数
        $validate = new Validate([
            'code'           => 'require',
            'encryptedData' => 'require',
            'iv'             => 'require',
            'rawData'       => 'require',
            'signature'      => 'require',
            'unit_id' => 'require',
            'real_name'=>'require',
            'tel'=>'require'
        ]);

        $validate->message([
            'code.require'           => '缺少参数code!',
            'encryptedData.require' => '缺少参数encrypted_data!',
            'iv.require'             => '缺少参数iv!',
            'rawData.require'       => '缺少参数raw_data!',
            'signature.require'      => '缺少参数signature!',
            'unit_id.require' => '缺少单位',
            'real_name.require' => '缺少用户名',
            'tel.require' => '缺少电话',
        ]);

        $data = $this->request->param();
        if (!$validate->check($data)) {
            $this->error(['code'=>ErrorCodeMap::INVALIDATE_PARAM,'msg'=>$validate->getError()]);
        }

        return true;
    }


    /**
     *
     */
    private function loginReqParamCheck(){
        //检查header
        $this->checkHeader();
        //检查参数
        $validate = new Validate([
            'code'           => 'require',
            'encryptedData' => 'require',
            'iv'             => 'require',
            'rawData'       => 'require',
            'signature'      => 'require',
        ]);

        $validate->message([
            'code.require'           => '缺少参数code!',
            'encryptedData.require' => '缺少参数encrypted_data!',
            'iv.require'             => '缺少参数iv!',
            'rawData.require'       => '缺少参数raw_data!',
            'signature.require'      => '缺少参数signature!',
        ]);

        $data = $this->request->param();
        if (!$validate->check($data)) {
            $this->error(['code'=>ErrorCodeMap::INVALIDATE_PARAM,'msg'=>$validate->getError()]);
        }

        return true;
    }


    /**
     * 获得微信用户的信息
     * @return bool
     */
    private function getWxAppUserInfo()
    {
        $data = $this->request->param();
        $code = $data['code'];
        $app_id = $this->getOneHeader('appid');
        //获得微信小程信息
        $app_info = WxService::getWxAppInfo($app_id);
        //获得微信用户会话key
        $app_secret = $app_info['app_secret'];
        $response = WxService::getAppSessionKey($app_id,$app_secret,$code);
        $response = json_decode($response, true);
        if (!$response || !empty($response['errcode'])) {
            return false;
        }
        //获得微信的用户信息
        $session_key = $response['session_key'];
        return WxService::decryptAppData(urldecode($data['encryptedData']),$app_id,urldecode($data['iv']),$session_key);
    }


}
