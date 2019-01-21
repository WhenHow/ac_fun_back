<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2018/9/24
 * Time: 16:43
 */

namespace app\common\model;


use think\Db;

class WordBooksModel extends BaseModel
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    public function countWords($id){
        $count = Db::name('WordListMap')->where(['book_id'=>$id])->count();
        $this->where(['id'=>$id])->update(['words_count'=>$count]);
    }
}