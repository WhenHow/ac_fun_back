<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2018/9/24
 * Time: 13:44
 */

namespace app\common\model;


use api\common\map\ErrorCodeMap;
use think\Db;

class WordBookListModel extends BaseModel
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }


    public function addOne($data){
        $is_validate = $this->isAddDataValidate($data);
        $data = $this->data_filter($data);

        if($is_validate !== true){
            return $is_validate;
        }

        $uid = $this->insertGetId($data);
        if(!$uid){
            return setReturnData(ErrorCodeMap::SYSTEM_ERROR);
        }

        return setReturnData(true,'',$uid);
    }



    private function isAddDataValidate($data){

        $rule = array(
            'book_id' => 'require|number',
            'name' => "require",
        );

        $is_validate = $this->validate($rule);
        if(!$is_validate){
            return setReturnData(ErrorCodeMap::INVALIDATE_PARAM,'','参数异常');
        }


        $is_book_list_exist = $this->isExist($data['name'],$data['book_id']);
        if($is_book_list_exist){
            return setReturnData(ErrorCodeMap::DATA_EXIST,"book list {$data['name']} exists",$is_book_list_exist['id']);
        }

        return true;
    }


    private function isExist($list_name,$book_id)
    {
        $where['book_id'] = $book_id;
        $where['name'] = $list_name;
        return $this->where($where)->find();
    }

    public function countWords($id){
        $count = Db::name('WordListMap')->where(['list_id'=>$id])->count();
        $this->where(['id'=>$id])->update(['words_count'=>$count]);
    }
}