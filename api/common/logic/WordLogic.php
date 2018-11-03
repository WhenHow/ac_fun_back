<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2018/9/24
 * Time: 18:50
 */

namespace api\common\logic;


use api\common\map\CommonMap;
use api\common\RedisModel\BookListRedisModel;
use think\Db;

class WordLogic extends BaseLogic
{

    const BOOK_LIST_REDIS_PREFIX = "BOOK_LIST_ID:";


    public function __construct()
    {
    }


    public function getAllWordList($book_id){
        $book_list_redis = new BookListRedisModel();
        $book_list = $book_list_redis->getBookList($book_id);
        if($book_list){
            return $book_list;
        }


        $where['book_id'] = $book_id;
        $where['is_flag'] = CommonMap::EXIST;
        $where['is_show'] = CommonMap::SHOW;

        $list = Db::name('WordBookList')->where($where)
            ->field('id,name,list_alias,sort_index,book_id,words_count')->select();

        $list = $list ? $list->toArray() : [];
        if($list){
            $book_list_redis->setBookList($book_id,$list);
        }

        return $list;
    }

    public function getListWords($list_id){
        $book_list_redis = new BookListRedisModel();
        $sentence_list = $book_list_redis->getListSentence($list_id);
        if($sentence_list){
            return $sentence_list;
        }

        $where['map.list_id'] = $list_id;
        $word_list = Db::name('WordListMap')->alias('map')
                    ->join('__WORDS__ w','w.id=map.word_id')
                    ->where($where)->column('w.*,list_id');

        if(!$word_list){
            return [];
        }

        $ids = array_keys($word_list);
        $sentence = $this->getWordsSentence($ids);

        $ret = [];
        foreach ($word_list as $val){
            $word_id = $val['id'];
            $val['sentence'] = key_exists($word_id,$sentence) ? array_slice($sentence[$word_id],0,3) : [];
            $ret[] = $val;
        }
        //获得单词本信息
        $book_list_info = Db::name("WordBookList")->where('id',$list_id)->find();
        $book_name = $book_list_info ? ($book_list_info['list_alias'] ? $book_list_info['list_alias'] : $book_list_info['name']) : "";

        $data = ["book_name"=>$book_name,"list"=>$ret];
        if($ret){
            $book_list_redis->setListSentence($list_id,$data);
        }
        return $data;

    }

    private function getWordsSentence($word_ids){
        $ret = [];

        $where['word_id'] = ["in",$word_ids];
        $list = Db::name("WordSentence")->where($where)->field("word_id,sentence,translation")->select();
        $list = $list ? $list->toArray() : [];

        array_walk($list,function($sentence) use(&$ret){
            $word_id = $sentence['word_id'];

            $val['sentence'] = $sentence['sentence'];
            $val['translation'] = $sentence['translation'];

            $ret[$word_id][] = $val;
        });

        return $ret;
    }



}