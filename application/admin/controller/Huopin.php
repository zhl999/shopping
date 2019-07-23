<?php
namespace app\admin\controller;
use gmars\rbac\Rbac;
use think\facade\Session;
use Db;
use Request;
class Huopin extends Common
{
    public function list()
    {	
    	$goods_id=Request::get('goods_id');
    	$this->assign('goods_id',$goods_id);
        return $this->fetch();
    }
    public function getGoodsattr()
    {
    	$goods_id=Request::get('goods_id');
    	$arr=Db::query("select a.attr_id,a.attr_name, a_d.attr_details_id,a_d.attr_detail_name from goods_attr as g_a join attr_details as a_d on g_a.attr_details_id=a_d.attr_details_id join attr as a on g_a.attr_id=a.attr_id where g_a.goods_id='$goods_id'");
    	$newarr = [];
        foreach ($arr as $key => $value) {
            $newarr[$value['attr_name']][]=$value;
        }
    	echo json_encode($newarr);
    }
    public function show()
    {
    	$goods_id=Request::post('goods_id');
    	$res=Db::query("select * from huoping where goods_id='$goods_id'");
    	for ($i=0; $i <count($res) ; $i++) { 
    		$new_attr=[];
    		$attr_details=$res[$i]['goods_attr_id'];
    		$attr_details_id=explode(",", $attr_details);
   			//var_dump($attr_details_id);die;
    		for ($j=0; $j <count($attr_details_id) ; $j++) { 
    			$details_id=$attr_details_id[$j];
    			$arr=Db::query("select * from attr_details where attr_details_id='$details_id'");
    			$new_attr[]=$arr[0]['attr_detail_name'];
    		}
    		$new_attr=implode('--',$new_attr );
    		$res[$i]['attr_detail_name']=$new_attr;
    	}
    	echo json_encode($res);
    }
    public function addAction()
    {
    	$data=Request::post();
    	// var_dump($data);die;
    	$attr_details_arr=$data['str'];
    	//var_dump($attr_details_arr);die;
    	$attr_details_arr = array_diff($attr_details_arr, ["0"]);
    	if (empty($attr_details_arr)) {
    		$json=['code'=>'1','status'=>'error','data'=>'请选择属性'];
    		echo json_encode($json);die;
    	}else{
    		$attr_details_arr=implode(',', $attr_details_arr);
    		//var_dump($attr_details_arr);
    		$arr=Db::table('huoping')->where('goods_id',$data['goods_id'])->find();
    		$attr_details_id=$arr['goods_attr_id'];
    		if ($attr_details_arr==$attr_details_id) {
    			$json=['code'=>'2','status'=>'error','data'=>'该货品已存在，请重新选择属性'];
    			echo json_encode($json);die;
    		}else{
	    		$data = ['goods_id' => $data['goods_id'], 'goods_attr_id' => $attr_details_arr,'price'=>$data['price'],'goods_number'=>$data['goods_number']];
				Db::name('huoping')->insert($data);
	    		$json=['code'=>'0','status'=>'ok','data'=>'添加成功'];
	    		echo json_encode($json);die;
    		}
    	}
    }
    public function updateAction()
    {
    	$data=Request::post();
    	$res=['price'=>$data['price'],'goods_number'=>$data['goods_number']];
    	Db::name('huoping') ->where('huoping_id', $data['huoping_id']) ->update($res);
    	$json=['code'=>'0','status'=>'ok','data'=>'修改成功'];
		echo json_encode($json);die;
    }
    public function delete()
    {
    	$data=Request::post();
    	Db::table('huoping')->where('huoping_id',$data['huoping_id'])->delete();
    	$json=['code'=>'0','status'=>'ok','data'=>'删除成功'];
		echo json_encode($json);die;
    }
}
