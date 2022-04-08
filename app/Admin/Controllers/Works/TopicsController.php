<?php

namespace App\Admin\Controllers\Works;

use App\Admin\Repositories\TopicsCategory;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;

class TopicsController extends AdminController
{
    // 页面标题
    protected $title = '话题';

    /**
     * 列表页
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new TopicsCategory(), function (Grid $grid) {

            // 第一列显示 id字段，并将这一列设置为可排序列
            $grid->column('id', 'ID')->sortable();

            // 第二列
            $grid->column('name', '话题名称');

            // 第三列
            $grid->column('status', '状态')->switch();

            // 第四列
            $grid->column('sort', '排序');

            // 第五列
            $grid->column('updated_at');

            // filter($callback)方法用来设置表格的简单搜索框
            $grid->filter(function ($filter) {
                // 话题名称搜索
                $filter->like('name', '话题名称');
            });

            // 去掉行选择
            $grid->disableRowSelector();

        });
    }

    /**
     * 表单页
     * @return Form
     */
    protected function form()
    {
        return Form::make(new TopicsCategory(), function (Form $form) {

            $form->display('id');

            $form->text('name', '话题名称')->required();

            $form->switch('status', '状态')->default(1);

            $form->text('sort', '排序')
                ->width(2, 2)
                ->placeholder('请输入排序值')
                ->default(0);

            $form->footer(function ($footer) {

                // 去掉`重置`按钮
                $footer->disableReset();

                // 去掉`查看`checkbox
                $footer->disableViewCheck();

                // 去掉`继续编辑`checkbox
                $footer->disableEditingCheck();

                // 去掉`继续创建`checkbox
                $footer->disableCreatingCheck();

            });

//            $form->submitted(function (Form $form) {
//
//                // 接收表单参数
//                $name = $form->name;
//
//                // 验证逻辑
//                if (!$name)
//                    $form->responseValidationMessages('name', '名称不能为空');
//
//            });

        });
    }

    /**
     * 显示页
     * @param $id
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new TopicsCategory(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('status');
            $show->field('sort');
            $show->field('created_at');
            $show->field('updated_at');
        });


    }



}
