<?php
/**
 * Created by PhpStorm.
 * User: yds
 * Date: 2018/9/10
 * Time: 13:42
 * Des: 用户管理类
 */
namespace app\api\controller;
use app\api\controller\Token;
use app\api\model\Userinfo as UserModel;
class User extends Token
{
    //获取用户信息
    public function getUserinfo()
    {
        $user_id = $this->user_id;
        $user_info = $this->get_user_info($user_id);

        return $this->api_suc($user_info);

    }

    //登出
    public function loginOut()
    {
        $user_id = $this->user_id;
        $user = UserModel::get($user_id);

        $user->registration_id = "";
        $user->update_time = time();
        $res = $user->save();

        if ($res){

            return $this->api_suc();

        }else{
            return $this->api_err('退出失败!');
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

        $user_dept_info = $user->getuserdep->getData();

        $user_info = $user->getData();

        $user_new_info = array_merge($user_info,$user_extra_info);

        $user_new_info['roles'] = $user_role_info;
        $user_new_info['dept'] = $user_dept_info;
        unset($user_new_info['password']);
        unset($user_new_info['token']);
        unset($user_new_info['dept_id']);

        return $user_new_info;
    }
}