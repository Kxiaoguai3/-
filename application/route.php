<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],
    'love/list' => ['love/love/list_', ['method' => 'get']],
    'love/publish' => ['love/Love/publish', ['method' => 'post']],
    'love/interact' => ['love/Love/interact',['method'=>'post']],
    'love/removeInteraction' => ['love/Love/removeInteraction',['method'=>'post']],
    'love/removePost' => ['love/Love/removePost',['method'=>'post']],

    'admin/love/audit' =>['admin/love/audit', ['method' => 'post']],
    'admin/love/categories' =>['admin/love/categories', ['method' => 'post']],
    'admin/love/pendingList' =>['admin/love/pendingList', ['method' => 'get']],

    'admin/love/getCategoryList' =>['admin/love/getCategoryList', ['method' => 'get']],
//    'admin/love/addCategory' =>['admin/love/addCategory', ['method' => 'post']],
//    'admin/love/editCategory' =>['admin/love/editCategory', ['method' => 'post']],
    'admin/love/deleteCategory' =>['admin/love/deleteCategory', ['method' => 'post']],
    'admin/love/addCategory' => ['admin/love/addCategory', ['method' => 'post']],
    'admin/love/editCategory' => ['admin/love/editCategory', ['method' => 'post']],
];
