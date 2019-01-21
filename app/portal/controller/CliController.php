<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2018/12/23
 * Time: 16:19
 */

namespace app\portal\controller;
use cmf\controller\BaseController;
use think\Db;
use think\Request;

class CliController extends BaseController
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    public function addSentenceIndex(){
        //获得所有word id
        //$word_ids = Db::name("WordSentence")->field("distinct('word_index') word_index")->column('word_index');

        //return $word_ids;
        return 's';
    }
}