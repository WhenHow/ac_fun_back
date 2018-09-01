<?php
/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2018/8/8
 * Time: 10:08
 */

namespace api\wxapp\controller;

use api\common\map\ErrorCodeMap;
use api\common\service\WxService;
use cmf\controller\RestBaseController;
use think\Validate;

class BaseController extends RestBaseController
{
    /**
     * 获得小程序header信息
     * @param null $request
     * @return mixed
     */
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

}