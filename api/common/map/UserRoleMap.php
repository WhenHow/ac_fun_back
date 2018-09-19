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
    const TEACHER = 3;//老师
    const STUDENT= 4;//学生

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
            case self::TEACHER:
                $type_name = "TEACHER";

                break;
            case self::STUDENT:
                $type_name = "STUDENT";

                break;
        }


        return $type_name;
    }

    public static function getAllRoleIds(){
        return [self::SUPER_ADMIN,self::NORMAL_ADMIN,self::TEACHER,self::STUDENT,self::NORMAL_ADMIN];
    }

}