<?php
namespace app\index\controller;
use think\Controller;
use think\Session;
class Base extends Controller
{
    protected $user_id;
    protected $user_info;
    protected function _initialize()
    {
        $this->user_id = Session::get('user_id');
        $this->user_info = Session::get('user_info');

        //获取/模块/控制器/方法名
//        if(strpbrk(request()->url(),'?')) {
//            $page_url = substr(request()->url(),0,strrpos(request()->url(),'?'));
//        }else{
//            $page_url = request()->url();
//        }
        $page_url = '/'.request()->module().'/'.strtolower(request()->controller()).'/'.lcfirst(request()->action());

        //获取所有的权限
        $perm_all_arr = Session::get('perm_all_user_id');

        //登陆控制
        if(!$this->user_id){

            $this -> error('用户未登陆,无权访问',url('login/login'),'',1);
        }

        //权限控制,排除admin账号
        if (!in_array($page_url, $perm_all_arr) && !in_array('admin',$this->user_info)){

            $this -> error('用户权限不足,无权访问!',url('/index/index/index'),'',1);
        }
    }

}