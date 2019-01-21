<?php
/**
 * Created by PhpStorm.
 * User: dwj
 * Date: 2018/9/22
 * Time: 12:36
 */
namespace app\script\command;

use api\common\map\ErrorCodeMap;
use app\common\model\WordBookListModel;
use app\common\model\WordBooksModel;
use app\common\model\WordListMapModel;
use app\common\model\WordsModel;
use app\Common\service\ExcelService;
use app\Common\service\HttpHelper;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class BasicModel{
    public function toArray(){
        return get_object_vars($this);
    }
}

class Word extends BasicModel {

    public $is_marked = 0;
    public $word = "";
    public $part_of_speech = "";
    public $phonetic_alphabet = "";
    public $translation = "";

}

Class BookList extends BasicModel {
    public $name = "";
    public $list_alias = "";
    public $sort_index = 1;
    public $book_id = 0;
}



class WordListMap extends  BasicModel {
    public $word = "";
    public $word_id = 0;
    public $book_id = 0;
    public $list_id = 0;
    public $sort_index = 1;

}

class WordScript extends Command
{
    const CODE_ROW = 1;
    const LIST_NAME_ROW = 2;

    const MARKED_COL = 0;
    const ID_COL = 1;
    const WORD_COL = 2;
    const VOICE_COL = 3;
    const SPEECH_COL = 4;
    const TRANSLATION_COL = 5;
    
    const BOOK_LIST_NAME_COL = 1;
    const BOOK_LIST_NAME_COL_2 = 0;

    const BOOK_ID = 1;

    private  $out_put = null;

    public function configure()
    {
        ini_set('memory_limit','2048M');
        set_time_limit(0);
        $this->setName('word');
        $this->setName('s');
        $this->setName('si');
    }

    public function phraseWordExcel($excel_path){
        $this->out_put->writeln("importing $excel_path");

        $excel_service = new ExcelService();
        $data = $excel_service->readAllExcelSheet($excel_path);
        if(!$data){
            $this->out_put->writeln("read excel failed");
            return false;
        }

        foreach($data as $sheet){
            //添加book_list
            $book_list_name = $sheet[self::LIST_NAME_ROW][self::BOOK_LIST_NAME_COL];
            $book_list_name = $book_list_name ? $book_list_name : $sheet[self::LIST_NAME_ROW][self::BOOK_LIST_NAME_COL_2];

            $add_book_list_ret = $this->importWordList($book_list_name,self::BOOK_ID);
            if($add_book_list_ret['status'] !== true && $add_book_list_ret['code'] !== ErrorCodeMap::DATA_EXIST){
                $this->out_put->writeln("add book list: {$book_list_name}  failed ");
                return $add_book_list_ret;
            }
            $book_list_id = $add_book_list_ret['data'];
            $this->out_put->writeln("add book list: {$book_list_name}  successfully ");

            foreach ($sheet as $row_num => $row_data){
                if($row_data[self::TRANSLATION_COL] == null){
                    continue;
                }
                //添加单词
                $this->out_put->writeln("add word: {$row_data[self::WORD_COL]}");
                $add_word_ret = $this->importOneWord($row_data[self::WORD_COL],$row_data[self::TRANSLATION_COL],$row_data[self::VOICE_COL],$row_data[self::SPEECH_COL],$row_data[self::MARKED_COL]);
                if($add_word_ret['status'] !== true && $add_word_ret['code'] !== ErrorCodeMap::DATA_EXIST){
                    $this->out_put->writeln("add word : {$row_data[self::WORD_COL]}  failed ");
                    return $add_word_ret;
                }
                
                $word_id = $add_word_ret['data'];
                //添加单词、单词表关系
                $add_map_ret = $this->makeWordListMap($row_data[self::WORD_COL],$word_id,self::BOOK_ID,$book_list_id,$row_data[self::ID_COL]);
                if($add_map_ret['status'] !== true && $add_map_ret['code'] !== ErrorCodeMap::DATA_EXIST){
                    $this->out_put->writeln("add word map : {$row_data[self::WORD_COL]}  failed ");
                    return $add_word_ret;
                }
            }

            //更新单词表数据
            $list_model = new WordBookListModel();
            $list_model->countWords($book_list_id);
        }
        //更新单词书数据
        $book_model = new WordBooksModel();
        $book_model->countWords(self::BOOK_ID);
        return false;
    }

