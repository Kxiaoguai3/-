<?php

namespace app\admin\validate;

use think\Validate;

class LoveCategory extends Validate
{
    protected $rule = [
        'id' => 'require|integer',
        'name'  => 'require|max:50',
        'status'=> 'require|in:0,1',
        'sort'  => 'number'
    ];

    protected $message = [
        'id.require'    => 'ID不能为空',     // 新增
        'id.integer'    => 'ID必须是整数',   // 新增
        'name.require' => '分类名称不能为空',
        'name.max'     => '分类名称不能超过50字',
        'status.require' => '必须选择状态',
        'status.in'    => '状态值非法',
        'sort.number'  => '排序值必须是数字'
    ];

    protected $scene = [
        'add'  => ['name', 'status', 'sort'],
        'edit' => ['id', 'name', 'status', 'sort']
    ];
}