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
 * 降低数组维度
 * @param type $array 输入的数组
 * @param type $key 数组的键
 * @author Helson <helsonlovejing@gmail.com>
 */
function reduce_array_dimen($array,$key='id'){
    foreach($array as $val){
        $return[] = $val[$key];
    }
    return $return;
}


/**
 * 设置返回的数据格式
 * @param $code
 * @param $msg
 * @param $data
 * @return array
 */
function setReturnData($code,$msg = '',$data = ''){
    /*$code = $code === true ? 1 : $code;
    return ['code'=>$code,'msg'=>$msg,'data'=>$data];*/
    $code = $code === true ? 1 : $code;
    $status = $code == 1 ? true : false;
    return ['code'=>$code,'msg'=>$msg,'data'=>$data,'status'=>$status];
}

function getDateDetail($time_stamp){
    $ret['year'] = date("Y",$time_stamp);
    $ret['month'] = intval(date("m",$time_stamp));
    $ret['day'] = intval(date("d",$time_stamp));
    $ret['week'] = intval(date("W",$time_stamp));
    $ret['week_day'] = intval(date("w",$time_stamp));

    return $ret;
}