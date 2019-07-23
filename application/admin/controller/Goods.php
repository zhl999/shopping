<?php
namespace app\admin\controller;
use gmars\rbac\Rbac;
use think\facade\Session;
use Db;
use Request;
class Goods extends Common
{
    public function list()
    {
        return $this->fetch();
    }
    public function category()
    {
    	$cate_arr=Db::query("select * from category");
    	$brand_arr=Db::query("select * from brand");
    	$arr=['cate_arr'=>$cate_arr,'brand_arr'=>$brand_arr];
    	echo json_encode($arr);die;
    }
    public function show()
    {
        $arr=Db::query("select g.goods_id,g.goods_name,goods_desc,c.cat_id,c.cat_name,b.brand_id,b.brand_name from goods as g join category as c on g.cat_id=c.cat_id join brand as b on g.brand_id=b.brand_id order by g.goods_id");
        echo json_encode($arr);
    }
    public function addAction()
    {
    	$data=Request::post();
        $data = ['goods_name' =>$data['goods_name'],'cat_id' =>$data['category'],'brand_id'=>$data['brand'],'goods_desc'=>$data['goods_desc']];
        Db::name('goods')->insert($data);
        $arr=['code'=>'0','status'=>'ok','data'=>'添加成功'];
        echo json_encode($arr);die;
    }
    public function delete()
    {
        $data=Request::post();
        $goods_id=$data['goods_id'];
        $arr=Db::query("select * from goods_img where goods_id='$goods_id'");
        Db::query("delete from goods where goods_id='$goods_id'");
        if ($arr) {
            foreach ($arr as $key => $value) {
                unlink("./uploads/goodsimg/".$value['big_img']);
                unlink("./uploads/goodsimg/".$value['middle_img']);
                unlink("./uploads/goodsimg/".$value['small_img']);
                unlink("./uploads/goodsimg/".$value['origin']);
                Db::query("delete from goods_img where goods_id='$goods_id'");
            }
            $arr=['code'=>'0','status'=>'ok', 'data'=>'删除成功'];
            $json = json_encode($arr);
            echo $json;
        }

    }
}