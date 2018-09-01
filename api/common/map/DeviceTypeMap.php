<?php

/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2018/8/8
 * Time: 18:19
 */
namespace api\common\map;

class DeviceTypeMap
{
    const MOBILE = 'mobile';
    const ANDROID = 'android';
    const IPHONE = 'iphone';
    const IPAD = 'ipad';
    const WEB = 'web';
    const PC = 'pc';
    const MAC = 'mac';
    const WXAPP = 'wxapp';

    private static $ALL_TYPE = ['mobile','android','iphone','ipad','web','pc','mac','wxapp'];

    /**
     * 获得所有类型值
     * @return array
     */
    public static function getAllType(){
        return self::$ALL_TYPE;
    }

    /**
     * 类型名是否合法
     * @param $type
     * @return bool
     */
    public static function isTypeValidate($type){
        return in_array($type,self::$ALL_TYPE) ? true : false;
    }

}