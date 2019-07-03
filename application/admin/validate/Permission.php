<?php

namespace app\admin\validate;

use think\Validate;

class Permission extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule =   [
        'name'  => 'require|max:50|min:1|token',
        'category_id'  => 'require|min:1',
        'path'  => 'require|max:100|min:1',
        'description'   => 'require|max:200|min:1',    
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
}