<?php
namespace app\index\controller;
use think\Request;
use think\Controller;
use app\index\model\Userinfo as UserModel;
use think\Session;
use app\index\model\Role as RoleModel;
class Login extends Controller
{
    protected $sex_type = [1=>'男',2=>'女'];
    protected $account_status = [0=>'开启',1=>'关闭'];
    public function login()
    {
        if (Session::has('user_id')) {
            $this -> error('用户已经登陆,请勿重复登陆',url('index/index/index'),'',1);
        }
        return $this->view->fetch();
    }

    public function checkLogin(Request $request)
    {
        //返回参数
        $status = 0;
        $result = '';
        $data = $request->param();

        //创建验证规则
        $rule = [
            'name|用户名'=>'require',
            'password|密码'=>'require',
            'verify|验证码'=>'require|captcha',
        ];

        //自定义验证信息
        $msg = [
            'name'=>['require'=>'用户名不能为空,请检查'],
            'password'=>['require'=>'密码不能为空,请检查'],
            'verify'=>[
                'require'=>'验证码不能为空,请检查',
                'captcha'=>'验证码错误'
                ]
        ];

        //验证
        $result = $this->validate($data,$rule,$msg);

        //验证通过
        if($result === true){

            //构造查询条件
            $map = [
                'username'=>$data['name'],
                'password'=>md5($data['password']),
            ];

            //查询用户信息
           $user =  UserModel::get($map);

           if ($user == null){

               $result ="当前账户不存在";
           }else{

               if($user->is_disable){

                   $result ="账户处于关闭状态";

               }else{
                   $status = 1;
                   $result = '验证通过';


                   //侧边栏
                   $perm_url_info = $this->perm_url_info($user->user_id);

                   $menu = $this->get_menu($perm_url_info);
                   $perm_all = $this->get_perm($perm_url_info);

                   //获取用户的详细信息
                   $info = $this->get_user_info($user->user_id);

                   //设置session
                   Session::set('user_id',$user->user_id);
                   Session::set('user_info',$info);
                   Session::set('menu_user_id',$menu);
                   Session::set('perm_all_user_id',$perm_all);
               }

           }
        }


        return ['status'=>$status,'message'=>$result,'data'=>$data];
    }

    public function loginout()
    {
        //注销session
        Session::delete('user_id');
        Session::delete('user_info');
        $this->success('注销登陆正在返回','/index/login/login',"",1);
    }

    private function perm_url_info($user_id)
    {
        //Userinfo与Role数据表多对多关联
        $user_info = UserModel::get($user_id);
        $user_role = $user_info->getroles;

        //role与permission_menu数据表多对多关联
        foreach ($user_role as $value){
            $role_info = RoleModel::get($value['role_id']);
            $array_perm[] = $role_info->getperm;
        }

        foreach ($array_perm as $v){
            foreach ($v as $val){
                unset($val['pivot']);
                $new [] = $val;
            }
        }

        //处理重复信息
        $perm_url_info = array_unique($new);

        return $perm_url_info;
    }

    //获取当前用户的菜单栏信息
    private function get_menu($perm_url_info)
    {
        foreach ($perm_url_info as $k=>$item){
            if($item['is_menu'] == 1 && $item['parent_id'] == 0){
                $menu_arr[$item['code']] = [
                    'name'=>$item['name'],
                    'perm_id'=>$item['perm_id']
                ];
            }
        }

        foreach ($menu_arr as $key=>$item1){
            foreach ($perm_url_info as $key2=>$item2){
                if($item2['parent_id'] == $item1['perm_id']){
                    $children[$item2['code']] = [
                        'name'=>$item2['name'],
                        'page_url'=>$item2['page_url']
                    ];
                }
            }
            $menu_arr[$key]['children']= $children;
            unset($children);
        }

        return $menu_arr;
    }

    //获取所有的权限
    private function get_perm($perm_url_info)
    {
        $perm_url = array_column(json_decode(json_encode($perm_url_info),true),'page_url','code');
        foreach ($perm_url as $key_url=>$val_url){
            if (!$val_url){
                unset($perm_url[$key_url]);
            }
        }
        return $perm_url;
    }

    //组装用户信息
    private function get_user_info($user_id)
    {
        //查询用户信息
        $user =  UserModel::get($user_id);

        //获取用户的详细信息
        $user_role_info = $user->getroles;

        $user_extra_info = $user->getuserinfo->getData();

        $user_dept_info = $user->getuserdep->getData();

        $user_info = $user->getData();

        $user_new_info = array_merge($user_info,$user_extra_info);

        $user_new_info['roles'] = json_decode(json_encode($user_role_info),true);
        $user_new_info['dept'] = $user_dept_info;
        $user_new_info['sex'] = empty($user_new_info['sex']) ? '' : $this->sex_type[$user_new_info['sex']];
        $user_new_info['is_disable'] = $this->account_status[$user_new_info['is_disable']];
        unset($user_new_info['password']);
        unset($user_new_info['token']);
        unset($user_new_info['dept_id']);

        return $user_new_info;
    }
}