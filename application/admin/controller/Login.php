<?php
namespace app\admin\controller;
use think\Controller;
// use app\admin\model\User;
use think\captcha\Captcha;
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
			$where = ['name' => $name,'password' => $password];
			//$result = User::where($where)->find();
			$result = Db::table('user')->where($where)->find();
			if (empty($result)) {
				$arr = ['code' => '2','status' => 'error','data' => '账号或密码错误'];
				echo $json=json_encode($arr);
				die;
			}else{
				$arr = ['code' => '0','status' => 'ok'];
				echo $json=json_encode($arr);
				die;
			}
		}
    }
}