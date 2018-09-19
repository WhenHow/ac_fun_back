<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2018/8/26
 * Time: 22:50
 */

namespace app\user\controller;


use cmf\controller\HomeBaseController;
use think\cache\driver\Redis;
use think\Db;

class TestController extends HomeBaseController
{
    public function index(){
        $begin = time();

        $redis = new Redis();
        $redis->set('s',"s");

        var_dump(time()-$begin);
        $ret = Db::name('user')->find();
        var_dump($ret);
    }

    public function info(){
        phpinfo();
    }
}