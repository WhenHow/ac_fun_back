<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2018/11/26
 * Time: 1:15
 */

namespace api\common\model;


use think\Db;

class WordSentenceModel extends CommonModel
{
    public function __call($method, $args)
    {
        return parent::__call($method, $args); // TODO: Change the autogenerated stub
    }

    public function getWordsSentence($word_ids, $sentence_limit = 3)
    {
        $ret = [];

        $where['word_id'] = ["in", $word_ids];

        $list = Db::name('WordSentence')->alias('a')->where($sentence_limit, '>', function ($query) {
            $query->table('WordSentence')->alias('b')
                ->where('a.word_id', '=', 'b.word_id')
                ->where('a.id', '>', 'b.id')->count();
        })->where($where)->field("word_id,sentence,translation")->select();
        $list = $list ? $list->toArray() : [];

        array_walk($list, function ($sentence) use (&$ret) {
            $word_id = $sentence['word_id'];

            $val['sentence'] = $sentence['sentence'];
            $val['translation'] = $sentence['translation'];

            $ret[$word_id][] = $val;
        });

        return $ret;
    }
}