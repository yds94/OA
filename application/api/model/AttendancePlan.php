<?php
/**
 * Created by PhpStorm.
 * User: yds
 * Date: 2018/9/17
 * Time: 16:57
 */
namespace app\api\model;
use think\Model;
class AttendancePlan extends Model
{
    protected $pk = 'plan_id';

    protected $updateTime = false;
    protected $createTime = false;

    /*public function setWorkingTimeAttr($value)
    {
        return strtotime('1970-01-01 ' . $value);
    }*/

    public function getPlanMonthDetailAttr($value)
    {
        return json_decode($value,true);
    }

}