<?php
namespace app\index\controller;
use app\index\controller\Base;
use think\Request;
use app\index\model\User as UserModel;
use think\Session;
class User extends Base
{
    public function login()
    {
        $this->alreadyLogin();
        return $this->view->fetch();
    }

    public function checkLogin(Request $request)
    {
        //返回参数
        $status = 0;
        $result = '';
        $data = $request->param();

        //创建验证规则
        $rule = [
            'name|用户名'=>'require',
            'password|密码'=>'require',
            'verify|验证码'=>'require|captcha',
        ];

        //自定义验证信息
        $msg = [
            'name'=>['require'=>'用户名不能为空,请检查'],
            'password'=>['require'=>'密码不能为空,请检查'],
            'verify'=>[
                'require'=>'验证码不能为空,请检查',
                'captcha'=>'验证码错误'
                ]
        ];

        //验证
        $result = $this->validate($data,$rule,$msg);

        //验证通过
        if($result === true){

            //构造查询条件
            $map = [
                'name'=>$data['name'],
                'password'=>md5($data['password']),
            ];

            //查询用户信息
           $user =  UserModel::get($map);

           if ($user == null){

               $result ="当前账户不存在";
           }else{
               $status = 1;
               $result = '验证通过';
               //设置session
               Session::set('user_id',$user->id);
               Session::set('user_info',$user->getData());
           }
        }


        return ['status'=>$status,'message'=>$result,'data'=>$data];
    }

    public function loginout()
    {
        //注销session
        Session::delete('user_id');
        Session::delete('user_info');
        $this->success('注销登陆正在返回','user/login',"",0);
    }
}