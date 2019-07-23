<?php
namespace app\admin\controller;
use think\Controller;
use think\facade\Session;
use gmars\rbac\Rbac;
use Request;
class Common extends Controller
{
    public function __construct()
    {
    	parent::__construct();
        $name=Session::get('name');
        if (empty($name)) {
    		$this->redirect('login/login');
        }else{
        	$this->assign('name',$name);
        }
        $model=Request::module();
        $class=Request::controller();
        $action=Request::action();
        $arr_class=['Permission','Permissioncate','Role','User'];
        $arr_action=['show','delete','addaction','updateaction'];
        if (in_array($class, $arr_class)) {
            if (in_array($action, $arr_action)) {
                $str="$model/$class/$action";
                $str=strtolower($str);
                $rbac=new Rbac();
                $bool=$rbac->can($str);
                if (!$bool) {
                    header("content-Type:application/json");
                    $arr=['code'=>'10001','status'=>'error','data'=>'您没有权限'];
                    echo json_encode($arr);
                    die;
                }
            }
        }
    }
    public function commonToken()
    {
        $token = $this->request->token('__token__', 'sha1');
        $arr=['code'=>'0','data'=>$token];
        $json=json_encode($arr);
        echo $json;
    }
}
