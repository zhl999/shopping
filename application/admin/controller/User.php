<?php
namespace app\admin\controller;
use gmars\rbac\Rbac;
use think\facade\Session;
use Request;
use Db;
class User extends Common
{
	public function list()
    {
        return $this->fetch();
    }
    public function show()
    {
        $arr=Db::query("select u.id,u.user_name,u.password,u.mobile,u.create_time,u_r.role_id,role.name from user as u join user_role as u_r on u.id=u_r.user_id join role on u_r.role_id=role.id");
        $json=json_encode($arr);
        echo $json;
    }
    public function addAction()
    {
        $rbac = new Rbac();
        $data=Request::post();
         $validate = new \app\admin\validate\User;
        // 1、使用验证器初步验证权限分类名称和描述是否符合要求
        if (!$validate->check($data)) {  
            $arr = ['code'=>'1','status'=>'error','data'=>$validate->getError()];
            echo json_encode($arr);
            die;
        }
        $user_name=$data['user_name'];
        $getname=Db::query("select user_name from user where user_name='$user_name'");
        if (empty($getname)) {
	        $create_time=date('Y-m-d h:i:s', time());
	        $role_id=$data['role_id'];
	        $user_arr= ['user_name' =>$user_name, 'password' =>$data['password'],'mobile'=>$data['mobile'],'create_time'=>$create_time];
			Db::name('user')->insert($user_arr);
			$user_id=Db::query("select user.id from user where user_name='$user_name'");
			$user_id=$user_id[0]['id'];
			$rbac->assignUserRole($user_id, [$role_id]);
			$arr=['code'=>'0','status'=>'ok','data'=>'添加成功'];
        }else{
        	$arr=['code'=>'2','status'=>'error','data'=>'用户名称不能重复'];
        }
        echo json_encode($arr);
    }
    public function updateAction()
    {
        $data = Request::post();
        $rbac = new Rbac();
        $validate = new \app\admin\validate\User;
        //1、使用验证器初步验证权限分类名称和描述是否符合要求
        if (!$validate->check($data)) {  
            $arr = ['code'=>'1','status'=>'error','data'=>$validate->getError()];
            echo $json = json_encode($arr);
            die;
        }
        unset($data['__token__']);
        $user_name=$data['user_name'];
        $mobile=$data['mobile'];
        $getarr=Db::query("select * from user where user_name='$user_name'or mobile=$mobile");
        if (empty($getarr)) {
        	$user_arr=['user_name'=>$user_name,'password'=>$data['password'],'mobile'=>$data['mobile']];
        	Db::name('user')->where('id', $data['id'])->update($user_arr);
        	$u_r_arr=['role_id'=>$data['role_id']];
        	Db::name('user_role')->where('user_id',$data['id'])->update($u_r_arr);
            $arr1 = ['code'=>'0','status'=>'ok','data'=>'修改成功'];
            $json =json_encode($arr1);
            echo $json;die;
        }else{
        	foreach ($getarr as $key => $value) {
                if ($value['id']!=$data['id']) {
                    $arr = ['code'=>'1','status'=>'error','data'=>'name或phone已存在'];
                    $json =json_encode($arr);
                    echo $json;die;
                }
            }
            $user_arr=['user_name'=>$user_name,'password'=>$data['password'],'mobile'=>$data['mobile']];
        	Db::name('user')->where('id', $data['id'])->update($user_arr);
        	$u_r_arr=['role_id'=>$data['role_id']];
        	Db::name('user_role')->where('user_id',$data['id'])->update($u_r_arr);
            $arr1 = ['code'=>'0','status'=>'ok','data'=>'修改成功'];
            $json =json_encode($arr1);
            echo $json;die;
        }
    }
    public function delete()
    {
        $data = Request::post();
        $rbac = new Rbac();
        $validate = new \app\admin\validate\Deletetoken;
        //1、使用验证器初步验证权限分类名称和描述是否符合要求
        if (!$validate->check($data)) {  
            $arr = ['code'=>'1','status'=>'error','data'=>$validate->getError()];
            echo $json = json_encode($arr);
            die;
        }
        Db::table('user')->where('id',$data['id'])->delete();
        Db::table('user_role')->where('user_id',$data['id'])->delete();
        $arr=['code'=>'0','staus'=>'ok', 'data'=>'删除成功'];
        $json = json_encode($arr);
        echo $json;
    }
    public function datadel()
    {
    	$data= Request::post();
        $id = $data['id'];
        $rbac = new Rbac();
        $validate = new \app\admin\validate\Deletetoken;
        //1、使用验证器初步验证权限分类名称和描述是否符合要求
        if (!$validate->check($data)) {  
            $arr = ['code'=>'1','status'=>'error','data'=>$validate->getError()];
            echo $json = json_encode($arr);
            die;
        }
        if (empty($id)) {
            $arr = ['code'=>'1','status'=>'error','data'=>'未选择任何对象'];
            $json = json_encode($arr);
            echo $json;
            die;
        }else{
            $rbac = new Rbac();
            $id=explode(',', $id);
            array_shift($id);
            $id=implode(',', $id);
            Db::table('user')->where('id','in',$id)->delete();
            Db::table('user_role')->where('user_id','in',$id)->delete();
            $arr=['code'=>'0','staus'=>'ok', 'data'=>'删除成功'];
            $json = json_encode($arr);
            echo $json;
        }
    }
}