<?php
/**
 * Created by PhpStorm.
 * User: yds
 * Date: 2018/9/20
 * Time: 9:58
 */
namespace app\index\controller;
use think\Controller;
use app\index\model\Userinfo as UserModel;
use app\index\model\UserExtra as UserExtraModel;
use app\index\model\Role as RoleModel;
use app\index\model\Department as DepartModel;
use app\index\model\AttendanceConfig as AttenConfModel;
use app\index\model\UserRole as UserRoleModel;
use think\Session;
use think\Db;
use think\Request;
class Test extends Controller
{
    public function index()
    {
        $str = "2018-09-01";
        $arr = explode('-',$str);
        echo phpinfo();
    }
}