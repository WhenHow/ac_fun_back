<?php
/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2018/7/27
 * Time: 17:06
 */
$group_array = [
    ['group_id'=>3,'device_id'=>3,'agency_id'=>2],
    ['group_id'=>4,'device_id'=>4,'agency_id'=>5],
    ['group_id'=>5,'device_id'=>5,'agency_id'=>8],
    ['group_id'=>6,'device_id'=>6,'agency_id'=>13],
];



foreach ($group_array as $val){
    $visitor_num = 0;

    for($i=1;$i<=70;$i++){
        $month = floor($i/10 +1);
        $day = floor($i/3 + 7);
        $hour = $day;
        if($day < 10){
            $day = "0{$day}";
            $hour = $day;
        }

        elseif($day>23){
            $hour = 23;
        }

        $datetime_str = "2018-0{$month}-{$day} {$hour}:00:00";
        $datetime = strtotime($datetime_str);
        if(!$datetime){
            return;
        }

        $in_num = rand(2,5);
        $out_num = rand(2,5);
        $is_last = $i == 70 ? 1 : 0;
        add_one_log($in_num,$out_num,$val['group_id'],$val['device_id'],$visitor_num,$datetime,$val['agency_id'],$is_last);
    }
}


function add_one_log($in_num,$out_num,$group_id,$device_id,&$visitor_num,$create_time,$agency_id,$is_last = 0){
    $str = add_agency_log($in_num,$out_num,$group_id,$device_id,$visitor_num,$create_time,$agency_id,$is_last);
    $str2 = add_volume($device_id,$in_num,$out_num,$create_time);

    echo "{$str}<br/><br/>{$str2}<br/><br/>";
}


function add_agency_log($in_num,$out_num,$group_id,$device_id,&$visitor_num,$create_time,$agency_id,$is_last = 0){
    $data = getTimeSpan($create_time);
    $create_time = date('Y-m-d H:i:s',$create_time);

    $add_year = $data['add_year'];
    $add_month = $data['add_month'];
    $add_day = $data['add_day'];
    $add_hour = $data['add_hour'];
    $add_week = $data['add_week'];
    $add_week_day = $data['add_week_day'];

    $visitor_num = $visitor_num + $in_num - $out_num;
    $visitor_num = $visitor_num >0 ? $visitor_num : 0;

    return "INSERT INTO `cl_visitor_agency_log` 
    (`id`, `agency_id`, `visitor_num`, `last_update_time`, `is_last_record`, `in_num`, `exit_num`, `volume_id`, `group_id`, `device_id`, `add_year`, `add_month`, `add_day`, `add_hour`, `add_week`, `add_week_day`) 
    VALUES ('', '{$agency_id}', '{$visitor_num}', '{$create_time}', '{$is_last}', '{$in_num}', '{$out_num}', '0', '{$group_id}', '{$device_id}', '{$add_year}', '{$add_month}', '{$add_day}', '{$add_hour}', '{$add_week}', '{$add_week_day}');";
}


function add_volume($device_id,$enter_total,$leave_total,$create_time){
    $data = getTimeSpan($create_time);
    $create_time = date('Y-m-d H:i:s',$create_time);

    $add_year = $data['add_year'];
    $add_month = $data['add_month'];
    $add_day = $data['add_day'];
    $add_hour = $data['add_hour'];
    $add_week = $data['add_week'];
    $add_week_day = $data['add_week_day'];

    return "INSERT INTO `cl_visitor_volume` (`id`, `device_id`, `enter_total`, `leave_total`, `current_total`, `ip_address`, `create_time`, `add_year`, `add_month`, `add_day`, `add_hour`, `add_week`, `add_week_day`) VALUES 
            ('', '{$device_id}', '{$enter_total}', '{$leave_total}', '0', '127.0.0.1', '$create_time', '{$add_year}', '{$add_month}', '{$add_day}', '{$add_hour}', '{$add_week}', '{$add_week_day}');";

}

function getTimeSpan($time_stamp = ""){
    if($time_stamp <= 0){
        $time_stamp = time();
    }

    $data['add_year'] = date('Y',$time_stamp);//年
    $data['add_month'] = intval(date('m',$time_stamp));//月
    $data['add_day'] = intval(date('d',$time_stamp));//日
    $data['add_hour'] = intval(date('H',$time_stamp));//小时
    $data['add_week'] = intval(date('W',$time_stamp));//第几周
    $data['add_week_day'] = date('w',$time_stamp);//周几
    $data['add_week_day'] = $data['add_week_day'] == 0 ? 7 : $data['add_week_day'];

    return $data;
}