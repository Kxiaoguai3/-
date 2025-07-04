<?php

namespace app\love\controller;

use app\love\model\LovePost;
use app\love\validate\LovePost as LovePostValidate;
use \app\love\validate\LoveInteraction as LoveInteractionValidate;
use think\Request;

class Love
{
    //发布帖子
    public function publish(Request $request)
    {
        // 获取请求数据
        $data = $request->param();

        // 验证数据，参数出问题会来这里
        $validate = new LovePostValidate();
        if (!$validate->check($data)) {
            return json([
                'code' => 400,
                'msg'  => $validate->getError()
            ]);
        }

        // 创建帖子
        try {
            $post = LovePost::create([
                'user_id'       => $data['user_id'],
                'type'          => $data['type'],
                'text'          => $data['text'],
                'is_anonymous'  => isset($data['is_anonymous']) ? $data['is_anonymous'] : false
            ]);

            // 返回成功响应
            return json([
                'code' => 0,
                'msg'  => 'success',
                'data' => [
                    'post_id' => $post->id,
                    'user_id' => $post->user_id
                ]
            ]);

        } catch (\Exception $e) {
            // 返回错误响应，服务器出问题来这里
            return json([
                'code' => 500,
                'msg'  => '发布失败: ' . $e->getMessage()
            ]);
        }
//        return 'publish';
    }

    public function list_(Request $request)
    {
        // 获取请求参数
        $data = $request->param();

        // 验证参数，抄ai的
        $page = isset($data['page']) ? max(1, intval($data['page'])) : 1;
        $pageSize = isset($data['pageSize']) ? max(1, intval($data['pageSize'])) : 10;
        $type = isset($data['type']) ? intval($data['type']) : null;

        try {
            //只查询通过审核的
//            // 构建查询
//            $query = LovePost::with(['user' => function($query) {
//                $query->field('id,user_name');
//            }])->order('create_time', 'desc');

            $query = LovePost::where('status', 1) // 只查询已通过的帖子
            ->with(['user' => function($query) {
                $query->field('id,user_name');
            }]);

            // 按类型筛选
            if ($type !== null) {
                $query->where('type', $type);
            }

            // 获取分页数据
            $list = $query->paginate([
                'page' => $page,
                'list_rows' => $pageSize
            ]);

            // 格式化返回数据
            $result = [
                'code' => 0,
                'msg' => 'success',
                'data' => [
                    'total' => $list->total(),
                    'per_page' => $list->listRows(),
                    'current_page' => $list->currentPage(),
                    'last_page' => $list->lastPage(),
                    'data' => $list->items()
                ]
            ];

            return json($result);

        } catch (\Exception $e) {
            return json([
                'code' => 500,
                'msg' => '获取列表失败: ' . $e->getMessage()
            ]);
        }
    }


    //添加互动
    public function interact(Request $request)
    {
        // 获取请求数据
        $data = $request->param();

        // 验证数据
//        $validate = new \app\love\validate\LoveInteraction();
        $validate = new LoveInteractionValidate();
        $scene = $data['type'] == 2 ? 'comment' : ($data['type'] == 1 ? 'like' : 'share');
        if (!$validate->scene($scene)->check($data)) {
            return json([
                'code' => 400,
                'msg'  => $validate->getError()
            ]);
        }

        try {
            // 检查是否重复操作 (点赞/转发防重复)
            if ($data['type'] != 2 && $data['type'] != 3) { // 评论允许重复，转发应该也可以重复吧
                $exists = \app\love\model\LoveInteraction::where([
                    'post_id' => $data['post_id'],
                    'user_id' => $data['user_id'],
                    'type'    => $data['type']
                ])->find();

                if ($exists) {
                    return json([
                        'code' => 600,        //重复点赞600，不过这个应该不会发生，我会提供取消点赞的接口，除非直接使用接口调用
                        'msg'  => '您已经点过赞了'
                    ]);
                }
            }

            // 检查评论内容是否重复 (防止刷屏)，抄的，AI真好用🤭
            if ($data['type'] == 2) {
                $recentComment = \app\love\model\LoveInteraction::where([
                    'post_id' => $data['post_id'],
                    'user_id' => $data['user_id'],
                    'type'    => 2,
                    'content' => $data['content']
                ])->whereTime('create_time', '>=', date('Y-m-d H:i:s', strtotime('-5 minutes')))
                    ->find();

                if ($recentComment) {
                    return json([
                        'code' => 601,    //相同评论，601
                        'msg'  => '5分钟内不能重复提交相同评论'
                    ]);
                }
            }

            // 创建互动记录，分享就不创建了吧
                if($data['type'] != 3){
                    $interaction = \app\love\model\LoveInteraction::create([
                        'post_id' => $data['post_id'],
                        'user_id' => $data['user_id'],
                        'type'    => $data['type'],
                        'content' => $data['type'] == 2 ? $data['content'] : null
                    ]);

                }
                else{
                    // 直接增加分享计数，不创建记录
                    \app\love\model\LovePost::where('id', $data['post_id'])
                        ->inc('share_count')
                        ->update();
                    //没有interaction的id，直接返回了
                    return json([
                        'code' => 0,
                        'msg'  => 'success',
                        'data' => [
                            '分享没有记录'
                        ]
                    ]);
                }

            // 如果是点赞，更新帖子点赞数
            if ($data['type'] == 1) {
                \app\love\model\LovePost::where('id', $data['post_id'])->setInc('like_count');

            }


            return json([
                'code' => 0,
                'msg'  => 'success',
                'data' => [
                    'interaction_id' => $interaction->id,
                    'post_id' => $interaction->post_id,
                    'user_id' => $interaction->user_id
                ]
            ]);

            //服务器问题来这里
        } catch (\Exception $e) {
            return json([
                'code' => 500,
                'msg'  => '操作失败: ' . $e->getMessage()
            ]);
        }
    }

