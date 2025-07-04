<?php

namespace app\admin\validate;

use think\Validate;

class LovePost extends Validate
{
    protected $rule = [
        'id' => 'require|number',
        'status' => 'require|in:1,2',
        'reason' => 'max:255'
    ];

    protected $message = [
        'id.require' => '帖子ID不能为空',
        'id.number' => '帖子ID必须为数字',
        'status.require' => '审核状态不能为空',
        'status.in' => '审核状态不合法',
        'reason.max' => '审核原因不能超过255个字符'
    ];
}