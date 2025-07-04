<?php

namespace app\love\model;

use think\Model;

class User extends Model
{
    // 设置数据表名
    protected $table = 'user';

    // 设置主键
    protected $pk = 'id';

    // 关联表白帖模型
    public function posts()
    {
        return $this->hasMany('LovePost', 'user_id');
    }
}