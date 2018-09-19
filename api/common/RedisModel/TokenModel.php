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
    const USER_TOKEN_PREFIX = 'XWAPP_TOKEN:';

    public function __construct()
    {
        parent::__construct();
    }


    public function isTokenValidate($token)
    {
        $redis_index = $this->getTokenRedisIndex($token);
        return $this->get($redis_index,null);
    }

    public function setToken($token,$user_info,$expire_seconds = 0)
    {
        $redis_index = $this->getTokenRedisIndex($token);
        $expire_seconds = $expire_seconds <= 0 ? config('REDIS_EXPIRE_TIME.USER_TOKEN') : $expire_seconds;
        return $this->set($redis_index,$user_info,$expire_seconds);
    }


    private function getTokenRedisIndex($token)
    {
        return self::USER_TOKEN_PREFIX.$token;
    }

}