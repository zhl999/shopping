<?php
namespace app\admin\controller;
use gmars\rbac\Rbac;
use think\facade\Session;
use Db;
use Request;

class Attrdetail extends Common
{
    public function list()
    {
        $attr_id=Request::get('attr_id');
    	$this->assign('attr_id',$attr_id);
        return $this->fetch();
    }
    public function show()
    {
    	$attr_id=Request::post('attr_id');
    	$arr=Db::query("select * from attr_details where attr_id='$attr_id'");
    	echo json_encode($arr);
    }
    public function addAction()
    {
    	$data=Request::post();
    	$attr_detail_name=$data['attr_detail_name'];
        $attr_id=$data['attr_id'];
    	$arr=Db::query("select * from attr_details where `attr_id`='$attr_id' and `attr_detail_name`='$attr_detail_name'");
    	if (empty($arr)) {
			$res = ['attr_detail_name' =>$attr_detail_name,'attr_id'=>$data['attr_id']];
		    Db::name('attr_details')->insert($res);
		    $arr=['code'=>'0','status'=>'ok','data'=>'添加成功'];
		    echo json_encode($arr);die;
    	}else{
    		$arr=['code'=>'1','status'=>'error','data'=>'该属性值已存在'];
		    echo json_encode($arr);die;
    	}
    }
    public function updateAction()
    {
        $data=Request::post();
        $attr_id=$data['attr_id'];
        $attr_detail_id=$data['attr_detail_id'];
        $attr_detail_name=$data['attr_detail_name'];
        $arr=Db::query("select * from attr_details where `attr_id`='$attr_id' and `attr_detail_name`='$attr_detail_name'");
        if (empty($arr)) {
            Db::query("update attr_details set `attr_detail_name`='$attr_detail_name' where `attr_details_id`='$attr_detail_id'");
            $arr= ['code'=>'0','status'=>'ok','data'=>'修改成功'];
            echo $json=json_encode($arr);
            die;
        }else{
            if ($arr[0]['attr_details_id']!=$attr_detail_id) {
                $arr = ['code'=>'2','status'=>'error','data'=>'该属性已存在'];
                echo $json=json_encode($arr);
                die;
            }else{
                Db::query("update attr_details set `attr_detail_name`='$attr_detail_name' where `attr_details_id`='$attr_detail_id'");
                $arr= ['code'=>'0','status'=>'ok','data'=>'修改成功'];
                echo $json=json_encode($arr);
                die;
            }
        }
    }
    public function delete()
    {
    	$data=Request::post();
    	$attr_details_id=$data['attr_details_id'];
    	Db::query("delete from attr_details where attr_details_id='$attr_details_id'");
    	$arr= ['code'=>'0','status'=>'ok','data'=>'删除成功'];
    	echo $json=json_encode($arr);
    	die; 
    }
}