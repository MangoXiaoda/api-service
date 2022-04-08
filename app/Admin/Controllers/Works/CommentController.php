<?php

namespace App\Admin\Controllers\Works;

use App\Models\WorksComment;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;

class CommentController extends AdminController
{
    // 页面标题
    protected $title = '评论';

    /**
     * 列表页
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(WorksComment::with(['user']), function (Grid $grid) {

            $grid->model()->orderBy('updated_at', 'desc');

            // 第一列显示 id字段，并将这一列设置为可排序列
            $grid->column('id', 'ID')->sortable();

            $grid->column('user.nickname', '评论人');

            $grid->column('content', '评论内容');

            $grid->column('updated_at');

            // 隐藏操作列
            $grid->disableActions();

            // 隐藏创建数据按钮
            $grid->disableCreateButton();

            // filter($callback)方法用来设置表格的简单搜索框
            $grid->filter(function ($filter) {
                // 作品内容
                $filter->like('content', '评论内容');
            });
        });

    }

}
