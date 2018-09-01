<?php
namespace app\index\controller;
use app\index\controller\Base;
class Index extends Base
{
    public function index()
    {
        $this->isLogin();
        return $this->view->fetch();
    }
}
