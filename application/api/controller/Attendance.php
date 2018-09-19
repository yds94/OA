<?php
/**
 * Created by PhpStorm.
 * User: yds
 * Date: 2018/9/17
 * Time: 14:46
 */
namespace app\api\controller;
use app\api\controller\Token;
use app\api\model\AttendancePlan;
use app\api\model\AttendanceRecord;
use app\api\model\Userinfo as UserModel;
use app\api\model\UserExtra as UserExtraModel;
use app\api\model\AttendanceConfig as AttendConfModel;
use app\api\model\AttendanceRecord as AttendRecordModel;
use think\Db;

class Attendance extends Token
{
    protected $week_type = [
        '0'=>'星期天',
        '1'=>'星期一',
        '2'=>'星期二',
        '3'=>'星期三',
        '4'=>'星期四',
        '5'=>'星期五',
        '6'=>'星期六'
    ];

    public function getConf()
    {
        $attend_conf_id = request()->param('attend_conf_id');

        $atten = AttendConfModel::get($attend_conf_id);

        $user = UserModel::all(['attend_conf_id'=>$attend_conf_id]);

        $atten['user'] = $user;
        if ($atten){
            return $this->api_suc($atten);
        }else{
            return $this->api_err('未找到配置');
        }

    }

    public function confDel()
    {
        $attend_conf_id = request()->param('attend_conf_id');

        $user = new UserModel();

        $user->save(['attend_conf_id' => '0'],['attend_conf_id' => $attend_conf_id]);

        $res = AttendConfModel::where('attend_conf_id','=',$attend_conf_id)->delete();

        if ($res){
            return $this->api_suc('删除成功!');
        }else{
            return $this->api_err('删除失败');
        }
    }

    /*public function getAttendRecord()
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

    }*/

    public function getAttendPlan()
    {
        $attend_conf_id = request()->param('attend_conf_id');
        $current_year_month = request()->param('current_year_month');

        $attend_conf = AttendConfModel::get($attend_conf_id);

        $users = UserModel::all(['attend_conf_id'=>$attend_conf_id]);
        if ($users){

            //查询到用户id  拿着用户id去查询当前用户是否有月计划,没有月计划就先添加数据占个坑,然后生成一个月计划,再更新塞进坑里
            foreach ($users as $user){
                $map = [
                    'user_id'=>$user['user_id'],
                    'current_year_month'=>$current_year_month,
                ];

                $plan = new AttendancePlan();
                $plan_info = $plan->get($map);
                //两个都是空 就要插入并更新处理  plan不为空 字段为空 要更新  两个都不空不用处理
                if (empty($plan_info) && empty($plan_info['plan_month_detail'])){
                    $plan->user_id = $user['user_id'];
                    $plan->current_year_month = $current_year_month;
                    $plan->save();
                    $plan_id = $plan->plan_id;

                    //生成数据
                    $plan->save(['plan_month_detail'=>$this->getweek($current_year_month,$plan_id)],['plan_id'=>$plan_id]);

                }elseif (!empty($plan_info) && empty($plan_info['plan_month_detail'])){
                    //生成数据

                    $plan->save(['plan_month_detail'=>$this->getweek($current_year_month,$plan_info->plan_id)],['plan_id'=>$plan_info->plan_id]);

                }
            }

            $attendancePlans = Db::table('userinfo')
                ->alias('a')
                ->join('user_extra b', 'a.user_id = b.user_id','LEFT')
                ->join('attendance_plan c','a.user_id = c.user_id','LEFT')
                ->field('a.user_id,b.realname,c.plan_id,c.current_year_month,c.plan_month_detail')
                ->where('a.attend_conf_id','=',$attend_conf_id)
                ->where('c.current_year_month','=',$current_year_month)
                ->select();

            foreach ($attendancePlans as $key=>$v){
                $attendancePlans[$key]['planDetails'] = json_decode($v['plan_month_detail'],true);

                unset( $attendancePlans[$key]['plan_month_detail']);
            }

            $res['attendancePlans'] = $attendancePlans;

        }else{
            $res['attendancePlans'] = [];
        }
        $res['attendanceConfig'] = $attend_conf;

        return $this->api_suc($res);

    }

    public function saveAllPlan()
    {
        $allPlan = request()->param('plan_list');

        $all_plan_arr = json_decode($allPlan,true);

        foreach ($all_plan_arr as $value){
            $data[] = [
                'plan_id'=>$value['plan_id'],
                'plan_month_detail'=>json_encode($value['planDetails']),
            ];
        }
        $AttendancePlan = new AttendancePlan;
        $res = $AttendancePlan->saveAll($data);

        if ($res){
            return $this->api_suc('更新成功!');
        }else{
            return $this->api_err('更新失败!');
        }

    }

    /**
     * month_date : 月份 格式:"2018-09"
     * plan_id : 月计划id
    **/
    private function getweek($month_date,$plan_id)
    {
        $start_time = strtotime($month_date);
        $start_week = date('w', $start_time);
        $total_month_day = date('t', $start_time);

        $weeks_in_month = ceil(($start_week+$total_month_day)/7);

        $month_day_arr = [];
        $start_month_day = 1;
        for($i=0;$i<$weeks_in_month;$i++) {

            for($j=0;$j<7;$j++){
                if($i ==0 && $j >= $start_week) {
                    $month_day_arr[$i][$j] = $start_month_day;
                    $start_month_day++;
                } elseif($i == 0) {
                    $month_day_arr[$i][$j] = '';
                } else {
                    $month_day_arr[$i][$j] = $start_month_day;
                    $start_month_day++;
                }

                if($start_month_day > $total_month_day){
                    break;
                }
            }
        }

        $week_type = $this->week_type;
        foreach ($month_day_arr as $value){
            foreach ($value as $k=>$v){
                if ($v){
                    $planDetails[] = [
                        'date'=>$month_date.'-'.$v,
                        'week'=>$week_type[$k],
                        'plan_id'=>$plan_id,
                    ];
                }

            }
        }

        return json_encode($planDetails);
    }


}