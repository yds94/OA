<?php
/**
 * Created by PhpStorm.
 * User: yds
 * Date: 2018/9/19
 * Time: 9:41
 */
namespace app\api\controller;
use app\api\controller\Token;
use app\api\model\AttendancePlan as AttendPlanModel;
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
        $date_tmp = explode('-',$dateday);
        $date_month = $date_tmp['0'].'-'.$date_tmp['1'];

        //看当前用户有没有考勤配置
        $user_attendconf = $this->user_info->attend_conf_id;
        $attendConf = AttendConfModel::get($user_attendconf);
        if(!$user_attendconf || !$attendConf){
            return $this->api_err('当前账户无考勤配置!');
        }

        //获取对应的考勤记录
        $map_record = [
            'user_id'=>$this->user_id,
            'date'=>$dateday
        ];
        $attendRecordObj = AttendRecordModel::get($map_record);

        //为空添加一条数据
        if (!$attendRecordObj){

            //获取对应月份的考勤计划
            $map_plan = [
                'user_id'=>$this->user_id,
                'current_year_month'=>$date_month
            ];
            $attend_month_plan = AttendPlanModel::get($map_plan);
            $plan_month_detail = $attend_month_plan->plan_month_detail;
            if (!$plan_month_detail){
                return $this->api_err('暂未排班!');
            }
            foreach ($plan_month_detail as $key=>$value){
                if (strtotime($dateday) == strtotime($value['date'])){

                    //记录当天的计划数据
                    $today_record_conf = [
                        'leavetime'=> $value['leavetime'],
                        'date' => $dateday,
                        'user_id' => $this->user_id,
                        'status' => $value['status'],
                        'working_time_config' => $attendConf->working_time,
                        'closing_time_config' => $attendConf->closing_time
                    ];
                    break;
                }
            }
            if(time() > strtotime($dateday.' 12:00:00')){
                $today_record_conf['is_miss_amcard']= 1;
            }
            if(time() > strtotime($dateday.' 23:59:59')){
                $today_record_conf['is_miss_pmcard']= 1;
            }

            $record = new AttendRecordModel();
            $record->data($today_record_conf);
            $record->save();
            $attendRecordObj = AttendRecordModel::get($record->rec_id);

        }else{
            if((time() > strtotime($dateday.' 12:00:00')) && empty($attendRecordObj->working_time)){
                $attendRecordObj->is_miss_amcard = 1;
                $attendRecordObj->save();
            }
            if((time() > strtotime($dateday.' 23:59:59')) && empty($attendRecordObj->closing_time)){
                $attendRecordObj->is_miss_pmcard = 1;
                $attendRecordObj->save();
            }
        }

        $attendRecord = json_decode(json_encode($attendRecordObj),true);
        $attendRecord['wifi_macs'] = $attendConf->wifi_macs;
        $attendRecord['current_time'] = time();

        return $this->api_suc($attendRecord);

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