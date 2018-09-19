<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2018/9/1
 * Time: 18:00
 */

namespace api\common\RedisModel;


class UserInfoRedisModel extends BaseRedisModel
{
    const USER_INFO_KEY_PREFIX = 'USER_INFO:';

    public function __construct()
    {
        parent::__construct();
    }


    public function setInfo($user_id,$user_data){
        $key = $this->getUserInfoRedisIndex($user_id);
        $user_info_json = json_encode($user_data);
        return $this->set($key,$user_info_json);
    }

    public function getInfo($user_id){
        $key = $this->getUserInfoRedisIndex($user_id);
        $json = $this->get($key);
        if(!$json){
            return null;
        }
        return json_decode($json,true);
    }

    private function getUserInfoRedisIndex($user_id)
    {
        return self::USER_INFO_KEY_PREFIX.$user_id;
    }
}