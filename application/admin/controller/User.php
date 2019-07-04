<?php
namespace app\admin\controller;
use gmars\rbac\Rbac;
use think\facade\Session;
use Request;
use Db;
class User extends Common
{
	public function list()
    {
        return $this->fetch();
    }
    
}