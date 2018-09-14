<?php
/**
 * Created by PhpStorm.
 * User: yds
 * Date: 2018/9/10
 * Time: 14:37
 * Des: 角色管理
 */
namespace app\index\controller;
use app\index\controller\Base;
use app\index\model\UserRole as UserRoleModel;
use app\index\model\Role as RoleModel;
use app\index\model\RolePermission as RolePermModel;
use think\Db;
use think\Request;
class Role extends Base
{
    public function index()
    {
        $role_info = Db::table('role')->paginate(2);
        $count = Db::table('role')->count();
        /*dump($role_info->render());
        die;*/
        $this->assign('title','角色列表');
        $this->assign('role',$role_info);
        $this->assign('count',$count);
        return $this->view->fetch();
    }

    public function store()
    {
        $perm_arr = $this->getAllPerm();

        $this->assign('perm_arr',$perm_arr);
        return $this->view->fetch('role_add');
    }

    public function create()
    {
        $info = request()->param();
        //判断是否勾选主页
        if (empty($info['shouye'])){

            return ['code'=>0,'msg'=>'首页选现必须勾选!'];

        }

        //角色表添加数据
        $role = new RoleModel();
        $role->name = $info['name'];
        $role->code = $info['code'];
        $role->remark = $info['remark'];
        $role->save();

        //返回新增角色id
        $role_id = $role->role_id;

        //角色权限表添加数据,销毁多余的数据
        unset($info['name']);
        unset($info['code']);
        unset($info['remark']);

        //处理数据
        foreach ($info as $value){
            $list[] = [
                'role_id'=>$role_id ,
                'perm_id'=>$value,
            ];
        }

        //角色权限表添加数据
        $role_perm = new RolePermModel();
        $res = $role_perm->saveAll($list);

        if ($res){
            return ['code'=>1,'msg'=>'添加成功!'];
        }else{
            return ['code'=>0,'msg'=>'添加失败,请刷新页面重试!'];
        }

    }

    public function edit()
    {
        $role_id = request()->param('role_id');


        $role = RoleModel::get($role_id);

        //获取角色对应的权限
        $role_perm = $role->getperm;

        //处理成['code'=>'perm_id']的数组,当前角色的权限信息
        $role_perm_info = array_column(json_decode(json_encode($role_perm),true),'perm_id','code');
        //当前角色信息
        $role_info = $role->getData();
        //权限列表
        $perm_arr = $this->getAllPerm();

        $this->assign('perm_arr',$perm_arr);
        $this->assign('role_info',$role_info);
        $this->assign('role_perm_info',$role_perm_info);

        return $this->view->fetch('role_edit');
    }

    public function update()
    {
        $info = request()->param();
        $role_id = $info['role_id'];

        if (empty($info['shouye'])){

            return ['code'=>0,'msg'=>'首页选现必须勾选!'];

        }

        //角色表添加数据
        $role = RoleModel::get($role_id);
        $role->name = $info['name'];
        $role->code = $info['code'];
        $role->remark = $info['remark'];
        $role->save();


        //角色权限表添加数据,销毁多余的数据
        unset($info['name']);
        unset($info['code']);
        unset($info['remark']);
        unset($info['role_id']);

        //处理数据
        foreach ($info as $value){
            $list[] = [
                'role_id'=>$role_id ,
                'perm_id'=>$value,
            ];
        }

        //角色权限表添加数据
        $role_perm = new RolePermModel();
        //删除已有的角色权限信息
        $role_perm->where('role_id','=',$role_id)->delete();
        //重新写入新的角色权限信息
        $res = $role_perm->saveAll($list);

        if ($res){
            return ['code'=>1,'msg'=>'修改成功!'];
        }else{
            return ['code'=>0,'msg'=>'修改失败,请刷新页面重试!'];
        }

    }

    public function delete()
    {
        $role_id = request()->param('role_id');

        $user_role = UserRoleModel::where('role_id','=',$role_id)->delete();
        $role = RoleModel::where('role_id','=',$role_id)->delete();
        $role_perm = RolePermModel::where('role_id','=',$role_id)->delete();

        if ($role){
            return ['code'=>1];
        }else{
            return ['code'=>0];
        }

    }


    private function getAllPerm()
    {
        $perm_info = Db::table('permission_menu')->select();

        foreach ($perm_info as $value){
            if ($value['parent_id'] == 0){
                $perm_arr[] = $value;
            }
        }

        foreach ($perm_arr as $key=>$item){
            foreach ($perm_info as $k=>$v){
                if ($item['perm_id'] == $v['parent_id']){
                    $perm_arr[$key]['children'][]= $v;
                }
            }
        }

        return $perm_arr;
    }

}