<?php
namespace api\common\service;

/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2018/8/8
 * Time: 16:13
 */

class WxService extends BaseService
{
    /**
     * 小程序获得session_key (其中会包含openid和unionid)
     * @param $app_id
     * @param $secret_key
     * @param $code
     */
    public static function getAppSessionKey($app_id,$secret_key,$code){
        $response = cmf_curl_get("https://api.weixin.qq.com/sns/jscode2session?appid=$app_id&secret=$secret_key&js_code=$code&grant_type=authorization_code");
        return !empty($response['errcode']) ? false : $response;
    }

    /**
     *
     * @param $encrypted_data
     * @param $appId
     * @param $iv
     * @param $session_key
     * @return bool
     */
    public static function decryptAppData($encrypted_data,$appId,$iv,$session_key){
        $pc = new \wxapp\aes\WXBizDataCrypt($appId,$session_key);
        $errCode = $pc->decryptData($encrypted_data, $iv, $wxUserData);
        if ($errCode != 0) {
            return false;
        }
        if(!isset($wxUserData['union_id'])){
            $wxUserData['union_id'] = '';
        }
        return $wxUserData;
    }


    /**
     * 根据啊appid获得微信小程序信息
     * @param $app_id
     * @return null
     */
    public static function getWxAppInfo($app_id)
    {
        $wx_app_settings = cmf_get_option('wxapp_settings');
        if(!$wx_app_settings) {
            return null;
        }

        return isset($wx_app_settings['wxapps'][$app_id]) ? $wx_app_settings['wxapps'][$app_id] : null;
    }
}