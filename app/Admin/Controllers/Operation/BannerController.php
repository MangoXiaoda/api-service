<?php

namespace App\Admin\Controllers\Operation;

use App\Models\Banners;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;

class BannerController extends AdminController
{
    // 页面标题
    protected $title = '轮播图';

    /**
     * 列表页
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Banners(), function (Grid $grid) {

            $grid->model()->orderBy('updated_at', 'desc');

            // 第一列显示 id字段，并将这一列设置为可排序列
            $grid->column('id', 'ID');

            $grid->column('name', '名称');

            $grid->column('image', '图片')->image('http://xxx.com', 100, 50);

            $grid->column('url', '链接');

            $grid->column('status', '状态')->switch();

            $grid->column('sort', '排序');

            $grid->column('start_time', '开始时间');

            $grid->column('end_time', '结束时间');

            $grid->column('updated_at');
        });
    }

    /**
     * 表单页
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Banners(), function (Form $form) {

            $form->display('id');

            $form->text('name', '名称');

            $form->image('image', '图片')
                ->accept('jpg,png,gif,jpeg')
                ->autoUpload()
                ->uniqueName();

            $form->text('url', '链接');

            $form->text('sort', '排序')
                ->width(2, 2)
                ->placeholder('请输入排序值')
                ->default(0);

            $form->datetime('start_time', '开始时间')->format('YYYY-MM-DD HH:mm:ss');

            $form->datetime('end_time', '结束时间')->format('YYYY-MM-DD HH:mm:ss');

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


    protected function detail($id)
    {
        return Show::make($id, new Banners(), function (Show $show) {

            $show->field('id');
            $show->field('name', '名称');
            $show->field('image', '图片')->image();
            $show->field('url', '链接');
            $show->field('status', '审核状态')->as(function () {
                $status = $this->status;

                switch ($status) {
                    case 0:
                        $str = '关闭';
                        break;
                    case 1:
                        $str = '正常';
                        break;
                    default:
                        $str = '未知';
                        break;
                }

                return $str;
            });
            $show->field('start_time', '开始时间');
            $show->field('end_time', '结束时间');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

}
