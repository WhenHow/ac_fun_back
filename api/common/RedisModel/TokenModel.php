<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2018/8/29
 * Time: 8:16
 */
namespace api\common\RedisModel;

class TokenModel extends BaseRedisModel
{
    const USER_TOKEN_PREFIX = 'USER_TOKEN';

    public function __construct()
    {
        parent::__construct();
    }


    public function isTokenValidate($token)
    {
        $redis_index = $this->getTokenRedisIndex($token);
        return $this->get($redis_index,null);
    }

    public function setToken($token,$user_info)
    {
        $redis_index = $this->getTokenRedisIndex($token);
        return $this->set($redis_index,$user_info,config('REDIS_EXPIRE_TIME.USER_TOKEN'));
    }


    private function getTokenRedisIndex($token)
    {
        return self::USER_TOKEN_PREFIX.$token;
    }

}