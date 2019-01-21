<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2018/9/22
 * Time: 16:14
 */

namespace app\common\model;


use api\common\map\ErrorCodeMap;
use think\Validate;

class WordsModel extends BaseModel
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }


    public function addOne($data){
        $data = $this->data_filter($data);

        $rule = [
            "word|单词" => "require",
            "phonetic_alphabet|音标" => "require",
            "part_of_speech|词性" => "require",
            "translation|中文" => "require",
            "is_marked" => "in:0,1",
        ];

        $validate = new Validate($rule);
        if(!$validate->check($data)){
            return setReturnData(ErrorCodeMap::INVALIDATE_PARAM,'',$validate->getError());
        }

        $is_word_exist = $this->isExist($data['word']);
        if($is_word_exist){
            return setReturnData(ErrorCodeMap::DATA_EXIST,"word {$data['word']} exists",$is_word_exist['id']);
        }

        $word_id = $this->insertGetId($data);
        if(!$word_id){
            return setReturnData(ErrorCodeMap::SYSTEM_ERROR);
        }
        return setReturnData(true,'',$word_id);
    }

    private function isExist($word)
    {
        $where['word'] = $word;
        return $this->where($where)->find();
    }
}