<?php
/**
 * Created by PhpStorm.
 * User: yds
 * Date: 2018/9/13
 * Time: 16:56
 */
namespace app\index\model;
use think\Model;
class AttendanceConfig extends Model
{
    protected $pk = 'attend_conf_id';

    protected $updateTime = false;
    protected $createTime = false;
    public function setWorkingTimeAttr($value)
    {
        return strtotime('1970-01-01 '.$value);
    }
    public function setClosingTimeAttr($value)
    {
        return strtotime('1970-01-01 '.$value);
    }

    public function getWorkingTimeAttr($value)
    {
        return date('H:i:s',$value);
    }

    public function getClosingTimeAttr($value)
    {
        return date('H:i:s',$value);
    }



}