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
use app\common\model\TaskLogModel;
use app\common\model\UserTaskDetailModel;
use think\Db;

class UserWordLogic extends BaseLogic
{
    const MAX_REMEMBER_NUM = 100;
    const MIN_REMEMBER_NUM = 10;

    public function getUserTodayWords($user_id,$book_id,$word_num,$need_sentence = true){
        //获得最近一条背诵任务日志
        $last_task_log = $this->getLastUserTaskLog($user_id,$book_id);
        $last_map_id = $last_task_log ? $last_task_log['end_book_map_id'] : 0;
        $date = date("Y-m-d");
        //获得要背诵的单词id
        $empty_ret = ['list'=>[],'date'=>$date,'word_num'=>$word_num,"begin"=>0,"end"=>0,"begin_map_id"=>0,'end_map_id'=>0];
        if($this->isRememberToday($user_id)){
            return $empty_ret;
        }

        $ids = $this->getRememberWordIds($book_id,$last_map_id,$word_num);
        if(!$ids){
            return $empty_ret;
        }

        $list = $this->getWordsBriefFromDb($ids,$need_sentence);
        $begin_id = $list[0]['id'];
        $end_id = end($list)['id'];

        foreach($list as &$val){
            $val['remember_times'] = 0;
            $val['is_unknown'] = 0;
        }

        $list_map = array_keys($ids);
        $begin_map_id = $list_map[0];
        $end_map_id = end($list_map);

        return ['begin_map_id'=>$begin_map_id,"end_map_id"=>$end_map_id,'list'=>$list,'date'=>$date,'word_num'=>$word_num,"begin"=>$begin_id,"end"=>$end_id];
    }

    public function isRememberToday($user_id){
        $where['user_id'] = $user_id;
        $where['create_date'] = date("Y-m-d");
        $ret = Db::name("TaskLog")->where($where)->find();
        return $ret ? true : false;
    }

    public function reportRemember(TaskLogModel $log_model,$all_word_ids,$forget_ids){
        //添加任务日志
        $log_data = $log_model->getLogData();
        $log_data['next_review_date'] = date('Y-m-d',strtotime("+1 day"));
        $add_log_ret = $log_model->addOne($log_data);
        if($add_log_ret['status']!=true){
            return $add_log_ret;
        }
        //添加背诵详情
        $log_id = $add_log_ret['data'];
        $book_id = $log_model->book_id;
        $user_id = $log_model->user_id;
        $detail_list = [];
        foreach ($all_word_ids as $id){
            $detail['word_id'] = $id;
            $detail['book_id'] = $book_id;
            $detail['log_id'] = $log_id;
            $detail['is_remember_first_time'] = in_array($id,$forget_ids) ? 0 : 1;
            $detail['forget_times'] = in_array($id,$forget_ids) ? 1:0;
            $detail['user_id'] = $user_id;
            $detail_list[] = $detail;
        }

        $detail_model = new UserTaskDetailModel();
        return $detail_model->multiAdd($detail_list);
    }

    public function getWordsBriefFromDb($word_ids,$need_sentence = true){
        $word_model = new WordsModel();
        //获得单词
        $words = $word_model->getWordsBrief($word_ids);
        if(!$words){
            return null;
        }
        //获得句子
        if($need_sentence){
            $sentence_model = new WordSentenceModel();
            $sentence_list = $sentence_model->getWordsSentence($word_ids);
            foreach ($words as &$word){
                $word_id = $word['id'];
                $word['sentence'] = isset($sentence_list[$word_id]) ? $sentence_list[$word_id] : [];
            }
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
        return Db::table($sub_query." a")->where($where)->limit($word_num)->column('word_id','id');
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