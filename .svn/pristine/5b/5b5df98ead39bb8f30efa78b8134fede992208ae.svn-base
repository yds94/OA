<?php
/**
 * Created by PhpStorm.
 * User: yds
 * Date: 2018/8/29
 * Time: 14:20
 */
namespace app\index\model;
use think\Model;
class UserExtra extends Model
{
    protected $pk = 'user_id';

    public function getSexAttr($value)
    {
        $status = [1=>'男',2=>'女'];
        return $status[$value];
    }
}