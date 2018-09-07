<?php
namespace app\index\controller;
use app\index\controller\Base;
use app\api\model\Userinfo as UserModel;
use app\api\model\Role as RoleModel;
use think\Session;
class Index extends Base
{
    public function index()
    {
        /*dump(Session::get('user_id'));
        dump(Session::get('user_info'));
        dump(Session::get('menu_user_id'));
        dump(Session::get('perm_all_user_id'));
        die;*/
        return $this->view->fetch();
    }
}
