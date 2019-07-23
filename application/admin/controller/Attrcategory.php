<?php
namespace app\admin\controller;
use gmars\rbac\Rbac;
use think\facade\Session;
use Db;
use Request;

class Attrcategory extends Common
{
    public function list()
    {
        return $this->fetch();
    }
    public function show()
    {
        $goods_id=Request::post('goods_id');
        $goods_attr_arr=Db::table('goods_attr')->where('goods_id',$goods_id)->find();
        $attr_cate_id=$goods_attr_arr['attr_cate_id'];
        $arr=Db::query("select * from attr_category");
        $json=['arr'=>$arr,'attr_cate_id'=>$attr_cate_id];
        echo json_encode($json);die;
    }
    public function addAction()
    {
        $data=Request::post();
        $data = ['attr_cate_name' =>$data['attr_cate_name']];
        Db::name('attr_category')->insert($data);
        $arr=['code'=>'0','status'=>'ok','data'=>'添加成功'];
        echo json_encode($arr);die;
    }
    public function updateAction()
    {
    	$data=Request::post();
    	$attr_cate_name=$data['attr_cate_name'];
    	$result=Db::query("select * from attr_category where attr_cate_name='$attr_cate_name'");
    	if (empty($result)) {
        	//修改数据库
        	Db::name('attr_category')->where('attr_cate_id',$data['attr_cate_id'])->update(['attr_cate_name'=>$attr_cate_name]);
        	$arr= ['code'=>'0','status'=>'ok','data'=>'修改成功'];
        	echo $json=json_encode($arr);
        	die; 
        }else{
        	if ($result[0]['attr_cate_id']!=$data['attr_cate_id']) {
        		$arr = ['code'=>'2','status'=>'error','data'=>'该属性分类已存在'];
        		echo $json=json_encode($arr);
        		die;
        	}else{
        		Db::name('attr_category')->where('attr_cate_id',$data['attr_cate_id'])->update(['attr_cate_name'=>$attr_cate_name]);
        		$arr= ['code'=>'0','status'=>'ok','data'=>'修改成功'];
        		echo $json=json_encode($arr);
        		die; 
        	}
        }
    }
    public function delete()
    {
    	$data=Request::post();
    	$attr_cate_id=$data['attr_cate_id'];
    	Db::query("delete from attr_category where attr_cate_id='$attr_cate_id'");
    	$arr= ['code'=>'0','status'=>'ok','data'=>'删除成功'];
    	echo $json=json_encode($arr);
    	die; 
    }
}