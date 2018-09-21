<?php
namespace app\index\controller;
use app\index\controller\Base;
use app\index\model\Userinfo as UserModel;
use app\index\model\UserExtra as UserExtraModel;
use app\index\model\Role as RoleModel;
use app\index\model\Department as DepartModel;
use app\index\model\AttendanceConfig as AttenConfModel;
use app\index\model\UserRole as UserRoleModel;
use think\Session;
use think\Db;
use think\Request;
class User extends Base
{
    protected $sex_type = ['1' => '男', '2' => '女'];

    protected $is_disable_type = ['0' => '开启', '1' => '关闭'];

    public function index()
    {
        $keywords = request()->param('keywords');
        $is_disable = request()->param('is_disable');
        //分页数据
        $list = Db::table('userinfo')
            ->alias('a')
            ->join('user_extra b', 'a.user_id = b.user_id')
            ->where(function($query) use($is_disable){
                if (is_numeric($is_disable)){
                    $query->where('a.is_disable',$is_disable);
                }
            })
            ->where(function($query) use($keywords){
                $query->where('a.username','like', '%'.$keywords.'%');
            })
            ->paginate(10);
        //总条数
        $count = Db::table('userinfo')
            ->alias('a')
            ->join('user_extra b', 'a.user_id = b.user_id')
            ->where(function($query) use($is_disable){
                if (is_numeric($is_disable)){
                    $query->where('a.is_disable',$is_disable);
                }
            })
            ->where(function($query) use($keywords){
                $query->where('a.username','like', '%'.$keywords.'%');
            })
            ->count();

        //往模版中传入值
        $this->assign('list', $list);
        $this->assign('count', $count);
        $this->assign('sex_type', $this->sex_type);
        $this->assign('is_disable_type', $this->is_disable_type);
        $this->assign('dept_info', $this->getdep());
        $this->assign('title', '用户列表');
        $this->assign('keywords',$keywords);
        $this->assign('is_disable',$is_disable);
        $this->assign('active_menu','yonghuguanli.yonghuliebiao');


        return $this->view->fetch();
    }

    //渲染添加用户模版
    public function store()
    {
        $role_info = RoleModel::all();

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
        $this->assign('role_info', $role_info);
        $this->assign('title', '添加用户');
        $this->assign('active_menu','yonghuguanli.tianjiayonghu');

        return $this->view->fetch('user_create');
    }

    public function getAttend()
    {
        $dept_id = request()->param('dept_id');

        $conf = AttenConfModel::all(function($query) use($dept_id){
            $query->where('dept_id', $dept_id);
        });
        if ($conf){
            return ['code'=>1,'msg'=>$conf];
        }else{
            return ['code'=>0,'msg'=>'当前部门下没有配置上下班时间'];
        }

    }

    //添加用户数据处理
    public function create()
    {
        $user_info = request()->param();

        $userinfo = new UserModel();
        $userinfo->username = $user_info['username'];
        $userinfo->password = md5($user_info['password']);
        $userinfo->dept_id = $user_info['dept_id'];
        $userinfo->attend_conf_id = $user_info['attend_conf_id'];
        $userinfo->save();
        $user_new_id = $userinfo->user_id;

        foreach ($user_info['role_id'] as $v){
            $user_role_info[] = ['user_id'=>$user_new_id,'role_id'=>$v];
        }

        $user_role = new UserRoleModel();
        $user_role->saveAll($user_role_info);

        $user_extra = new UserExtraModel();
        $user_extra->user_id = $user_new_id;
        $user_extra->sex = $user_info['sex'];
        $user_extra_res = $user_extra->save();

        if ($user_extra_res){
            return ['code'=>1,'msg'=>"添加成功"];
        }else{
            return ['code'=>0,'msg'=>'添加失败'];
        }

    }

    private function getdep()
    {
        $dep_info = DepartModel::all();

        $dep_arr = array_column(json_decode(json_encode($dep_info),true),'name','dept_id');

        return $dep_arr;
    }

    public function getuserinfo()
    {
        $uid = request()->param('user_id');

        $user = UserModel::get($uid);

        $user_info = UserExtraModel::get($uid);

        $dept = DepartModel::get($user->dept_id);

        $this->assign('user',$user);
        $this->assign('user_info',$user_info);
        $this->assign('dept',$dept);
        $this->assign('sex_type', $this->sex_type);


        return $this->view->fetch('user_info');
    }

    public function setstatus()
    {
        $uid = request()->param('user_id');
        $is_disable = request()->param('is_disable');

        $is_disable = $is_disable==1 ? 0 : 1 ;

        $user = UserModel::get($uid);
        $user->is_disable = $is_disable;
        $res = $user->save();

        if ($res){
            return ['code'=>1,'msg'=>"修改成功"];
        }else{
            return ['code'=>0,'msg'=>'修改失败'];
        }
    }

    public function edit()
    {

    }

}
