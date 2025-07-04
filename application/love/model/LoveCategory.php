<?php

namespace app\love\model;

use think\Model;
class LoveCategory extends Model
{

    protected $allowField = ['name', 'status', 'sort'];
    // 定义时间戳字段名

    protected $autoWriteTimestamp = 'datetime'; // 明确指定用datetime格式
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $dateFormat = 'Y-m-d H:i:s';

    // 设置数据表名
    protected $table = 'love_category';

    // 设置主键
    protected $pk = 'id';

    protected $schema = [
        'id' => 'int',
        'name' => 'string',
        'status' => 'bool',
        'sort' => 'int',
    ];

    // 状态常量
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    // 获取器：状态文字显示
    public function getStatusTextAttr($value, $data): string
    {
        $status = [1 => '启用', 0 => '禁用'];
        return $status[$data['status']] ?? '未知';
    }

    // 关联用户模型
    public function post()
    {
        return $this->belongsTo('LovePost', 'post_id');
    }
}