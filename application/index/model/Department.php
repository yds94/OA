<?php
/**
 * Created by PhpStorm.
 * User: yds
 * Date: 2018/8/29
 * Time: 14:20
 */
namespace app\index\model;
use think\Model;
class Department extends Model
{
    protected $pk = 'dept_id';

    protected $updateTime = false;
    protected $createTime = false;

}