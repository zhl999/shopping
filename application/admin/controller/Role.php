<?php
namespace app\admin\controller;
use gmars\rbac\Rbac;
use think\facade\Session;
use Request;
use Db;
class Role extends Common
{
    public function list()
    {
        return $this->fetch();
    }
    public function show()
    {
        $arr=Db::query("select * from role");
        $json=json_encode($arr);
        echo $json;
    }
    public function add()
    {
        return $this->fetch();
    }
    public function addAction()
    {
        $rbac = new Rbac();
        $data=Request::post();
        $validate = new \app\admin\validate\Role;
        // 1、使用验证器初步验证权限分类名称和描述是否符合要求
        if (!$validate->check($data)) {  
            $arr = ['code'=>'1','status'=>'error','data'=>$validate->getError()];
            echo json_encode($arr);
            die;
        }
        unset($data['__token__']);
        $getname=$rbac->getRole([['name', '=', $data['name']]]);
        if (empty($getname)) {
            $permission_id=$data['permission_id'];
            $array=explode(',', $permission_id);
            array_shift($array);
            $permission_id=implode(',', $array);
            $rbac->createRole([
                'name' => $data['name'],
                'description' => $data['description'],
                'status' => 1
            ], $permission_id);
            $arr= ['code'=>'0','status'=>'ok','data'=>'添加成功'];
            echo json_encode($arr);
            die;
        }else{
            $arr = ['code'=>'2','status'=>'error','data'=>'角色名称不能重复'];
            echo json_encode($arr);
            die;
        }
        

    }
    public function permissionShow()
    {
        $arr= Db::query("select p.id,p.name,p.category_id,p.path,p.description,pc.name as pc_name from permission as p join permission_category as pc on p.category_id = pc.id order by p.id");
        // var_dump($arr);
        // die;
        $newarr = [];
        foreach ($arr as $key => $value) {
            $newarr[$value['pc_name']][]=$value;
        }
        // var_dump($newarr);
        // die;
        //$arr=['data'=>$newarr];
        $json=json_encode($newarr);
        echo $json;
    }
    public function myPermissionShow()
    {
        $id = Request::post('id');
        $r_p_arr=Db::query("select permission_id from role_permission where role_id=$id");
        $p_arr= Db::query("select p.id,p.name,p.category_id,p.path,p.description,pc.name as pc_name from permission as p join permission_category as pc on p.category_id = pc.id order by p.id");
        $newarr = [];
        foreach ($p_arr as $key => $value) {
            $newarr[$value['pc_name']][]=$value;
        }
        $arr=['r_p_arr'=>$r_p_arr,'p_arr'=>$newarr];
        echo json_encode($arr);
    }
    public function updateAction()
    {
        $rbac = new Rbac();
        $validate = new \app\admin\validate\Role;
        $data=Request::post();
        if (!$validate->check($data)) {  
            $arr = ['code'=>'1','status'=>'error','data'=>$validate->getError()];
            echo $json = json_encode($arr);
            die;
        }
        unset($data['__token__']);
        $id=$data['id'];
        $getname=$rbac->getRole([['name', '=', $data['name']]]);//查数据库有没有相同的名字
        if (empty($getname)||!empty($getname)&&$getname[0]['id']==$id) {
            //如果没有相同的名字，并且修改的是自己的就可以先把role表先入库
            $qq=Db::name('role')->where('id', $id)->update(['name' => $data['name'],'description'=>$data['description']]);
            $arr = ['code'=>'0','status'=>'ok','data'=>'修改成功'];
            //不管角色对应的权限与没有修改，都得先把角色对应的权限删除，然后新增角色对应的权限
            Db::query("delete from role_permission where role_id='$id'");
            $permission_id=$data['permission_id'];
            $p_id=explode(',', $permission_id);
            array_shift($p_id);
            foreach ($p_id as $key => $value) {
                $v=['role_id'=>$id,'permission_id'=>$value];
                Db::name('role_permission')->insert($v);
            }
        }else{
            $arr = ['code'=>'1','status'=>'error','data'=>'name已存在'];
        }
        $json =json_encode($arr);
        echo $json;
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
        $rbac->delRole($data['id']);
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
             $rbac->delRole($id);
            $arr=['code'=>'0','staus'=>'ok', 'data'=>'删除成功'];
            $json = json_encode($arr);
            echo $json;die;
        }
    }
}