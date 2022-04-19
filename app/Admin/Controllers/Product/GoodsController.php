<?php

namespace App\Admin\Controllers\Product;

use App\Models\Goods;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;

class GoodsController extends AdminController
{
    // 页面标题
    protected $title = '商品';

    /**
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(Goods::with([]), function (Grid $grid) {

            $grid->model()->orderBy('updated_at', 'desc');

            // 第一列显示 id字段，并将这一列设置为可排序列
            $grid->column('id', 'ID')->sortable();

            $grid->column('title', '商品名称');

            $grid->column('thumb', '商品主图')->image('http://xxx.com', 200, 200);

            $grid->column('content', '商品详情')->limit(30);

            $grid->column('total', '商品库存');

            $grid->column('price', '商品价格');

            $grid->column('cost_price', '商品成本价');

            $grid->column('updated_at');
        });
    }

    /**
     * 表单页
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Goods(), function (Form $form) {

            $form->display('id');

            $form->text('title', '商品名称')->required();

            $form->image('thumb', '商品主图')
                ->accept('jpg,png,gif,jpeg')
                ->autoUpload()
                ->autoSave(false)//不自动保存，避免提交出错
                ->uniqueName()
                ->required();

            $form->textarea('content', '商品详情');

            $form->currency('price', '商品售价');

            $form->currency('cost_price', '商品成本价');

            $form->number('total', '商品库存');

            $form->switch('status', '状态')->default(1);

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

    /**
     * 显示页
     * @param $id
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new Goods(), function (Show $show) {

            $show->field('id');

            $show->field('title', '商品名称');

            $show->field('thumb', '商品主图')->image();

            $show->field('content', '商品详情');

            $show->field('price', '商品价格');

            $show->field('cost_price', '商品成本价');

            $show->field('total', '商品库存');

            $show->field('sales', '已售数量');

            $show->field('status', '状态');

            $show->field('view_count', '查看次数');

            $show->field('created_at');
            $show->field('updated_at');
        });


    }


}
