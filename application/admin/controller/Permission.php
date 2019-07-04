<?php
namespace app\admin\controller;
use gmars\rbac\Rbac;
use think\facade\Session;
use Request;
use Db;

class Permission extends Common
{
    public function list()
    {
        return $this->fetch();
    }
    public function addAction()
    {
        $rbac = new Rbac();
        $data = Request::post();
        $validate = new \app\admin\validate\Permission;
        // 1、使用验证器初步验证权限分类名称和描述是否符合要求
        if (!$validate->check($data)) {  
            $arr = ['code'=>'1','status'=>'error','data'=>$validate->getError()];
            echo json_encode($arr);
            die;
        }
        unset($data['__token__']);
        $getname=$rbac->getPermission([['name', '=', $data['name']]]);
        $getpath=$rbac->getPermission([['path', '=', $data['path']]]);
        if (empty($getname)&&empty($getpath)) {
            $rbac->createPermission([
                'name' => $data['name'],
                'category_id' => $data['category_id'],
                'path' => $data['path'],
                'description' => $data['description'],
                'status' => 1,
                'type' => 1,
            ]);
            $arr= ['code'=>'0','status'=>'ok','data'=>'添加成功'];
            echo json_encode($arr);
            die;
        }else{
            $arr = ['code'=>'2','status'=>'error','data'=>'分类名称或路径不能重复'];
            echo json_encode($arr);
            die;
        }
    }
    public function show()
    {
        $data = Db::query("select p.id,p.name,p.category_id,p.path,p.description,pc.name as pc_name from permission as p join permission_category as pc on p.category_id = pc.id order by p.id");
        echo json_encode($data);
    }
    public function updateAction()
    {
        $data = Request::post();
        $rbac = new Rbac();
        $validate = new \app\admin\validate\Permission;
        //1、使用验证器初步验证权限分类名称和描述是否符合要求
        if (!$validate->check($data)) {  
            $arr = ['code'=>'1','status'=>'error','data'=>$validate->getError()];
            echo $json = json_encode($arr);
            die;
        }
        unset($data['__token__']);
        $name=$data['name'];
        $path = $data['path'];
        $id = $data['id'];
        $arr = Db::query("select * from permission where name = '$name' or path = '$path'");
        if (empty($arr)) {
            Db::table('permission') ->update($data);
            $arr1 = ['code'=>'0','status'=>'ok','data'=>'修改成功'];
            $json =json_encode($arr1);
            echo $json;die;
        }else{
            foreach ($arr as $key => $value) {
                if ($value['id']!=$id) {
                    $arr = ['code'=>'1','status'=>'error','data'=>'name或path已存在'];
                    $json =json_encode($arr);
                    echo $json;die;
                }
            }
            Db::table('permission') ->update($data);
            $arr2 = ['code'=>'0','status'=>'ok','data'=>'修改成功'];
            $json =json_encode($arr2);
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
        $rbac->delPermission($data['id']);
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
            $rbac->delPermission($id);
            $arr=['code'=>'0','staus'=>'ok', 'data'=>'删除成功'];
            $json = json_encode($arr);
            echo $json;die;
        }
    }
}