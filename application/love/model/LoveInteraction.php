<?php

namespace app\love\model;

use think\Model;

class LoveInteraction extends Model
{
    // 设置数据表名
    protected $table = 'love_interaction';

    // 自动写入时间戳
    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'create_time';

    // 定义类型
    const TYPE_LIKE = 1;    // 点赞
    const TYPE_COMMENT = 2; // 评论
    const TYPE_SHARE = 3;   // 转发

    // 关联帖子
    public function post()
    {
        return $this->belongsTo('LovePost', 'post_id');
    }

    // 关联用户
    public function user()
    {
        return $this->belongsTo('app\love\model\User', 'user_id');
    }
}