<?php
/**
 * Created by PhpStorm.
 * User: yds
 * Date: 2018/9/20
 * Time: 12:00
 */
use app\api\controller\Base;
use app\api\model\Userinfo as UserModel;
class Test extends Base
{

    public function test()
    {
        return $this->api_suc('测试成功!');
    }
}