<?php

namespace app\love\validate;

use think\Validate;

class LoveInteraction extends Validate
{
    protected $rule = [
        'post_id'  => 'require|number',
        'user_id'  => 'require|number',
        'type'     => 'require|in:1,2,3',
        'content'  => 'max:255',
    ];

    protected $message = [
        'post_id.require' => '帖子ID不能为空',
        'user_id.require' => '用户ID不能为空',
        'type.require'    => '互动类型不能为空',
        'type.in'         => '互动类型不合法',
        'content.max'     => '评论内容不能超过255个字符',
    ];

    // 场景验证
    protected $scene = [
        'comment' => ['post_id', 'user_id', 'type', 'content', 'id'], // 评论需要内容
        'like'    => ['post_id', 'user_id', 'type'], // 点赞不需要内容
        'share'   => ['post_id', 'user_id', 'type'], // 转发不需要内容
    ];
}