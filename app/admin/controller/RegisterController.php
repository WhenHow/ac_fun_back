<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2019/5/1
 * Time: 16:16
 */

namespace app\admin\controller;
use app\common\model\UserCheckMobileMapModel;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;


class RegisterController extends AdminBaseController
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }


    public function mobile_check_list(){
        $mobile = $this->request->param("mobile","");
        $user_name = $this->request->param("user_name","");
        $is_used = $this->request->param("is_used","-1");

        $list = Db::name("UserCheckMobileMap")->where(function($query)use($mobile,$user_name,$is_used){
            if($mobile){
                $query->where("mobile","like","%$mobile%");
            }
            if($user_name){
                $query->where("user_name","like","%$user_name%");
            }
            if(in_array($is_used,array(0,1))){
                $query->where("is_used","=",$is_used);
            }
        })->order('id desc')->paginate(10);

        $page = $list->render();
        $this->assign("page", $page);
        $this->assign("list", $list);
        $this->assign("mobile", $mobile);
        $this->assign("user_name", $user_name);
        $this->assign("is_used", $is_used);


        return $this->fetch();

    }

    public function show_add_mobile_check(){
        return $this->fetch();
    }

    public function do_add_mobile_check(){
        $user_id = session('ADMIN_ID');
        $data = $this->request->param();
        $data['add_user_id'] = $user_id;
        $model = new UserCheckMobileMapModel();
        return $model->addOne($data);
    }

    public function del_mobile_check(){
        $id = intval($this->request->param("id",0));
        $model = new UserCheckMobileMapModel();
        return $model->delOne($id);
    }



}