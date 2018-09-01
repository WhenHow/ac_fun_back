<?php
/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2018/7/27
 * Time: 14:59
 */

namespace app\admin\controller;

use app\common\validate\AgencyValidate;
use cmf\controller\AdminBaseController;
use think\Db;
use think\Request;

class AgencyController extends AdminBaseController
{
    public function _initialize()
    {
        parent::_initialize();
    }


    public function index()
    {
        $where = ["is_flag" => 1];
        $agency_list = Db::name('Agency')->where($where)->order('id desc')
            ->paginate(10);

        $page = $agency_list->render();
        $this->assign("page", $page);
        $this->assign("list", $agency_list);
        return $this->fetch();
    }


    public function agency_add()
    {
        return $this->fetch();
    }

    public function agency_edit()
    {
        $id = $this->request->param("id", 0, 'intval');
        $data = Db::name('agency')->where(["id" => $id,'is_flag'=>1])->find();
        if (!$data) {
            $this->error("机构不存在！");
        }
        $this->assign("data", $data);
        return $this->fetch();
    }

    /**
     * 管理员添加提交
     * @adminMenu(
     *     'name'   => '管理员添加提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '管理员添加提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        if (!$this->request->isPost()) {
            $this->error('请求方式异常');
        }

        $result = $this->validate($this->request->param(), 'Agency');
        if ($result !== true) {
            $this->error($result);
        }

        $data = array_merge($_POST,['create_time'=>date('Y-m-d H:i:s')]);
        $result = DB::name('Agency')->insertGetId($data);
        if ($result !== false) {
            $this->success("添加成功！", url("agency/index"));
        }

        $this->error("添加失败！");
    }

    public function editPost()
    {
        if (!$this->request->isPost()) {
            $this->error('请求方式异常');
        }
        //获得机构信息
        $post_data = $this->request->param();
        $id = $this->request->param("id", 0, 'intval');
        $data = Db::name('agency')->where(["id" => $id,'is_flag'=>1])->find();
        if (!$data) {
            $this->error("机构不存在！");
        }
        unset($data['id']);

        $post_data = diff_item_fetcher($data,$post_data);
        if(!$post_data){
            $this->error("没有有效的修改信息！");
        }
        $validate = new AgencyValidate();
        $result = $validate->scene('edit')->check($post_data);
        if($result!== true){
            $this->error($validate->getError());
        }
        $result = Db::name('agency')->where('id',$id)->update($post_data);
        $result === false ? $this->error("保存失败！") : $this->success("保存成功！");
    }

    public function del()
    {

    }
}