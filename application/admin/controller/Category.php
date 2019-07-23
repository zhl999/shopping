<?php
namespace app\admin\controller;
use think\Controller;
use Db;
use Request;

class Category extends Common
{
    public function list()
    {	
        return $this->fetch();
    }
    private function getTree($array, $parent_id =0, $level = 0)
    {
        static $list = [];
        echo "<ul class='tree_ul'>";
        foreach ($array as $key => $value){
            if ($value['parent_id'] == $parent_id){

                $value['cat_name']=$value['cat_name'];

                $m_id=$value['cat_id'];
                echo "<li class='tree_li' value='".$m_id."' id='id".$m_id."'>".$value['cat_name']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a onclick=modaldemo(\"".$m_id."\",\"".$value['cat_name']."\") class='Hui-iconfont'>&#xe6df;</a>&nbsp;&nbsp;<a onclick=del('".$m_id."') class='Hui-iconfont'>&#xe6a6;</a></li>";

                $list[]=$value;

                $this->getTree($array, $value['cat_id'], $level+1);
            }
        }
        echo "</ul>";
        return $list;
    }
    public function show()
    {
        $arr=Db::query("select * from category");
        $this->getTree($arr);
        //echo json_encode($arr);
    }
    public function addAction()
    {
        $data=Request::post();
        $arr=['cat_name'=>$data['cat_name'],'parent_id'=>$data['parent_id']];
        $id=Db::name('category')->insertGetId($arr);
        $arr1=['code'=>'0','status'=>'ok','data'=>$id];
        echo json_encode($arr1);die;
    }
    public function updateAction()
    {
        $data=Request::post();
        $cat_name=$data['cat_name'];
        $res=Db::query("select * from category where cat_name='$cat_name'");
        if (empty($res)||!empty($res)&&$res[0]['cat_id']==$data['cat_id']) {
            Db::name('category')->where('cat_id', $data['cat_id'])->update(['cat_name' => $cat_name]);
            $arr = ['code'=>'0','status'=>'ok','data'=>'修改成功'];
        }else{
            $arr = ['code'=>'1','status'=>'error','data'=>'name不能重复'];
        }
        echo json_encode($arr);die;
    }
    function del()
    {
        $data=Request::post();
        $cat_id=$data['id'];
        $arr=Db::query("delete from category where cat_id='$cat_id'");
        $this->delete($cat_id);   
    }
    function delete($cat_id)
    {   
        $arr=Db::query("select * from category where parent_id='$cat_id'");
        if (empty($arr)) {
            Db::query("delete from category where cat_id='$cat_id'");
            $arr1=['code'=>'1','status'=>'ok','data'=>'删除成功'];
            // echo json_encode($arr1);die;
        }else{
            foreach ($arr as $key => $value) {
                $parent_id=$value['cat_id'];
                Db::query("delete from category where cat_id='$parent_id'");
                $this->delete($value['cat_id']);
            }
        }
    }
}	