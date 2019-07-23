<?php
namespace app\admin\controller;
use gmars\rbac\Rbac;
use think\facade\Session;
use Db;
use Request;

class Attr extends Common
{
    public function list()
    {
    	$attr_cate_id=Request::get('attr_cate_id');
    	$this->assign('attr_cate_id',$attr_cate_id);
        return $this->fetch();
    }
    public function show()
    {
    	$attr_cate_id=Request::post('attr_cate_id');
    	$arr=Db::query("select * from attr where attr.attr_cate_id='$attr_cate_id'");
    	echo json_encode($arr);
    }
    public function addAction()
    {
    	$data=Request::post();
    	$attr_name=$data['attr_name'];
        $attr_cate_id=$data['attr_cate_id'];
    	$arr=Db::query("select * from attr where attr_cate_id='$attr_cate_id' and attr_name='$attr_name'");
    	if (empty($arr)) {
			$res = ['attr_name' =>$attr_name,'attr_cate_id'=>$data['attr_cate_id']];
		    Db::name('attr')->insert($res);
		    $arr=['code'=>'0','status'=>'ok','data'=>'添加成功'];
		    echo json_encode($arr);die;
    	}else{
    		$arr=['code'=>'1','status'=>'error','data'=>'该属性已存在'];
		    echo json_encode($arr);die;
    	}
    	
    }
    public function updateAction()
    {
    	$data=Request::post();
        $attr_id=$data['attr_id'];
        $attr_cate_id=$data['attr_cate_id'];
        $attr_name=$data['attr_name'];
        $arr=Db::query("select * from attr where attr_cate_id='$attr_cate_id' and attr_name='$attr_name'");
        if (empty($arr)) {
            Db::query("update attr set `attr_name`='$attr_name' where attr_id='$attr_id'");
            $arr= ['code'=>'0','status'=>'ok','data'=>'修改成功'];
            echo $json=json_encode($arr);
            die;
        }else{
            if ($arr[0]['attr_id']!=$attr_id) {
               $arr = ['code'=>'2','status'=>'error','data'=>'该属性已存在'];
               echo $json=json_encode($arr);
               die;
            }else{
                Db::query("update attr set `attr_name`='$attr_name' where attr_id='$attr_id'");
                $arr= ['code'=>'0','status'=>'ok','data'=>'修改成功'];
                echo $json=json_encode($arr);
                die;
            }
        }

    	 
    }
    public function delete()
    {
        $data=Request::post();
        $attr_id=$data['attr_id'];
        $arr=Db::query("select * from attr_details where attr_id='$attr_id'");
        if (empty($arr)) {
            Db::query("delete from attr where attr_id='$attr_id'");
        }else{
            Db::query("delete from attr where attr_id='$attr_id'");
            foreach ($arr as $key => $value) {
                Db::query("delete from attr_details where attr_id='$attr_id'");
            }
        }
        $arr= ['code'=>'0','status'=>'ok','data'=>'删除成功'];
        echo $json=json_encode($arr);
        die; 
    }
}