<?php
namespace app\admin\controller;
use gmars\rbac\Rbac;
use think\facade\Session;
use Db;
use Request;
class Goodsimg extends Common
{
    public function list()
    {
    	$goods_id=Request::get('goods_id');
    	$this->assign('goods_id',$goods_id);
        return $this->fetch();
    }
    public function addAction()
    {
    	$goods_id=Request::post('goods_id');
        $goods_img = request()->file('goods_img');
        if ($goods_img) {
        	foreach ($goods_img as $key => $value) {
        		$info = $value->validate(['size'=>1024*1024,'ext'=>'jpg,png,gif'])->move( './uploads/goodsimg');
        		if ($info) {
       			 	//$logo_path=str_replace("\\", "/", $info->getSaveName());
       			 	$name=$info->getFileName();
       			 	$date=date("Ymd");
       			 	$path="$date/$name";
       			 	$image = \think\Image::open("./uploads/goodsimg/$path");
       			 	$big_img="$date/big_$name";
       			 	$image->thumb(150,150)->save("./uploads/goodsimg/".$big_img);

       			 	$middle_img="$date/middle_$name";
       			 	$image->thumb(100,100)->save("./uploads/goodsimg/".$middle_img);
       			 	$small_img="$date/small_$name";
       			 	$image->thumb(50,50)->save("./uploads/goodsimg/".$small_img);
       			 	$data = ['goods_id' =>$goods_id, 'big_img' =>$big_img,'middle_img'=>$middle_img,'small_img'=>$small_img,'origin'=>$path];
       			 	Db::name('goods_img')->insert($data);
       			 }
        	}
        	$arr=['code'=>'0','status'=>'ok','data'=>'添加成功'];
        	echo json_encode($arr);die;
        }
    }
    public function show()
    {
    	$goods_id=Request::post('goods_id');
    	$arr=Db::query("select goods_img.img_id,goods_img.small_img,goods.goods_name,goods.goods_id from goods_img join goods on goods_img.goods_id=goods.goods_id where goods.goods_id='$goods_id'");
    	echo json_encode($arr);
    }
    public function delete()
    {
    	$data=Request::post();
    	$img_id=$data['img_id'];
    	$arr=Db::query("select * from goods_img where img_id='$img_id'");
    	if ($arr) {
    		foreach ($arr as $key => $value) {
    			unlink("./uploads/goodsimg/".$value['big_img']);
    			unlink("./uploads/goodsimg/".$value['middle_img']);
    			unlink("./uploads/goodsimg/".$value['small_img']);
    			unlink("./uploads/goodsimg/".$value['origin']);
    			Db::query("delete from goods_img where img_id='$img_id'");
    		}
    		$arr=['code'=>'0','staus'=>'ok', 'data'=>'删除成功'];
    		$json = json_encode($arr);
    		echo $json;
    	}
    }
}