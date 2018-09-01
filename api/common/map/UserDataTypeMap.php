<?php
/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2018/8/9
 * Time: 17:41
 */

namespace api\common\map;


class UserDataTypeMap
{
    const SEX_MALE = 1;//性别：男
    const SEX_FEMALE = 2;//性别：女
    const SEX_UNKNOWN = 0;//性别：未知

    const STATUS_NORMAL = 1;//用户状态:正常
    const STATUS_UNVERIFIED = 2;//用户状态:未验证
    const STATUS_DISABLE = 0;//用户状态:禁用

    const USER_TYPE_ADMIN = 1;//用户类型(1:管理员)
    const USER_TYPE_NORMAL = 2;//用户类型(2:普通用户)


}