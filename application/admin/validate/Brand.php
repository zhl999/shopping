<?php

namespace app\admin\validate;

use think\Validate;

class Brand extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule =   [
        'brand_name'  => 'require|max:100|min:1|token',
        'brand_logo' => 'require|min:1',  
    ];

}