<?php
/**
 * Created by PhpStorm.
 * User: yds
 * Date: 2018/9/11
 * Time: 11:40
 */
namespace app\index\controller;
use app\index\controller\Base;
use app\index\model\Userinfo as UserModel;
use app\index\model\UserExtra as UserExtraModel;
use app\index\model\Department as DepartModel;
use app\index\model\AttendanceConfig as AttenConModel;
use think\Db;
class Department extends Base
{

    public function index()
    {
        $depart_info = Db::table('department')
            ->field( ['dept_id'=>'id', 'parent_id'=>'pId', 'name'])
            ->where('status','1')
            ->order('sort','desc')
            ->select();
        foreach ($depart_info as $k=>$v){
            if ($v['pId'] == 0){
                $depart_info[$k]['open'] = true;
            }
        }

        $this->assign('depart_info',json_encode($depart_info));
        return $this->view->fetch();
    }

    public function store()
    {
        $dept_id = request()->param('parent_id');

        if ($dept_id){

            $dep_info = DepartModel::get($dept_id);

            $this->assign('dep_info',$dep_info);

        }
        $this->assign('parent_id',$dept_id);

        return $this->view->fetch('dept_create');
    }

    public function create()
    {
        $form_info = request()->param();

        $children_info = $form_info;
        //判断添加的是否为顶级部门
        if ($form_info['parent_id'] == 0){

            $children_info['parent_ids'] = $form_info['parent_id'];

        }else{
            $parent_info = DepartModel::get($form_info['parent_id']);

            //如果当前创建子部门的部门之前为子部门 将其标识为父级部门
            if ($parent_info->is_parent == 0){

                $parent_info->is_parent = 1;

                $parent_info->save();
            }
            $children_info['parent_ids'] = $parent_info->parent_ids.'-'.$form_info['parent_id'];
        }

        $children_info['is_parent'] = 0;

        $dep = new DepartModel($children_info);
        $res = $dep->allowField(true)->save();

        if ($res){
            return ['code'=>1,'msg'=>'添加成功!'];
        }else{
            return ['code'=>0,'msg'=>'提价失败!'];
        }
    }

    public function edit()
    {
        $dept_id = request()->param('dept_id');

        $dep_info = DepartModel::get($dept_id);

        $this->assign('dep_info',$dep_info);

        return $this->view->fetch('dept_edit');

    }

    public function update()
    {
        $dept_info = request()->param();

        if (!is_float($dept_info['sort']) && !is_int($dept_info['sort']) && !empty($dept_info['sort'])){
            return ['code'=>0,'msg'=>'排序字段需为整数或者小数'];
        }

        $dept = DepartModel::get($dept_info['dept_id']);

        $dept->name = $dept_info['name'];
        $dept->des = $dept_info['des'];
        $dept->sort = $dept_info['sort'];
        $res = $dept->save();

        if ($res){
            return ['code'=>1,'msg'=>'修改成功!'];
        }else{
            return ['code'=>0,'msg'=>'修改失败,请修改内容后提交!'];
        }


    }

    //渲染配置页面
    public function set()
    {
        $dept_id = request()->param('dept_id');

        $all_info = AttenConModel::all(['dept_id'=>$dept_id]);

        $this->assign('all_info',$all_info);
        $this->assign('dept_id',$dept_id);

        return $this->view->fetch('dept_config');

    }

    public function setting()
    {
        $form_info = request()->param();

        $attend_conf = new AttenConModel();

        if (empty($form_info['attend_conf_id'])){

            //销毁attend_conf_id
            unset($form_info['attend_conf_id']);

            //添加数据
            $attend_conf->data($form_info,true);
            $res = $attend_conf->save();

        }else{
            //更新
            $res = $attend_conf->allowField(true)->save($form_info,['attend_conf_id' => $form_info['attend_conf_id']]);

        }

        if ($res){
            return ['code'=>1,'msg'=>'提交成功!'];
        }else{
            return ['code'=>0,'msg'=>'提交失败!'];
        }

    }

    public function getconf()
    {
        $attend_conf_id = request()->param('attend_conf_id');

        $info = AttenConModel::get($attend_conf_id);
        if ($info){
            return ['code'=>1,'msg'=>$info];
        }else{
            return ['code'=>0,'msg'=>'获取当前配置失败!'];
        }

    }

    public function del()
    {
        $dept_id = request()->param('dept_id');

        //这里是删除操作

        //要考虑是否有人员 请假 删除为软删除 在以往的信息查询时可以查到之前的信息

        return ['code'=>1,'msg'=>$dept_id];
    }
}