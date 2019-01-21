<?php
/**
 * Created by PhpStorm.
 * User: xiangwenhao
 * Date: 2015/11/17
 * Time: 15:53
 */

namespace app\Common\service;

use think\Loader;

class ExcelService
{
    private $excel_object = null;
    private $upload_config = null;

    public function _initialize()
    {

        Loader::import('Excel.PHPExcel');

        $this->excel_object = new \PHPExcel();
        $this->upload_config = array(
            'maxSize' => 100 * 1024 * 1024,
            //'rootPath'   =>    SITE_PATH.'/Uploads/',
            'savePath' => 'Excel/',
            'relativePath' => '',
            'saveName' => array('uniqid', ''),
            'exts' => array('xlsx', 'csv', 'xls', 'xlsm', 'xltx', 'xltm', 'xlsb', 'xlam'),
            'autoSub' => true,
            'subName' => array('date', 'Y-m-d'),

        );
    }


    /**
     * 设置excel某个index里面的数据
     * @param $cell_index
     * @param $data
     * @param int $sheet_index
     */
    public function setExcelCellData($cell_index, $data, $sheet_index = 0)
    {

        $this->excel_object->setActiveSheetIndex($sheet_index)
            ->setCellValue($cell_index, $data);

    }

    /**
     * 给excel的某一行插入数据
     * Author:XiangWenhao
     * @param $row_index
     * @param $data_list
     * @param int $sheet_index
     */
    public function setExcelRowData($row_index, $data_list, $sheet_index = 0)
    {
        $excel_obj = $this->excel_object->setActiveSheetIndex($sheet_index);
        $col_count = 0;
        $ascii_num = ord("A");
        foreach ($data_list as $key => $val) {
            $char_word = chr($ascii_num + $col_count);
            $cell_index = $char_word . $row_index;

            $excel_obj->setCellValue($cell_index, $val);
            $col_count++;
        }
    }

    /**
     * 设置Excel某个标签的标题
     * @param $title
     * @param int $sheet_index
     */
    public function setExcelSheetTitle($title, $sheet_index = 0)
    {
        $this->excel_object->setActiveSheetIndex($sheet_index)->setTitle($title);
    }

    /**
     * 导出excel
     * @param $export_file_name
     * @param string $excel_type
     * @throws \PHPExcel_Reader_Exception
     */
    public function excelExport($export_file_name, $excel_type = '2007', $save = false)
    {

    }

    /**
     * php上传excel文件
     * @return mixed
     * 返回数组
     * ['status'] 上传成功true 失败false
     * ['error'] 失败信息
     * ['files'] 上传文件信息 可以是多个的上传文件
     */
    public function uploadExcel()
    {
    }

    //设置上传配置
    public function setUploadConfig($config)
    {
        $this->upload_config = $config;
    }

    public function readExcel($file,$row = 1)
    {
        if (!file_exists($file)) {
            return false;
        }
        $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
        \PHPExcel_Settings::setCacheStorageMethod($cacheMethod);

        $objReader = \PHPExcel_IOFactory::createReaderForFile($file);//自动解析文件类型，不必指定到底是读取哪个类型的excel

        $timeA = date('Y-m-d H:i:s', time());
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($file);
        $timeB = date('Y-m-d H:i:s', time());

        $objWorksheet = $objPHPExcel->getActiveSheet();

        $highestRow = $objWorksheet->getHighestRow(); // 取得总行数
        $highestColumn = $objWorksheet->getHighestColumn(); // 取得总列数
        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);//把列数从ABC这种转换成1,2,3

        $data = array();
        for (; $row <= $highestRow; $row++) {//行数是以第1行开始
            for ($column = 0; $column < $highestColumnIndex; $column++) {//列数是以0列开始
                $cell_value = $objWorksheet->getCellByColumnAndRow($column, $row)->getValue();
                $data[$row][$column] = $cell_value;
            }
        }
        return $data;
    }

    public function readAllExcelSheet($file,$row = 1){
        if (!file_exists($file)) {
            return false;
        }
        $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
        \PHPExcel_Settings::setCacheStorageMethod($cacheMethod);

        $objReader = \PHPExcel_IOFactory::createReaderForFile($file);//自动解析文件类型，不必指定到底是读取哪个类型的excel

        $timeA = date('Y-m-d H:i:s', time());
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($file);
        $timeB = date('Y-m-d H:i:s', time());


        $ret = [];
        $sheets = $objPHPExcel->getAllSheets();
        foreach($sheets as $objWorksheet){

            $highestRow = $objWorksheet->getHighestRow(); // 取得总行数
            $highestColumn = $objWorksheet->getHighestColumn(); // 取得总列数
            $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);//把列数从ABC这种转换成1,2,3

            $data = array();
            for ($row=1; $row <= $highestRow; $row++) {//行数是以第1行开始
                for ($column = 0; $column < $highestColumnIndex; $column++) {//列数是以0列开始
                    $cell_value = $objWorksheet->getCellByColumnAndRow($column, $row)->getValue();
                    $data[$row][$column] = $cell_value;
                }
            }
            $ret[$objWorksheet->getTitle()] = $data;
        }
        return $ret;
    }
}