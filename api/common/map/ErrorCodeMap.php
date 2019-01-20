<?php
/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2018/8/9
 * Time: 10:32
 */
namespace api\common\map;

class ErrorCodeMap
{
    const SUCCESS = '1';
    const MISS_APPID = '00001';//缺少appid
    const MISS_DEVICE_TYPE = '00002';//缺少客户端类型;
    const INVALIDATE_DEVICE_TYPE = '00003';//非法客户端类型
    const INVALIDATE_PARAM = '00004';//参数异常
    const BAD_WXAPP_ID = '00005';//微信小程序appid
    const GET_WX_USER_INFO_FAILED = '00006';//获得微信用户信息失败
    const USER_NOT_EXIST = '00007';//用户不存在
    const SYSTEM_ERROR = '00008';//系统异常
    const USER_TOKEN_EXPIRE = '00009';//用户token已过期
    const USER_ALREADY_EXIST = '00010';//用户已存在
    const MOBILE_EXIST = '00011';//手机号已存在
    const LOGIN_NAME_EXIST = '00012';//登陆名已存在
    const EMAIL_EXIST = '00013';//email已存在
    const AGENCY_NOT_EXIST = '00014';//机构不存在
    const DATA_EXIST = "00015";//数据已存在
    const ALREADY_REMEMBERED_TODAY = "00016";//今天已经背诵过单词

}