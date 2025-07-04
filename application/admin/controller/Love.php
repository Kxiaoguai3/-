<?php

namespace app\admin\controller;

use app\admin\validate\LovePost as LovePostValidate;
use app\love\model\LoveCategory;
use app\love\model\LovePost;
use think\Request;
use app\admin\validate\LoveCategory as LoveCategoryValidate;

class Love
{

    //这个连起来一起的可能用不上了
    public function categories(Request $request)
    {
        $action = $request->param('action');
        switch ($action) {
            case 'list':   return $this->getCategoryList($request);
            case 'add':    return $this->addCategory($request);
            case 'edit':   return $this->editCategory($request);
            case 'delete': return $this->deleteCategory($request);
            case 'status': return $this->toggleStatus($request);
            default:       return json(['code'=>400, 'msg'=>'非法操作']);
        }
    }

// 获取分类列表
    public function getCategoryList(Request $request)
    {
        $params = $request->param();
        try {
            $query = LoveCategory::field('id,name,status,sort,create_time,update_time');

            if (!empty($params['keyword'])) {
                $query->where('name', 'like', "%{$params['keyword']}%");
            }

            $list = $query->order('sort DESC, id DESC')
                ->paginate([
                    'page' => isset($params['page']) ? $params['page'] : 1,
                    'list_rows' => isset($params['pageSize']) ? $params['pageSize'] : 10
                ]);

            return json([
                'code' => 0,
                'data' => [
                    'total' => $list->total(),
                    'items' => $list->items()
                ]
            ]);
        } catch (\Exception $e) {
            return json(['code'=>500, 'msg'=>'获取失败: '.$e->getMessage()]);
        }
//        return 'admin categories';
    }

    // 添加分类，有问题！！！！！！！！！！！！！！！
    public function addCategory(Request $request)
    {

//        $data = $request->param();
        // 只获取需要的字段，排除路径参数
        $data = $request->only(['name', 'status', 'sort']);

        $validate = new LoveCategoryValidate();
        if (!$validate->scene('add')->check($data)) {
            return json(['code'=>400, 'msg'=>$validate->getError()]);
        }

        try {
//            $category = LoveCategory::create($data);
            $post = LoveCategory::create([
                'name' => $data['name'],
                'status' => $data['status'],
                'sort' => $data['sort']
            ]);
            return json([
                'code' => 0,
                'msg'  => 'success',
                'data' => [
                    'name' => $post->name,
                    'id' => $post->id,
                ],
            ]);
        } catch (\Exception $e) {
            return json(['code'=>500, 'msg'=>'添加失败: '.$e->getMessage()]);
        }
    }

    // 修改分类，有问题！！！！！！！！！！！！！！！！！
    public function editCategory(Request $request)
    {
        $data = $request->param();
        $validate = new LoveCategoryValidate();
        if (!$validate->scene('edit')->check($data)) {
            return json(['code'=>400, 'msg'=>$validate->getError()]);
        }

        try {
            $category = LoveCategory::get($data['id']);
            if (!$category) {
                return json(['code'=>404, 'msg'=>'分类不存在']);
            }

            $category->name = $data['name'];
            $category->status = $data['status'];
            $category->sort = $data['sort'];
            $category->save();
            return json(['code'=>0, 'msg'=>'更新成功']);
        } catch (\Exception $e) {
            return json(['code'=>500, 'msg'=>'更新失败: '.$e->getMessage()]);
        }
    }

    // 删除分类
    public function deleteCategory(Request $request)
    {
        $id = $request->param('id');
        if(empty($id)){
            return json([
                'code'=>400,
                'msg'=>'id不能为空'
            ]);
        }
        try {
            // 检查是否有帖子使用该分类
            $used = \app\love\model\LovePost::where('type', $id)->count();
            if ($used > 0) {
                return json(['code'=>400, 'msg'=>'该分类下有帖子，不可删除']);
            }

            LoveCategory::destroy($id);
            return json(['code'=>0, 'msg'=>'删除成功']);
        } catch (\Exception $e) {
            return json(['code'=>500, 'msg'=>'删除失败: '.$e->getMessage()]);
        }
    }

    // 切换状态,这是什么意思？？？？？？？？？？
    public function toggleStatus(Request $request)
    {
        $id = $request->param('id');
        try {
            $category = LoveCategory::find($id);
            if (!$category) {
                return json(['code'=>404, 'msg'=>'分类不存在']);
            }

            $category->status = $category->status == 1 ? 0 : 1;
            $category->save();

            return json([
                'code' => 0,
                'data' => ['status' => $category->status],
                'msg'  => '状态已更新'
            ]);
        } catch (\Exception $e) {
            return json(['code'=>500, 'msg'=>'操作失败: '.$e->getMessage()]);
        }
    }

    //审核
    public function audit(Request $request)
    {
        $data = $request->param();

        // 验证数据
        $validate = new LovePostValidate();
        if (!$validate->check($data)) {
            return json([
                //参数错误都是400
                'code' => 400,
                'msg' => $validate->getError()
            ]);
        }

        try {
            // 查找帖子
            $post = LovePost::find($data['id']);
            if (!$post) {
                return json([
                    //找不到帖子是404
                    'code' => 404,
                    'msg' => '帖子不存在'
                ]);
            }

            // 更新审核状态
            $post->status = $data['status'];
            $post->audit_time = date('Y-m-d H:i:s');
            $post->audit_reason = $data['status'] == 2 ? (isset($data['reason']) ? $data['reason'] : '') : null;  //审核状态时，有字时就存，没字就空字符


            //save失败不抛出异常，所以需要else
            if ($post->save()) {
                return json([
                    'code' => 0,
                    'msg' => '审核操作成功',
                    'data' => [
                        'post_id' => $post->id,
                        'status' => $post->status,
                        'audit_time' => $post->audit_time
                    ]
                ]);
            } else {
                return json([
                    'code' => 500,
                    'msg' => '审核操作失败'
                ]);
            }

        } catch (\Exception $e) {
            return json([
                'code' => 500,
                'msg' => '审核操作失败: ' . $e->getMessage()
            ]);
        }
    }

    //待测试

    //获取待审核
    public function pendingList(Request $request)
    {
        $page = $request->param('page', 1);
        $pageSize = $request->param('pageSize', 10);

        try {
            $list = LovePost::with(['user' => function($query) {
                $query->field('id,user_name');
            }])
                ->where('status', 0)
                ->order('create_time', 'desc')
                ->paginate([
                    'page' => $page,
                    'list_rows' => $pageSize
                ]);

            if ($list->isEmpty()) {
                return json([
                    'code' => 404,
                    'msg' => '暂无待审核帖子',
                    'data' => [
                        'total' => 0,
                        'data' => []
                    ]
                ]);
            }

            return json([
                'code' => 0,
                'msg' => 'success',
                'data' => [
                    'total' => $list->total(),
                    'per_page' => $list->listRows(),
                    'current_page' => $list->currentPage(),
                    'last_page' => $list->lastPage(),
                    'data' => $list->items()
                ]
            ]);

        } catch (\Exception $e) {
            return json([
                'code' => 500,
                'msg' => '获取待审核列表失败: ' . $e->getMessage()
            ]);
        }
    }

}