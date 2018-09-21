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
use app\api\model\UserExtra as UserExtraModel;
Use think\Request;

class User extends Token
{
    //获取用户信息
    public function getUserinfo()
    {
        $user_id = $this->user_id;
        $user_info = $this->get_user_info($user_id);

        return $this->api_suc($user_info);

    }

    public function saveUserinfo()
    {
        //$head_img = request()->param('head_img');
        $info['birthday'] = request()->param('birthday');
        $info['sex'] = request()->param('sex');
        $info['phone'] = request()->param('phone');
        $info['email'] = request()->param('email');
        $info['bank_num'] = request()->param('bank_num');
        $info['bank_open_address'] = request()->param('bank_open_address');

        $file = request()->file('head_img');
        // 移动到框架应用根目录/public/static/uploads/ 目录下
        if($file){
            $file_info = $file->move(ROOT_PATH . 'public' . DS .'static'.DS. 'uploads');
            if($file_info){

                $head_img = DS.'static'.DS. 'uploads'.DS.$file_info->getSaveName();
                $info['head_img'] = $head_img;
            }
        }

        $user_id = $this->user_id;
        $user_extra = new UserExtraModel();
        $res = $user_extra->allowField(true)->save($info,['user_id' => $user_id]);

        if ($res){
            return $this->api_suc();
        }else{
            return $this->api_err('保存失败!');
        }
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


}