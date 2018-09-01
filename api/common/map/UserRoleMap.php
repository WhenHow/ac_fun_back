<?php
/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2018/8/9
 * Time: 9:43
 */
namespace api\common\map;

class UserRoleMap
{
    const SUPER_ADMIN = 1;//超级管理员
    const NORMAL_ADMIN = 2;//普通管理员
    const SUPPLIER_ADMIN = 4;//服务商管理员(提供设备维修服务的厂商的管理人员，负责分配维修工单等)
    const SUPPLIER_ENGINEER = 6;//维修工程师(厂商下属的各产品维护的过程维护人员，包括软件服务，硬件服务等)
    const SYSTEM_ADMIN = 7;//系统管理员(维护软件系统的管理员，包括一些初始化信息的添加和管理等)
    const FRONT_USER = 8;//终端用户(个人，学校设备管理员等，下面简称用户)

    public static function getRoleName($role_id)
    {
        $type_name = "UNKNOWN_TYPE";

        switch ($role_id){
            case self::SUPER_ADMIN:
                $type_name = "SUPER_ADMIN";
                break;
            case self::NORMAL_ADMIN:
                $type_name = "NORMAL_ADMIN";

                break;
            case self::SUPPLIER_ADMIN:
                $type_name = "SUPPLIER_ADMIN";

                break;
            case self::SUPPLIER_ENGINEER:
                $type_name = "SUPPLIER_ENGINEER";

                break;
            case self::SYSTEM_ADMIN:
                $type_name = "SYSTEM_ADMIN";

                break;
            case self::FRONT_USER:
                $type_name = "FRONT_USER";

                break;
        }


        return $type_name;
    }

    public static function getAllRoleIds(){
        return [self::SUPER_ADMIN,self::NORMAL_ADMIN,self::SUPPLIER_ADMIN,self::SUPPLIER_ENGINEER,self::SYSTEM_ADMIN,self::FRONT_USER];
    }

}