<?php
/**
 * Created by PhpStorm.
 * User: yds
 * Date: 2018/8/29
 * Time: 14:20
 */
namespace app\api\model;
use think\Model;
class Userinfo extends Model
{
    protected $pk = 'user_id';
    public function getroles()
    {
        return $this->belongsToMany('Role','user_role','role_id','user_id');
    }

    public function getuserinfo()
    {
        return $this->hasOne('UserExtra','user_id','user_id');
    }

    public function getuserdep()
    {
        return $this->hasOne('Department','dept_id','dept_id');
    }
}