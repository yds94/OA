<?php
/**
 * Created by PhpStorm.
 * User: yds
 * Date: 2018/9/17
 * Time: 16:16
 */
namespace app\api\controller;
use app\api\controller\Token;
use app\api\model\AttendanceConfig;
use app\api\model\AttendanceRecord;
use app\api\model\Userinfo as UserModel;
use app\api\model\AttendanceConfig as AttendConfModel;
use app\api\model\AttendanceRecord as AttendRecordModel;
use think\Db;
class Department extends Token
{
    public function getMemberList()
    {
        $dept_id = request()->param('dept_id');

        if ($dept_id){
            $map = [
                'dept_id'=>$dept_id,
            ];

            $users = UserModel::all($map);

            foreach ($users as $user) {

                $res[] = $this->get_user_info($user['user_id']);

            }

        }else{
            $users = UserModel::all();
            foreach ($users as $user) {

                $res[] = $this->get_user_info($user['user_id']);

            }
        }

        if ($users){
            return $this->api_suc($res);
        }else{
            return $this->api_err('无任何成员!');
        }

    }

    public function getSelfDeptAttendConf()
    {
        $user_info = $this->user_info;

        $attend_conf = AttendConfModel::all(['dept_id'=>$user_info['dept_id']]);


        if ($attend_conf){

            foreach ($attend_conf as $k=>$v){

                $count = Db::table('userinfo')->where('attend_conf_id','=',$v['attend_conf_id'])->count();

                $attend_conf[$k]['memberCount'] = $count;
            }

            return $this->api_suc($attend_conf);
        }else{
            return $this->api_err('当前部门无上班时间配置',[],1);
        }

    }

    public function setConf()
    {
        $attend_conf_id = request()->param('attend_conf_id');
        $name = request()->param('name');
        $working_time = request()->param('working_time');
        $closing_time = request()->param('closing_time');
        $user_ids = request()->param('user_ids');
        $wifi_macs = request()->param('wifi_macs');
        $dept_id = request()->param('dept_id');

        //添加配置的数据
        $conf_data = [
            'name'=>$name,
            'working_time'=>$working_time,
            'closing_time'=>$closing_time,
            'wifi_macs'=>$wifi_macs,
            'dept_id'=>$dept_id
        ];

        $attend_conf = new AttendConfModel();

        //attend_conf_id为空执行添加操作,不为空就更新
        if (empty($attend_conf_id)){

            //添加数据
            $attend_conf->data($conf_data,true);
            $attend_conf->save();
            $res = $attend_conf->getData();

            //获取到插入的id
            $attend_conf_id = $attend_conf->attend_conf_id;


            //判断是否传入user_ids
            if ($user_ids){
                //处理传入的数据
                $uid_arr = explode(',', $user_ids);

                foreach ($uid_arr as $v){
                    $user_data[] =[
                        'user_id'=>$v,
                        'attend_conf_id'=>$attend_conf_id,
                    ];
                }

                $user = new UserModel();

                $user_update = $user->isUpdate()->saveAll($user_data);

                if(!$user_update){

                    return $this->api_err('用户配置更新失败!');
                }

            }

        }else{
            //更新
            $attend_conf->allowField(true)->save($conf_data,['attend_conf_id' => $attend_conf_id]);
            $res = $attend_conf->getData();

            $user = new UserModel();
            //先把所有的配置id置0,再更新选择的用户id
            $user->where('attend_conf_id', $attend_conf_id)->update(['attend_conf_id' => '0']);

            //判断是否传入user_ids
            if ($user_ids){
                //处理传入的数据
                $uid_arr = explode(',', $user_ids);

                foreach ($uid_arr as $v){
                    $user_data[] =[
                        'user_id'=>$v,
                        'attend_conf_id'=>$attend_conf_id,
                    ];
                }

                $user_update = $user->isUpdate()->saveAll($user_data);

                if(!$user_update){
                    return $this->api_err('用户配置更新失败!');
                }

            }

        }

        if ($res){
            return $this->api_suc('更新成功!');
        }else{
            return $this->api_err('更新失败!');
        }
    }



}