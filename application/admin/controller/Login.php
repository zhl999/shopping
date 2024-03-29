<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\User;
use think\facade\Session;
use think\captcha\Captcha;
use gmars\rbac\Rbac;
use Request;
use Db;
class Login extends controller
{
    public function login()
    {
       return $this->fetch();
    }
    public function loginAction()
    {
		$name = Request::post('name');
		$password = Request::post('password');
		$password=md5($password);
		$code = Request::post('code');
		$captcha = new Captcha();
		if (!$captcha->check($code)) {
			$arr = ['code' => '1','status' => 'error','data' => '验证码错误'];
			echo $json=json_encode($arr);
			die;
		}else{
			$where = ['user_name' => $name,'password' => $password];
			$result = User::where($where)->find();
			if (empty($result)) {
				$arr = ['code' => '2','status' => 'error','data' => '账号或密码错误'];
				echo $json=json_encode($arr);
				die;
			}else{
				$rbac=new Rbac();
				$rbac->cachePermission($result['id']);
				$arr = ['code' => '0','status' => 'ok'];
				Session::set('name',$name);
				echo $json=json_encode($arr);
				die;
			}
		}
    }
    public function loginOut()
    {
       Session::clear('name');
       $this->redirect('login/login');
    }
}