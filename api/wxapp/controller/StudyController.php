<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2018/11/26
 * Time: 2:57
 */

namespace api\wxapp\controller;


use api\common\logic\UserWordLogic;
use api\common\map\ErrorCodeMap;
use app\common\model\TaskLogModel;
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

    public function remember(){
        $book_id = $this->request->param('book_id',1);
        $word_count = $this->request->param('count',100);

        $word_logic = new UserWordLogic();

        $word_list = $word_logic->getUserTodayWords($this->user_id,$book_id,$word_count);
        return setReturnData(ErrorCodeMap::SUCCESS,'',$word_list);
    }


    public function report_remember(){
        $book_id = $this->request->param('book_id',1);
        $word_count = $this->request->param('word_num',100);
        $begin_word_id = $this->request->param('begin',0);
        $end_word_id = $this->request->param('end',0);

        $begin_map_id = $this->request->param('begin_map_id',0);
        $end_map_id = $this->request->param('end_map_id',0);

        $forget_words_ids = $this->request->param('forget_words',"");
        $forget_words_ids = explode(",",$forget_words_ids);

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