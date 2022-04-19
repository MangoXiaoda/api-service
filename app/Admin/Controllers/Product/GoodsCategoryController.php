<?php

namespace App\Admin\Controllers\Product;

use App\Models\GoodsCategory;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;

class GoodsCategoryController extends AdminController
{
    // 页面标题
    protected $title = '商品分类';

    /**
     * 列表页
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new GoodsCategory(), function (Grid $grid) {

            $grid->model()->orderBy('updated_at', 'desc');

            // 第一列显示 id字段，并将这一列设置为可排序列
            $grid->column('id', 'ID')->sortable();

            $grid->column('c_name', '分类名称');

            $grid->column('c_sort', '排序');

            $grid->column('c_status', '状态')->switch();

        });

    }


    protected function form()
    {
        return Form::make(new GoodsCategory(), function (Form $form) {

            $form->display('id');

            $form->text('c_name', '商品分类名称');

            $form->number('c_sort', '排序');

            $form->switch('c_status', '状态')->default(1);

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

        });
    }

}
