<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2018/9/24
 * Time: 16:23
 */

namespace app\common\model;


use api\common\map\ErrorCodeMap;
use think\Validate;

class WordListMapModel  extends BaseModel
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
            'word_id' => 'require|number',
            'word' => "require",
            'book_id' => 'require|number',
            'list_id' => 'require|number',
            'sort_index' => "number",
        );

        $validate = new Validate($rule);
        if(!$validate->check($data)){
            return setReturnData(ErrorCodeMap::INVALIDATE_PARAM,'',$validate->getError());
        }


        $is_map_exist = $this->isExist($data['word'],$data['list_id']);
        if($is_map_exist){
            return setReturnData(ErrorCodeMap::DATA_EXIST,"word list map {$data['word']} exists",$is_map_exist['id']);
        }

        return true;
    }


    private function isExist($word,$list_id)
    {
        $where['word'] = $word;
        $where['list_id'] = $list_id;
        return $this->where($where)->find();
    }
}