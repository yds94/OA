<?php
/**
 * Created by PhpStorm.
 * User: yds
 * Date: 2018/9/7
 * Time: 14:16
 */
namespace app\api\controller;
use app\api\controller\Base;
use app\api\model\Userinfo as UserModel;
class Login extends Base
{
    //登陆
    public function login()
    {
        $username = request()->param('username');
        $password = request()->param('password');
        $registration_id = request()->param('registration_id');

        $map = [
            'username'=>$username,
            'password'=>$password
        ];

        $user_info = UserModel::get($map);

        if ($user_info){

            if ($user_info->is_disable == 1){

                return $this->api_err('账户处于关闭状态,请联系管理员');
            }

            $token = md5($user_info->username.time());

            //更新token和registration_id
            $user_info->token = $token;
            $user_info->registration_id = $registration_id;
            $res = $user_info->save();

            if ($res){
                //返回登陆后的用户信息
                $data['token'] = $token;
                $data['userinfo'] = $this->get_user_info($user_info->user_id);
                return $this->api_suc($data);

            }else{
                return $this->api_err('登陆失败!');
            }

        }else{
            return $this->api_err('用户名或密码错误,请重新登陆!');
        }

    }

    //组装用户信息
    private function get_user_info($user_id)
    {
        //查询用户信息
        $user =  UserModel::get($user_id);

        //获取用户的详细信息
        $user_role_info = $user->getroles;

        $user_extra_info = $user->getuserinfo->getData();

        //$user_dept_info = $user->getuserdep->getData();
        $user_dept_info = $user->getuserdep;

        $user_info = $user->getData();

        $user_new_info = array_merge($user_info,$user_extra_info);

        $user_new_info['roles'] = $user_role_info;
        $user_new_info['dept'] = $user_dept_info;
        unset($user_new_info['password']);
        unset($user_new_info['token']);
        unset($user_new_info['dept_id']);

        return $user_new_info;
    }

    public function test()
    {
        return $this->api_suc('测试成功!');
    }
}