<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2018/8/29
 * Time: 8:19
 */
namespace api\common\RedisModel;

interface GetAndSettableInterface
{
    public function getCaChe();
    public function setCaChe($data,$expire_time = 0);

}