<?php
/**
 * Created by PhpStorm.
 * User: yds
 * Date: 2018/9/19
 * Time: 9:41
 */
namespace app\api\controller;
use app\api\controller\Token;
use app\api\model\AttendancePlan;
use app\api\model\Userinfo as UserModel;
use app\api\model\UserExtra as UserExtraModel;
use app\api\model\AttendanceConfig as AttendConfModel;
use app\api\model\AttendanceRecord as AttendRecordModel;
use think\Db;

class AttendRecord extends Token
{
    public function getAttendRecord()
    {
        $dateday = request()->param('date');

        $map = [
            'user_id'=>$this->user_id,
            'date'=>$dateday
        ];

        $attendRecordObj = AttendRecordModel::get($map);

        if ($attendRecordObj){

            $attendRecord = json_decode(json_encode($attendRecordObj),true);

            $user = UserModel::get($this->user_id);

            $attendConf = AttendConfModel::get($user->attend_conf_id);

            $attendRecord['working_time_conf'] = $attendConf->working_time;
            $attendRecord['closing_time_conf'] = $attendConf->closing_time;
            $attendRecord['wifi_macs'] = $attendConf->wifi_macs;
            $attendRecord['current_time'] = time();

            return $this->api_suc($attendRecord);
        }else{
            return $this->api_err('无考勤记录');
        }

    }

    public function attendClock()
    {
        $type = request()->param('type');
        $clock_time = request()->param('clock_time');

        $time_tmp = date('Y-m-d',strtotime($clock_time));
        $service_time_tmp = date('Y-m-d',time());

        if ($time_tmp !== $service_time_tmp){
            return $this->api_err('打卡时间与服务器时间不匹配!');
        }
        $record = new AttendRecordModel();


        if ($type == 0){
            //$record->working_time = $
        }elseif ($type == 1){

        }
    }
}