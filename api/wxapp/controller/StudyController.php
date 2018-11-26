<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2018/11/26
 * Time: 2:57
 */

namespace api\wxapp\controller;


use think\Request;

class StudyController extends BaseController
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    public function remember(){
        $book_id = $this->request->param('book_id',1);
        $word_count = $this->request->param('count',10);
        $user_id = $this->
    }
}