    private function importWordList($word_list_name,$book_id){
        $data_model = new BookList();
        $data_model->book_id = $book_id;
        $data_model->name = $word_list_name;

        $list_model = new WordBookListModel();
        return $list_model->addOne($data_model->toArray());
    }

    private function importOneWord($word,$translation,$phonetic_alphabet,$part_of_speech,$is_marked){
        $data_model = new Word();
        $data_model->word = $word;
        $data_model->translation = $translation;
        $data_model->phonetic_alphabet = $phonetic_alphabet;
        $data_model->part_of_speech = $part_of_speech;
        $data_model->is_marked = $is_marked ? 1 : 0;

        $word_model = new WordsModel();
        return $word_model->addOne($data_model->toArray());
    }

    private function makeWordListMap($word,$word_id,$book_id,$list_id,$sort_index){
        $data_model = new WordListMap();
        $data_model->word = $word;
        $data_model->word_id = $word_id;
        $data_model->book_id = $book_id;
        $data_model->list_id = $list_id;
        $data_model->sort_index = $sort_index;

        $model = new WordListMapModel();
        return $model->addOne($data_model->toArray());

    }


    public function execute(Input $in,Output $out){
        $this->out_put = $out;
        $command = $in->getFirstArgument();
        switch ($command){
            case 'word':
                $this->phraseWordExcel(CMF_ROOT."/words/3.xlsx");
                break;
            case 'si':
                $this->sentenceIndex();
                break;
            case "s":
                $this->fillSentence();
                break;
        }
    }


    public function sentenceIndex(){
        $list = Db::name('WordSentence')->field('id,word_id')->select()->toArray();

        $max = [];
        foreach ($list as $val){
            $id = $val['id'];
            $word_id = $val['word_id'];
            $max_index = isset($max[$word_id]) ? $max[$word_id] + 1 : 1;
            $max[$word_id] = $max_index;

            echo("\r\n id is $id max is $max_index");
            Db::name("WordSentence")->where("id",$id)->update(['sort_index'=>$max_index]);

        }



    }


    public function fillSentence(){
        //获得所有单词
        $word_list = Db::name("words")->where(['has_sentence'=>0])->column("id,word","id");


        foreach ($word_list as $id=>$word){
            $true_word = $this->getRealWord($word);
            if(!$true_word){
                echo "failed word is {$word}\r\n";
                continue;
            }
            echo "get sentence word is {$word}\r\n";

            $sentence = $this->getJinshanStence($true_word,$id);
            if(!$sentence){
                echo "get sentence failed word is {$word}\r\n";
                continue;
            }

            Db::name("WordSentence")->insertAll($sentence);
            Db::name("words")->where(['id'=>$id])->update(['has_sentence'=>1]);
        }

    }

    private function getJinshanStence($word,$id=0){
        $url = "http://www.iciba.com/index.php?a=getWordMean&c=search&word={$word}";
        $response = HttpHelper::curl($url);
        $response_data = json_decode($response->getBody(),true);
        if(!$response_data){
            return [];
        }

        $ret = [];
        if(isset($response_data['sentence'])){
            array_walk($response_data['sentence'],function($sentence)use(&$ret,$id){
                if(isset($sentence['Network_cn']) && isset($sentence['Network_en'])){
                    $ret[] = array("sentence"=>$sentence['Network_en'],'translation'=>$sentence['Network_cn'],"word_id"=>$id);
                }
            });
        }

        if(isset($response_data['jushi'])){
            array_walk($response_data['jushi'],function($sentence)use(&$ret,$id){
                if(isset($sentence['english']) && isset($sentence['chinese'])){
                    $ret[] = array("sentence"=>$sentence['english'],'translation'=>$sentence['chinese'],"word_id"=>$id);
                }
            });
        }

        return $ret;
    }

    private function getRealWord($word){
        preg_match('/[\w]+[-= ]{0,1}[\w]+/',$word,$true_word);
        if(!$true_word){
            return false;
        }

        return $true_word[0];
    }
}