<?php
namespace app\api\controller;
use app\api\controller\Base;
use think\Session;
class Index extends Base
{
    public function index()
    {

        $data = $_POST;
        return $this->api_suc($data);
    }
}
