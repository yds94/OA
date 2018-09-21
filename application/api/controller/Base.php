<?php
/**
 * 基类控制器
 *
*/
namespace app\api\controller;
use think\Controller;
use think\exception\HttpResponseException;
class Base extends Controller
{
    protected function api_suc($data = null,$code = 1,$msg = "")
    {
        $code  = (int) $code;
        $msg   = (string) $msg;
        if (!$data){
            $data = (object)$data;
        }
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];

        throw new HttpResponseException(json($result));
    }

    protected function api_err($msg,$data=null,$code = 0)
    {
        $code  = (int) $code;
        $msg   = (string) $msg;
        /*if (!$data){
            $data = (object)$data;
        }*/
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];

        throw new HttpResponseException(json($result));
    }
}