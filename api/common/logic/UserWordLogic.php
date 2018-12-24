<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2018/11/25
 * Time: 22:08
 */

namespace api\common\logic;


use api\common\model\WordSentenceModel;
use api\common\model\WordsModel;
use think\Db;

class UserWordLogic extends BaseLogic
{
    const MAX_REMEMBER_NUM = 100;
    const MIN_REMEMBER_NUM = 10;

    public function getUserTodayWords($user_id,$book_id,$word_num){
        //获得最近一条背诵任务日志
        $last_task_log = $this->getLastUserTaskLog($user_id,$book_id);
        $last_map_id = $last_task_log ? $last_task_log['begin_book_map_id'] : 0;
        //获得要背诵的单词id
        $ids = $this->getRememberWordIds($book_id,$last_map_id,$word_num);
        if(!$ids){
            return null;
        }

        return $this->getWordsBriefFromDb($ids);
    }

    private function getWordsBriefFromDb($word_ids){
        $word_model = new WordsModel();
        //获得单词
        $words = $word_model->getWordsBrief($word_ids);
        if(!$words){
            return null;
        }
        //获得句子
        $sentence_model = new WordSentenceModel();
        $sentence_list = $sentence_model->getWordsSentence($word_ids);
        foreach ($words as &$word){
            $word_id = $word['id'];
            $word['sentence'] = isset($sentence_list[$word_id]) ? $sentence_list[$word_id] : [];
        }
        return $words;
    }

    /**
     * @param $book_id
     * @param $start_map_id
     * @param $word_num
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function getWordsSpanToRemember($book_id,$start_map_id,$word_num){
        $where['id'] = array(">",$start_map_id);
        $where['book_id'] = $book_id;
        //获得第一个要背诵的单词
        $ret['begin_word'] = Db::name('WordListMap')->where($where)->group('word_id')->order('id asc')->find();
        //获得最后一个要背诵的单词
        $offset = $word_num - 1;
        $ret['end_word'] = Db::name('WordListMap')->where($where)->group('word_id')->order('id asc')->limit(1,"offset {$offset}")->find();
        return $ret;
    }

    /**
     * 获得要背诵的单词id
     * @param $book_id
     * @param $start_map_id
     * @param $word_num
     * @return array
     */
    private function getRememberWordIds($book_id,$start_map_id,$word_num)
    {
        $where['id'] = array(">", $start_map_id);
        $where['book_id'] = $book_id;
        $sub_query = Db::name("WordListMap")->group('word_id')->buildSql();
        return Db::table($sub_query." a")->where($where)->limit($word_num)->column('word_id');
    }
    /**
     * 获得最近的一条背诵任务
     * @param $user_id
     * @param $book_id
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function getLastUserTaskLog($user_id,$book_id){
        $where['user_id'] = $user_id;
        $where['book_id'] = $book_id;
        $ret = Db::name('TaskLog')->where($where)->order('id desc')->find();
        return $ret;
    }
}