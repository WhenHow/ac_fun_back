<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2019/2/10
 * Time: 13:17
 */

namespace api\common\logic;


use api\common\map\ErrorCodeMap;
use api\common\model\ReviewStepModel;
use think\Db;

class ReviewLogic extends BaseLogic
{
    public function __construct()
    {
    }


    public function getTodayReviewTask($user_id,$review_step){
        $step_ids = array_column($review_step,'id');
        //获得所有到期的背诵任务，每种时间间隔取一个
        $today = date('Y-m-d');
        $task_map['user_id'] = $user_id;
        $task_map['next_review_date'] = array("elt",$today);
        $task_map['current_process'] = array('in',$step_ids);
        $task_logs = Db::name('TaskLog')->where($task_map)->group('current_process')->column("id");
        return $task_logs;
    }


    public function getTodayReviewTaskDetail($user_id){
        if($this->isRememberToday($user_id)){
            return null;
        }
        //获得所有复习时间间隔
        $review_step_model = new ReviewStepModel();
        $review_step = $review_step_model->getAll();
        if(!$review_step){
            return null;
        }

        $need_remember_steps = [];
        $not_need_remember_steps = [];

        foreach ($review_step as $step){
            if($step['need_remember_first_time_word']){
                $need_remember_steps[] = $step['id'];
            }else{
                $not_need_remember_steps[] = $step['id'];
            }
        }

        $task_ids = $this->getTodayReviewTask($user_id,$review_step);
        if(!$task_ids){
            return null;
        }
        //$task_ids = array_column($task_ids,"id");

        $where['log_id'] = array("in",$task_ids);
        $word_ids = Db::name("UserTaskDetail")->where($where)->where(function($query)use ($not_need_remember_steps,$need_remember_steps){
            $query = $query->where(function($query)use($not_need_remember_steps){

                if($not_need_remember_steps){
                    $map['current_process'] = array("in",$not_need_remember_steps);
                    $map['is_remember_first_time'] = 0;
                    $query = $query->where($map);
                }

            })->whereOr(function($query)use($need_remember_steps){
                if($need_remember_steps){
                    $query->where("current_process","in",$need_remember_steps);
                }

            });
        })->column("word_id");


        return ['task_ids'=>$task_ids,'word_ids'=>$word_ids];
    }


    public function isRememberToday($user_id){
        $today = date("Y-m-d");

        $where['user_id'] = $user_id;
        $where['create_date'] = $today;

        return Db::name("UserReviewTask")->where($where)->find();
    }

    public function reportReview($user_id,$all_review_list,$forget_ids){
        if($this->isRememberToday($user_id)){
            return setReturnData(ErrorCodeMap::ALREADY_REVIEW_TODAY);
        }

        if(!$all_review_list){
            return ErrorCodeMap::ALREADY_REVIEW_TODAY;
        }

        $word_num = count($all_review_list['word_ids']);
        $task_ids = $all_review_list['task_ids'];
        $word_ids = $all_review_list['word_ids'];

        Db::startTrans();
        $log_id = $this->addReviewTask($user_id,$word_num);
        if($log_id === false){
            Db::rollback();
            return false;
        }

        if($this->addReviewTaskMap($log_id,$task_ids,$user_id) === false){
            Db::rollback();
            return false;
        }

        if($this->addReviewWordDetail($log_id,$user_id,$task_ids,$word_ids) === false){
            Db::rollback();
            return false;
        }

        //更新相关背诵日志，例如进度，下次背诵时间
        if($this->updateRememberTaskLogProcess($task_ids,$user_id,$forget_ids,$word_ids,$log_id) === false){
            Db::rollback();
            return false;
        }
        Db::commit();
        return true;

    }

    private function addReviewTask($user_id,$word_num){
        if(!$user_id){
            return false;
        }

        $current_time = time();
        $data['user_id'] = $user_id;
        $data['create_time'] = date("Y-m-d H:i:s",$current_time);
        $data['create_date'] = date("Y-m-d",$current_time);
        $data['create_year'] = date("Y",$current_time);
        $data['create_month'] = date("m",$current_time);
        $data['create_day'] = date("d",$current_time);
        $data['word_num'] = $word_num;

        return Db::name("UserReviewTask")->insertGetId($data);
    }

    private function addReviewTaskMap($review_id,$task_ids,$user_id){
        $where['id'] = array("in",$task_ids);
        $list = Db::name("TaskLog")->where($where)->field("id,current_process")->select();
        if(!$list){
            return false;
        }
        $list=$list->toArray();
        $data = [];
        array_walk($list,function ($item,$key)use(&$data,$review_id,$user_id){
            $data[] = array("user_id"=>$user_id,
                "review_id"=>$review_id,
                "task_id"=>$item['id'],
                "current_process"=>$item['current_process']);
        });

        return Db::name("ReviewTaskMap")->insertAll($data);
    }

    private function addReviewWordDetail($review_id,$user_id,$task_ids,$word_ids){
        if(!$task_ids){
            return false;
        }

        if(!$word_ids){
            return true;
        }

        $where['log_id'] = array("in",$task_ids);
        $where['word_id'] = array("in",$word_ids);
        $list = Db::name("UserTaskDetail")->where($where)->field("log_id,current_process,word_id")->select();
        if(!$list){
            return false;
        }

        $current_time = date("Y-m-d H:i:s");
        $data = [];
        foreach ($list as $log){
            $data[] = array(
                "review_log_id" => $review_id,
                "task_log_id" => $log["log_id"],
                "word_id" => $log['word_id'],
                "current_process" => $log["current_process"],
                "create_time" => $current_time,
                "user_id" => $user_id
            );
        }

        return Db::name("ReviewDetail")->insertAll($data);
    }

    private function updateRememberTaskLogProcess($task_ids,$user_id,$forget_word_ids,$word_ids,$review_id){

        if(!$task_ids){
            return false;
        }

        $tasks = Db::name("TaskLog")->where("id","in",$task_ids)
            ->column("id,current_process,next_review_date");
        if(!$tasks){
            return false;
        }

        $review_step_model = new ReviewStepModel();
        $all_steps = $review_step_model->getAll();

        $current_date = date("Y-m-d");

        foreach ($tasks as $task){
            $current_process = $review_step_model->getNextStepById($task['current_process'],$all_steps);
            $current_process_id = $current_process ? $current_process['id'] : 0;
            $next_review_date = $current_process ? date("Y-m-d",strtotime($current_date." +".$current_process['day_span']." day")) : $task['next_review_date'];
            $task_log_data = ["id"=>$task["id"],"current_process"=>$current_process_id,"next_review_date"=>$next_review_date];

            Db::name("TaskLog")->update($task_log_data);
            Db::name("UserTaskDetail")->where("log_id",$task['id'])->update(["current_process"=>$current_process_id]);
        }

        if($word_ids){
            Db::name("UserTaskDetail")->where(["user_id"=>$user_id,"word_id"=>array("in",$word_ids)])->inc("review_times");
        }

        if($forget_word_ids){
            Db::name("UserTaskDetail")->where(["user_id"=>$user_id,"word_id"=>array("in",$forget_word_ids)])->inc("forget_times");
            Db::name("ReviewDetail")->where(["review_log_id"=>$review_id,"word_id"=>array("in",$forget_word_ids)])->update(["is_remember"=>0]);
        }

        return true;
    }




}