<?php
namespace app\admin\controller;
use gmars\rbac\Rbac;
use think\facade\Session;
use Db;
use Request;

class Goodsattr extends Common
{
    public function list()
    {
    	$goods_id=Request::get('goods_id');
    	$this->assign('goods_id',$goods_id);
        return $this->fetch();
    }
    public function show()
    {
    	$attr_cate_id=Request::post('attr_cate_id');
        $goods_id=Request::post('goods_id');
        $goods_attr_arr=Db::table('goods_attr')->where('goods_id',$goods_id)->where('attr_cate_id',$attr_cate_id)->select();

    	$attr_arr=Db::query("select * from attr where attr_cate_id='$attr_cate_id'");
        $attr_id_arr=[];
        $aa=[];
        foreach ($attr_arr as $key => $value) {
            $attr_id=$value['attr_id'];
            $res=Db::query("select attr.attr_id,attr.attr_name,attr_details.attr_details_id,attr_details.attr_detail_name from attr join attr_details on attr.attr_id=attr_details.attr_id where attr_details.attr_id='$attr_id'");
            $aa[]=$res; 
        }        
		$arr=[];
        foreach ($aa as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $arr[$value1['attr_name']][]=$value1;
            }
        }
        $json=['goods_attr_arr'=>$goods_attr_arr,'arr'=>$arr];
    	echo json_encode($json);
    }
    public function addAction()
    {
        $goods_id=input("post.goods_id");//商品id
        $attr_details_id=input("post.attr_details_id");//选中的属性值的id
        $attr_details_id=substr($attr_details_id,1);
        $attr_details_arr=explode(',', $attr_details_id);//属性值id组成数组循环添加
        $attr_cate_id=input("post.attr_cate_id");//属性分类的id
        //根据商品的id查该商品有没有属性存在属性分类，如果为空，就直接新增，如果存在，就删掉原来的属性，重新新增
        $goods_attr_arr=Db::query("select * from goods_attr where goods_id='$goods_id'");
        if (empty($goods_attr_arr)) {
            foreach ($attr_details_arr as $key => $value) {
                $arr=Db::table('attr_details')->where('attr_details_id',$value)->find();
                $attr_id=$arr['attr_id'];
                $data=['goods_id'=>$goods_id,'attr_details_id'=>$value,'attr_id'=>$attr_id,'attr_cate_id'=>$attr_cate_id];
                Db::table('goods_attr')->insert($data);
            }
            $json=['code'=>'0','status'=>'ok','data'=>'添加成功'];
            echo json_encode($json);die;
        }else{
            Db::query("delete from goods_attr where `goods_id`='$goods_id'");
            foreach ($attr_details_arr as $key => $value) {
                $arr=Db::table('attr_details')->where('attr_details_id',$value)->find();
                $attr_id=$arr['attr_id'];
                $data=['goods_id'=>$goods_id,'attr_details_id'=>$value,'attr_id'=>$attr_id,'attr_cate_id'=>$attr_cate_id];
                Db::table('goods_attr')->insert($data);
            }
            $json=['code'=>'0','status'=>'ok','data'=>'添加成功'];
            echo json_encode($json);die;
        }
    }
}