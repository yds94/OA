<?php
namespace app\api\controller;
use think\Controller;
use think\Session;
use think\exception\HttpResponseException;
class Base extends Controller
{
    protected function _initialize(){
        //Session::set('uid','ceshisession'); //设置session
        Session::delete('uid');
        $uid = session('uid');
        if($uid == null){
          return   $this->api_err('请登录',$_POST);
        }
    }
    protected function api_suc($data = "",$code = 1,$msg = "")
    {
        $code  = (int) $code;
        $msg   = (string) $msg;
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];

        throw new HttpResponseException(json($result));
    }

    protected function api_err($msg,$data="",$code = 0)
    {
        $code  = (int) $code;
        $msg   = (string) $msg;
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];

        throw new HttpResponseException(json($result));
    }
}