<?php
namespace app\admin\controller;
use gmars\rbac\Rbac;
use think\facade\Session;
use Db;
use Request;

class Brand extends Common
{
    public function list()
    {
        return $this->fetch();
    }
    public function show()
    {
        $arr=Db::query("select * from brand");
        echo json_encode($arr);
        die;
    }
    public function add()
    {
        return $this->fetch();
    }
    public function addAction()
    {
    	$rbac = new Rbac();
    	$data=Request::post();
    	$brand_name=$data['brand_name'];
        $brand_logo = request()->file('brand_logo');
        $data1=['brand_name'=>$brand_name,'brand_logo'=>$brand_logo,'__token__'=>$data['__token__']];
    	$validate = new \app\admin\validate\Brand;
        // 1、使用验证器初步验证权限分类名称和描述是否符合要求
        if (!$validate->check($data1)) {  
            $arr = ['code'=>'1','status'=>'error','data'=>$validate->getError()];
            echo json_encode($arr);
            die;
        }
       	$res=Db::query("select * from brand where brand_name='$brand_name'");
       	if (empty($res)) {
       		if ($brand_logo) {
       			$info = $brand_logo->validate(['size'=>1024*1024,'ext'=>'jpg,png,gif'])->move( './uploads/brandlogo');
       			if ($info) {
       			 	$logo_path=str_replace("\\", "/", $info->getSaveName());
       			 	$arr = ['brand_name' =>$brand_name, 'brand_desc' =>$data['brand_desc'],'brand_logo'=>$logo_path];
       			 	Db::name('Brand')->insert($arr);
       			 	$arr1=['code'=>'0','status'=>'ok','data'=>'添加成功'];
       			 	echo json_encode($arr1);die;
       			}else{
       			 	$arr1=['code'=>'1','status'=>'error','data'=>'请选择正确的文件格式'];
       			 	echo json_encode($arr1);die;
       			}
       		}else{
       			$arr1=['code'=>'2','status'=>'error','data'=>'请选择文件'];
       			echo json_encode($arr1);die;
       		}
       	}else{
       		$arr1=['code'=>'3','status'=>'error','data'=>'品牌名称不能重复'];
       		echo json_encode($arr1);die;
       	}
    }
    public function imgupdate()
    {
    	$brand_logo = request()->file('brand_logo');
    	$data=Request::post();
    	$brand_id=$data['brand_id'];
    	$old_logo=$data['old_logo'];
    	$info = $brand_logo->validate(['size'=>1024*1024,'ext'=>'jpg,png,gif'])->move( './uploads/brandlogo');
    	if ($info) {
    	 	$logo_path=str_replace("\\", "/", $info->getSaveName());
    	 	$arr = ['brand_logo'=>$logo_path];
    	 	Db::name('brand')->where('brand_id',$brand_id)->update($arr);
    	 	unlink(".".$old_logo);
    	 	$arr1=['code'=>'0','status'=>'ok','data'=>'添加成功'];
    	 	echo json_encode($arr1);die;
    	}else{
    	 	$arr1=['code'=>'1','status'=>'error','data'=>'请选择正确的文件格式'];
    	 	echo json_encode($arr1);die;
    	}
    }
    public function updateAction()
    {
        $data=Request::post();
        $brand_name=$data['brand_name'];
        $res=Db::query("select * from brand where brand_name='$brand_name'");
        if (empty($res)||!empty($res)&&$res[0]['brand_id']==$data['brand_id']) {
        	Db::name('brand')->where('brand_id', $data['brand_id'])->update(['brand_name' => $brand_name,'brand_desc'=>$data['brand_desc']]);
            $arr = ['code'=>'0','status'=>'ok','data'=>'修改成功'];
        }else{
        	$arr = ['code'=>'1','status'=>'error','data'=>'name不能重复'];
        }
        echo json_encode($arr);die;
    }
    public function delete()
    {
        $data = Request::post();
        $brand_id=$data['brand_id'];
        $brand_logo=$data['brand_logo'];
        unlink("./uploads/brandlogo/".$brand_logo);
        Db::query("delete from brand where brand_id='$brand_id'");
        $arr=['code'=>'0','staus'=>'ok', 'data'=>'删除成功'];
        $json = json_encode($arr);
        echo $json;
    }
    public function datadel()
    {
        $data = Request::post();
        $brand_id=$data['brand_id'];
        if (empty($brand_id)) {
            $arr = ['code'=>'1','status'=>'error','data'=>'未选择任何对象'];
            $json = json_encode($arr);
            echo $json;
            die;
        }else{
            $brand_id=explode(',', $brand_id);
            array_shift($brand_id);
            $brand_id=implode(',', $brand_id);
            Db::table('brand')->where('brand_id','in',$brand_id)->delete();
            $arr=['code'=>'0','staus'=>'ok', 'data'=>'删除成功'];
            $json = json_encode($arr);
            echo $json;die;
        }
    }
}