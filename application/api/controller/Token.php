<?php
/**
 * token 验证,请求头获取
 * user_id : 用户id
 */
namespace app\api\controller;
use app\api\model\Userinfo as UserModel;
use app\api\controller\Base;
class Token extends Base
{
    protected $user_id = null;
    protected $user_info = null;
    protected function _initialize(){
        //获取请求头token
        $token = request()->header('token');

        //判断是否为空
        if (!$token){
            return   $this->api_err('请登录');
        }

        //查询token
        $map = [
            'token' => $token,
        ];
        $user_info = UserModel::get($map);

        if(!$user_info){
          return   $this->api_err('用户在其他地方登录,请重新登录','',3);

        }else{
            $this->user_id = $user_info->user_id;
            $this->user_info = $user_info;
        }

    }

    //组装用户信息
    protected function get_user_info($user_id)
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