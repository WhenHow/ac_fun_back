<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2019/5/1
 * Time: 16:20
 */

namespace app\common\model;


use think\Validate;

class UserCheckMobileMapModel extends BaseModel
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }


    public function addOne($data){
        $data = $this->data_filter($data);
        $is_validate = $this->isAddDataValidate($data);
        if($is_validate !== true){
            return $is_validate;
        }

        $data['create_time'] = date("Y-m-d H:i:s");
        $uid = $this->insertGetId($data);
        if(!$uid){
            return setReturnData(false,'添加失败');
        }

        return setReturnData(true,'操作成功',$uid);
    }

    public function updateOne($data,$id){

    }



    private function isAddDataValidate($data){

        $rule = array(
            'mobile|手机号' => 'require',
            'add_user_id|添加人id'=>'require|number',
            'user_name|用户名' => 'require|chs|min:2|max:10'
        );

        $validate = new Validate($rule);
        $is_validate = $validate->check($data);
        if(!$is_validate){
            $error = $validate->getError();
            return setReturnData(false,$error);
        }

        if(!cmf_check_mobile($data['mobile'])){
            return setReturnData(false,'手机号格式不合法');
        }

        if($this->isMobileExist($data['mobile'])){
            return setReturnData(false,'手机号已存在');
        }

        return true;
    }

    public function delOne($id)
    {
        $this->where("id","=",$id)->delete();
        return setReturnData(true,"删除成功");
    }


    public function isMobileExist($mobile,$id_filter=0){
        $where['mobile'] = $mobile;
        $where['id'] = array("neq",$id_filter);
        return $this->where($where)->find();
    }
}