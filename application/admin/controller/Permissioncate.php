<?php
namespace app\admin\controller;
use gmars\rbac\Rbac;
use think\facade\Session;
use Request;
use Db;

class Permissioncate extends Common
{
    public function list()
    {
    	// $token = $this->request->token('__token__', 'sha1');
    	// Session::set('token',$token);
     //    $this->assign('token', $token);
        return $this->fetch();
    }
    public function addAction()
    {
    	$rbac = new Rbac();
        $result = Request::post();
        $validate = new \app\admin\validate\Permissioncate;
        // 1、使用验证器初步验证权限分类名称和描述是否符合要求
        if (!$validate->check($result)) {  
			$arr = ['code'=>'1','status'=>'error','data'=>$validate->getError()];
			echo $json = json_encode($arr);
			die;
        }
     	//  2、验证数据库是否存在相同的权限分类名称，若存在相同的名字则提示分类名称不能重复
        $data=$rbac->getPermissionCategory([['name', '=', $result['name']]]);
        if (empty($data)) {
        	$rbac->savePermissionCategory([
        	    'name' => $result['name'],
        	    'description' => $result['description'],
        	    'status' => 1
        	]);
        	$arr= ['code'=>'0','status'=>'ok','data'=>'添加成功'];
        	echo $json=json_encode($arr);
        	die; 
        }else{
        	$arr = ['code'=>'2','status'=>'error','data'=>'分类名称不能重复'];
        	echo $json=json_encode($arr);
        	die;
        }
    }
    public function show()
    {
    	$rbac = new Rbac();
    	$result = $rbac->getPermissionCategory([]);//当数组为空时，查所有
    	$json = json_encode($result);
    	echo $json;


    }
    public function updateAction()
    {
    	$rbac = new Rbac();
    	$data = Request::post();
    	$validate = new \app\admin\validate\Permissioncate;
        $result=$rbac->getPermissionCategory([['name', '=', $data['name']]]);
        if (!$validate->check($data)) {  
			$arr = ['code'=>'1','status'=>'error','data'=>$validate->getError()];
			echo $json = json_encode($arr);
			die;
        }
        if (empty($result)) {
        	//修改数据库
        	Db::name('permission_category')->where('id',$data['id'])->update(['name'=>$data['name'], 'description'=>$data['description']]);
        	$arr= ['code'=>'0','status'=>'ok','data'=>'修改成功'];
        	echo $json=json_encode($arr);
        	die; 
        }else{
        	if ($result[0]['id']!=$data['id']) {
        		$arr = ['code'=>'2','status'=>'error','data'=>'该分类已存在'];
        		echo $json=json_encode($arr);
        		die;
        	}else{
        		Db::name('permission_category')->where('id',$data['id'])->update(['name'=>$data['name'], 'description'=>$data['description']]);
        		$arr= ['code'=>'0','status'=>'ok','data'=>'修改成功'];
        		echo $json=json_encode($arr);
        		die; 
        	}
        }
    }

    public function del()
    {
    	// $token=Session::get('token');
    	// $deltoken = Request::post('__token__');
    	// if ($token!=$deltoken) {
    	// 	$arr = ['code'=>'1','status'=>'error','dada'=>'令牌数据无效'];
    	// 	$json = json_encode($arr);
    	// 	echo $json;
    	// 	die;
    	// }
    	$data = Request::post();
    	$rbac = new Rbac();
        $validate = new \app\admin\validate\Permissioncate_del_token;
        //1、使用验证器初步验证权限分类名称和描述是否符合要求
        if (!$validate->check($data)) {  
			$arr = ['code'=>'1','status'=>'error','data'=>$validate->getError()];
			echo $json = json_encode($arr);
			die;
        }
    	$rbac->delPermissionCategory($data['id']);
    	$arr=['code'=>'0','staus'=>'ok', 'data'=>'删除成功'];
    	$json = json_encode($arr);
    	echo $json;
    }
    public function datadel()
    {
    	$data= Request::post();
    	$id = $data['id'];
    	$rbac = new Rbac();
        $validate = new \app\admin\validate\Permissioncate_del_token;
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
    	}else{
    		$rbac = new Rbac();
    		$id=explode(',', $id);
    		array_shift($id);
    		$rbac->delPermissionCategory($id);
    		$arr=['code'=>'0','staus'=>'ok', 'data'=>'删除成功'];
    		$json = json_encode($arr);
    		echo $json;
    	}
    }
}