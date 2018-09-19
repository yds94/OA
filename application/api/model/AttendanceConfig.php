<?php
/**
 * Created by PhpStorm.
 * User: yds
 * Date: 2018/8/29
 * Time: 14:20
 */
namespace app\api\model;
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

    /*public function getWorkingTimeAttr($value)
    {
        return date('H:i:s',$value);
    }

    public function getClosingTimeAttr($value)
    {
        return date('H:i:s',$value);
    }*/
    public function getWifiMacsAttr($value)
    {
        return json_decode($value,true);
    }
}