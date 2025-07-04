<?php

namespace app\love\validate;

use think\Validate;

class LovePost extends Validate
{
    protected $rule = [
        'user_id'       => 'require|number|checkUserExists',
        'text'          => 'require|min:5|max:500',
        'type'          => 'require|in:1,2,3',
        'is_anonymous'  => 'boolean',
    ];

    protected $message = [
        'user_id.require'       => '用户ID不能为空',
        'user_id.number'        => '用户ID必须是数字',
        'text.require'          => '表白内容不能为空',
        'text.min'              => '表白内容至少5个字',
        'text.max'              => '表白内容最多500字',
        'type.require'          => '表白类型不能为空',
        'type.in'               => '表白类型不合法',
        'is_anonymous.boolean'  => '匿名参数不合法',
    ];

    // 自定义验证规则 - 检查用户是否存在
    protected function checkUserExists($value)
    {
        $user = \app\love\model\User::find($value);
        return $user ? true : '用户不存在';
    }
}