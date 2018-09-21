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
use app\index\model\RolePermission as RolePermModel;
use app\index\model\PermissionMenu as PermModel;
use think\Session;
use think\Db;
use think\Request;
class Permission extends Base
{

    public function index()
    {
        $keywords = request()->param('keywords');

        $permission_menu = Db::table('permission_menu')
            ->where(function($query) use($keywords){
                $query->where('name','like', '%'.$keywords.'%');
            })
            ->paginate(10);
        $count = Db::table('permission_menu')
            ->where(function($query) use($keywords){
                $query->where('name','like', '%'.$keywords.'%');
            })
            ->count();

        $this->assign('title','权限列表');
        $this->assign('keywords',$keywords);
        $this->assign('permission_menu',$permission_menu);
        $this->assign('count',$count);
        $this->assign('active_menu','quanxianguanli.quanxianliebiao');

        return $this->view->fetch();
    }

    public function store()
    {
        $perm_menu_parent = Db::table('permission_menu')->where('parent_id','=','0')->select();

        //dump($perm_menu_parent);die;
        $this->assign('title','添加权限');
        $this->assign('perm_menu_parent',$perm_menu_parent);
        $this->assign('active_menu','quanxianguanli.tianjiaquanxian');
        return $this->view->fetch('perm_add');
    }

    public function create()
    {
        $info = request()->param();

        $perm = new PermModel();

        //检测当前权限节点是否已有
        $is_perm = $perm->where('name', $info['name'])->find();

        if ($is_perm){

            return ['code'=>1,'msg'=>'已有'.$info['name'].'权限节点,请勿重复添加!'];
        }

        //插入数据
        $perm->data($info);
        $res = $perm->save();

        if ($res){

            return ['code'=>1,'msg'=>'添加成功!'];
        }else{

            return ['code'=>0,'msg'=>'添加失败,请刷新页面重试!'];
        }
    }

    public function edit()
    {
        $perm_id = request()->param();

        $info = PermModel::get($perm_id);

        $perm_menu_parent = Db::table('permission_menu')->where('parent_id','=','0')->select();

        $this->assign('title','添加权限');
        $this->assign('perm_menu_parent',$perm_menu_parent);
        $this->assign('info',$info->getData());

        return $this->view->fetch('perm_edit');
    }

    public function update()
    {
        $perm_info = request()->param();

        $perm = new PermModel();

        $res = $perm->allowField(true)->save($perm_info,['perm_id' => $perm_info['perm_id']]);

        if ($res){

            return ['code'=>1,'msg'=>'修改成功!'];

        }else{

            return ['code'=>0,'msg'=>'没有修改任何内容!'];

        }
    }

    public function delete()
    {
        $perm_id = request()->param('perm_id');


        $perm = PermModel::where('perm_id','=',$perm_id)->delete();
        $role_perm = RolePermModel::where('perm_id','=',$perm_id)->delete();

        if ($perm_id){
            return ['code'=>1];
        }else{
            return ['code'=>0];
        }
    }
}