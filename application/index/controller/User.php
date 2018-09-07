<?php
namespace app\index\controller;
use app\index\controller\Base;
use app\index\model\Userinfo as UserModel;
use app\index\model\UserExtra as UserExtraModel;
use app\index\model\Role as RoleModel;
use think\Session;
class User extends Base
{
    public function index()
    {
        $user_id = $this->user_id;


        dump($this->user_info);
        die;
        return $this->view->fetch();
    }
}
