<?php
namespace app\admin\controller;
use gmars\rbac\Rbac;
use Request;

class Permission extends Common
{
    public function list()
    {
    	$rbac = new Rbac();
    	$category=$rbac->getPermissionCategory([]);
    	// var_dump($data);
    	// die;
    	$this->assign('category',$category);
        return $this->fetch();
    }
    public function addAction()
    {
        $arr = Request::post();
    }
}