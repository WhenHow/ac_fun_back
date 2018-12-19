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
        $word_count = $this->request->param('count',10);

        $word_logic = new UserWordLogic();
        $word_list = $word_logic->getUserTodayWords($this->user_id,$book_id,$word_count);
        return setReturnData(ErrorCodeMap::SUCCESS,'',$word_list);
    }
}