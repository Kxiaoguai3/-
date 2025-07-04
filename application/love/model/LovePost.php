<?php

namespace app\love\model;

use think\Model;
class LovePost extends Model
{

    protected $autoWriteTimestamp = 'datetime'; // 明确指定用datetime格式
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $dateFormat = 'Y-m-d H:i:s';

    // 设置数据表名
    protected $table = 'love_post';

    // 设置主键
    protected $pk = 'id';

    // 自动写入时间戳，为什么用这个要报错啊QAQ
//    protected $autoWriteTimestamp = true;

    // 定义字段类型和结构，直接在数据库中加字段好像也可以
    protected $schema = [
        'id' => 'int',
        'user_id' => 'int',
        'type' => 'int',
        'text' => 'string',
        'is_anonymous' => 'bool',
        'create_time' => 'datetime',
        'update_time' => 'datetime',
        'like_count' => 'int', // 新增点赞数，但是统计的方法好诡异
        'share_count' => 'int',  //分享字段数，不是查询的，是累加的
        'comment_count' => 'int', // 评论数字段
    ];

    // 定义类型转换
    protected $type = [
        'is_anonymous' => 'boolean',
    ];

    // 关联用户模型
    public function user()
    {
        return $this->belongsTo('User', 'user_id');
    }

    // 关联互动模型
    public function interactions()
    {
        return $this->hasMany('LoveInteraction', 'post_id');
    }
}