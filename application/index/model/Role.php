<?php
/**
 * Created by PhpStorm.
 * User: yds
 * Date: 2018/8/29
 * Time: 14:20
 */
namespace app\index\model;
use think\Model;
class Role extends Model
{
    protected $pk = 'role_id';

    public function getperm()
    {
        return $this->belongsToMany('PermissionMenu','role_permission','perm_id','role_id');
    }

    protected $updateTime = false;
    protected $createTime = false;
    /*protected $type = [
        'delete_time' => 'datetime',
    ];*/
    // 是否需要自动写入时间戳 如果设置为字符串 则表示时间字段的类型
    /*protected $autoWriteTimestamp = true; //自动写入
    // 创建时间字段
    protected $createTime = 'create_time';
    // 更新时间字段
    protected $updateTime = 'update_time';
    // 时间字段取出后的默认时间格式
    protected $dateFormat = 'Y-m-d H:i:s';*/
}