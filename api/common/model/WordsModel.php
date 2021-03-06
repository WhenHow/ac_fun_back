<?php
/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2018/11/27
 * Time: 17:02
 */

namespace api\common\model;


use think\db\Expression;

class WordsModel extends CommonModel
{
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * 获得单词的详情
     * @param $ids
     * @param string $field
     * @return array|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getWordsBrief($ids,$field="*"){
        if(!is_array($ids) || !$ids){
            return null;
        }
        $ids_str = implode(',',$ids);
        $where['id'] = array('in',$ids);
        $exp = new Expression("field(`id`,{$ids_str})");
        $ret = $this->where($where)->field($field)->order($exp)->select();
        return $ret ? $ret->toArray() : null;
    }
}