<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2018/8/29
 * Time: 7:58
 */
namespace api\common\RedisModel;
class BaseRedisModel
{
    protected $redis;

    public function __construct()
    {
        $this->redis = new \think\cache\driver\Redis();
    }

    public function __call($name, $arguments)
    {
        if(in_array($name,['get','set','has','inc','dec','rm','clear'])){
            return call_user_func_array(array($this->redis,$name),$arguments);
        }

        $instance = $this->redis->handler();

        return call_user_func_array(array($instance,$name),$arguments);
    }

}