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
class TestController extends HomeBaseController
{
    public function index(){

    }

    public function info(){
        phpinfo();
    }
}