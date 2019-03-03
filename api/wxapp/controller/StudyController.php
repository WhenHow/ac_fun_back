<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2018/11/26
 * Time: 2:57
 */

namespace api\wxapp\controller;


use api\common\logic\ReviewLogic;
use api\common\logic\UserWordLogic;
use api\common\map\ErrorCodeMap;
use app\common\model\TaskLogModel;
use think\Db;
use think\Request;

class StudyController extends BaseController
{
    private $user_id;


    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        //检查token
        $this->checkHeader();
        $this->user_id = $this->checkToken();

    }

    /**
     * 获得每月学习数据
     * @return array
     */
    public function user_month_statistics(){
        $current_year = date("Y");
        $current_month = date("m");

        $year = $this->request->param('year',$current_year);
        $month = $this->request->param('month',$current_month);
        //获得背诵数据
        $remember_map['year'] = $year;
        $remember_map['month'] = $month;
        $remember_map['user_id'] = $this->user_id;
        $list = Db::name("TaskLog")->where($remember_map)->column("create_date,real_word_num,year,month,day","day");


        $review_map['create_year'] = $year;
        $review_map['create_month'] = $month;
        $review_map['user_id'] = $this->user_id;
        $review_list = Db::name("UserReviewTask")->where($review_map)->column("create_date,word_num,create_year,create_month,create_day","create_day");

        //获得当前月份有多少天
        $last_day_in_month = date("d",strtotime("$year-$month-01 +1 month -1 day"));
        //遍历数据
        $data = [];
        $study_days = 0;
        for ($day = 1; $day<=$last_day_in_month; $day++){
            $remember_num = isset($list[$day]) ? $list[$day]['real_word_num'] : 0;
            $review_num = isset($review_list[$day]) ? $review_list[$day]['word_num'] : 0;;
            $is_study_today = 0;
            if($remember_num  || $review_num){
                $study_days++;
                $is_study_today = 1;
            }
            $data[] = ["is_study_today"=>$is_study_today,"remember"=>$remember_num,"review"=>$review_num,'day'=>$day,"date"=>date("Y-m-d",strtotime("$year-$month-$day"))];
        }

        $ret = ["study_days"=>$study_days,'detail'=>$data];
        return setReturnData(ErrorCodeMap::SUCCESS,"",$ret);
    }

    public function remember(){
        $book_id = $this->request->param('book_id',1);
        $word_count = $this->request->param('count',100);

        $word_logic = new UserWordLogic();

        $word_list = $word_logic->getUserTodayWords($this->user_id,$book_id,$word_count);
        return setReturnData(ErrorCodeMap::SUCCESS,'',$word_list);
    }

    public function review(){
        $review_logic = new ReviewLogic();
        $task_detail = $review_logic->getTodayReviewTaskDetail($this->user_id);

        if($task_detail && $task_detail['task_ids'] && !$task_detail['word_ids']){
            $review_logic->reportReview($this->user_id,$task_detail,null);
        }

        if(!$task_detail||!$task_detail['word_ids']){
            return setReturnData(ErrorCodeMap::SUCCESS,"",[]);
        }

        $word_logic = new UserWordLogic();
        $data['list'] = $word_logic->getWordsBriefFromDb($task_detail['word_ids']);
        foreach($data['list'] as &$val){
            $val['remember_times'] = 0;
            $val['is_unknown'] = 0;
        }
        $data['remember_tasks'] = $task_detail['task_ids'];
        $data['word_num'] = count($data['list']);
        $data['token'] = md5(implode(",",$data['remember_tasks']));
        return setReturnData(ErrorCodeMap::SUCCESS,"",$data);
    }

    public function report_review(){
        $forget_words_ids = $this->request->param('forget_words',"");
        $forget_words_ids = $forget_words_ids ? explode(",",$forget_words_ids) : [];

        $token = $this->request->param("token","");
        //获得今日需要复习的单词
        $review_logic = new ReviewLogic();
        $task_detail = $review_logic->getTodayReviewTaskDetail($this->user_id);
        if(!$task_detail||!$task_detail['word_ids']){
            return setReturnData(ErrorCodeMap::ALREADY_REVIEW_TODAY);
        }
        //检查token是否正确
        $remember_token = md5(implode(",",$task_detail['task_ids']));
        if($remember_token != $token){
            return setReturnData(ErrorCodeMap::INVALIDATE_PARAM);
        }

        $review_logic->reportReview($this->user_id,$task_detail,$forget_words_ids);
        return setReturnData(ErrorCodeMap::SUCCESS);
    }


    public function report_remember(){
        $book_id = $this->request->param('book_id',1);
        $word_count = $this->request->param('word_num',100);
        $begin_word_id = $this->request->param('begin',0);
        $end_word_id = $this->request->param('end',0);

        $begin_map_id = $this->request->param('begin_map_id',0);
        $end_map_id = $this->request->param('end_map_id',0);

        $forget_words_ids = $this->request->param('forget_words',"");
        $forget_words_ids = $forget_words_ids ? explode(",",$forget_words_ids) : [];

        $word_logic = new UserWordLogic();
        //检查今天是否已经背诵过单词
        if($word_logic->isRememberToday($this->user_id)){
            return setReturnData(ErrorCodeMap::ALREADY_REMEMBERED_TODAY);
        }
        //获得今天要背的单词
        $remember_words = $word_logic->getUserTodayWords($this->user_id,$book_id,$word_count,false);

        if(!$remember_words['list']){
            return setReturnData(ErrorCodeMap::SUCCESS);
        }
        //检查背诵单词是否合法
        if($remember_words['begin']!=$begin_word_id || $remember_words['end']!=$end_word_id){
            return setReturnData(ErrorCodeMap::INVALIDATE_PARAM);
        }

        if($remember_words['begin_map_id']!=$begin_map_id || $remember_words['end_map_id']!=$end_map_id){
            return setReturnData(ErrorCodeMap::INVALIDATE_PARAM);
        }

        $forget_words_ids = array_unique($forget_words_ids);
        foreach ($forget_words_ids as &$id){
            $id = intval($id);
            if($id < $remember_words['begin'] || $id > $remember_words['end']){
                return setReturnData(ErrorCodeMap::INVALIDATE_PARAM);
            }
        }
        //添加一条背诵日志
        $all_word_ids = array_column($remember_words['list'],'id');
        $log_model = new TaskLogModel();
        $log_model->setLogData($this->user_id,$word_count,$book_id,$begin_word_id,$end_word_id,$begin_map_id,$end_map_id,count($remember_words['list']));
        return $word_logic->reportRemember($log_model,$all_word_ids,$forget_words_ids);
    }
}