<?php
namespace app\api\controller;
use app\api\controller\Token;
class Index extends Token
{
    public function index()
    {
        return $this->api_suc();
    }
}
