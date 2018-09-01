<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\common\validate;

use think\Db;
use think\Validate;

class AgencyValidate extends Validate
{
    protected $rule = [
        'agency_name' => ['require','min:3','max:50','isAgencyNameUnique'],
        'intro' => ['max:200'],
        'wxapp_appid' => ['max:50','isWxAppIdUnique'],
        'wxapp_app_secret' => ['max:50'],
        'is_using' => ['in:0,1'],
        'is_flag' => ['in:0,1'],
    ];

    protected $message = [
        'agency_name.require' => '机构名称不能为空',
        'agency_name.max' => '机构名称至多50个字',
        'agency_name.min' => '机构名称至少3个字',
        'agency_name.isAgencyNameUnique' => '机构名称已存在',
        "intro.max" => '简介最多200个字',
        "wxapp_appid.max" => '小程序appid长度不能超过50',
        "wxapp_appid.isWxAppIdUnique" => '小程序appid已存在',
        'wxapp_app_secret.max' => '小程序appSecret长度不能超过50',
    ];

    protected $scene = [
        "add"=>['agency_name','intro','wxapp_appid','wxapp_app_secret','is_using','is_flag'],
        "edit"=>['agency_name'=>'min:3|max:50|isAgencyNameUnique','intro','wxapp_appid','wxapp_app_secret','is_using','is_flag'],
    ];


    /**
     * 机构名是否存在
     * @param $name
     * @param int $id_filter
     */
    public function isAgencyNameUnique($name,$id_filter = 0){
        $where['agency_name'] = $name;
        $where['is_flag'] = 1;
        $where['id'] = array('<>',$id_filter);
        $result = Db::name('Agency')->where($where)->find();
        return $result ? false : true;
    }


    public function isWxAppIdUnique($id,$id_filter = 0){
        if($id === ""){
            return true;
        }
        $where['is_flag'] = 1;
        $where['id'] = array('<>',$id_filter);
        $where['wxapp_appid'] = $id;
        $result = Db::name('Agency')->where($where)->find();
        return $result ? false : true;
    }

}