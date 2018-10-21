<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2018/9/24
 * Time: 19:13
 */

namespace api\wxapp\controller;


use api\common\logic\WordLogic;
use api\common\map\ErrorCodeMap;
use think\Request;

class WordController extends BaseController
{

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    public function getBookList(){
        $book_id = intval($this->request->param('book_id',config('DEFAULT_BOOK_ID')));
        if($book_id <= 0){
            $this->error(setReturnData(ErrorCodeMap::INVALIDATE_PARAM));
        }

        $list_logic = new WordLogic();
        $list = $list_logic->getAllWordList($book_id);
        $this->success("",$list);
    }




    public function getListWords(){
        $list_id = intval($this->request->param('list_id',0));
        if($list_id <= 0){
            $this->error(setReturnData(ErrorCodeMap::INVALIDATE_PARAM));
        }

        $list_logic = new WordLogic();
        $list = $list_logic->getListWords($list_id);
        $this->success("",$list);
    }
}