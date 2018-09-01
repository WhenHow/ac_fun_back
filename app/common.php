<?php
/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2018/7/31
 * Time: 20:23
 */

function diff_item_fetcher($original_data,$new_data){
    $ret = array();
    foreach($original_data as $key=>$val){
        if(isset($new_data[$key]) && $val!=$new_data[$key]){
            $ret[$key] = $new_data[$key];
        }
    }

    return $ret;
}


/**
 * 设置返回的数据格式
 * @param $code
 * @param $msg
 * @param $data
 * @return array
 */
function setReturnData($code,$msg = '',$data = ''){
    $code = $code == true ? 1 : $code;
    return ['code'=>$code,'msg'=>$msg,'data'=>$data];
}