    //取消点赞，哎呀，好像可以和上面放在一起，再来一个 type4 QAQ
    //删除互动的两个都在这里了，删除点赞和评论
    public function removeInteraction(Request $request)
    {
        $data = $request->param();

        // 验证
        $validate = new \app\love\validate\LoveInteraction();
        $scene = $data['type'] == 2 ? 'comment' : 'like';
        if (!$validate->scene($scene)->check($data)) {
            return json(['code' => 400, 'msg' => $validate->getError()]);
        }

        try {
            // 删除互动记录
            $query = \app\love\model\LoveInteraction::where([
                'user_id' => $data['user_id']
            ]);

            // 对于评论，使用精确的互动ID
            if ($data['type'] == 2) {
                $query->where('id', $data['id']);
            }
            // 对于点赞，使用原来的条件
            else {
                $query->where([
                    'post_id' => $data['post_id'],
                    'type'    => $data['type']
                ]);
            }

            $deleted = $query->delete();

            if ($deleted) {
                // 根据不同类型更新不同计数器
                if ($data['type'] == 1) {
                    // 更新点赞数
                    \app\love\model\LovePost::where('id', $data['post_id'])
                        ->setDec('like_count');
                    return json(['code' => 0, 'msg' => '取消点赞成功']);
                } elseif ($data['type'] == 2) {
                    // 更新评论数
                    \app\love\model\LovePost::where('id', $data['post_id'])
                        ->setDec('comment_count');
                    return json(['code' => 0, 'msg' => '删除评论成功']);
                }
            } else {
                $msg = $data['type'] == 1 ? '您还未点赞' : '评论不存在或已被删除';
                return json(['code' => 602, 'msg' => $msg]);
            }

        } catch (\Exception $e) {
            return json([
                'code' => 500,
                'msg'  => '操作失败: ' . $e->getMessage()
            ]);
        }
    }

    public function removePost(Request $request){
        $data = $request->param();
        $data['type'] = 1;
        $data['text'] = 'NULL POST';

        $validate = new LovePostValidate();
        if(!$validate->check($data)){
            return json([
                'code' => 400,
                'msg'  => $validate->getError()
            ]);
        }
        try{
            //先判断帖子id的user_id是否和删除帖子的user_id一致


            $post = LovePost::find($data['id']);

            if (!$post) {
                return json([
                    'code' => 404,
                    'msg' => '帖子不存在'
                ]);
            }

            if($post->user_id == $data['user_id']){
                $post->delete();
                return json([
                    'code' => 0,
                    'msg'  => 'success'
                ]);
            }
            else{
                return json([
                    'code' => 500,
                    'msg' => '权限不足'
                ]);
            }

        }catch (\Exception $e){
            return json([
                'code' => 500,
                'msg' => $e->getMessage()
            ]);
        }
    }

}