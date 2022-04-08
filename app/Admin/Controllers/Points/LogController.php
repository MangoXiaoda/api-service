<?php

namespace App\Admin\Controllers\Points;

use App\Models\UserPointsLog;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;

class LogController extends AdminController
{

    // 页面标题
    protected $title = '积分日志';

    /**
     * 列表页
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(UserPointsLog::with(['user']), function (Grid $grid) {

            $grid->model()->orderBy('updated_at', 'desc');

            // 第一列显示 id字段，并将这一列设置为可排序列
            $grid->column('id', 'ID')->sortable();

            $grid->column('user.nickname', '用户');

            $grid->column('operator_type', '操作者类型')->display(function ($operator_type) {
                switch ($operator_type) {
                    case 1:
                        $str = '系统';
                        break;
                    case 2:
                        $str = '后台管理员';
                        break;
                    default:
                        $str = '未知';
                        break;
                }
                return $str;
            });

            $grid->column('change_type', '操作者类型')->display(function ($change_type) {
                switch ($change_type) {
                    case 1:
                        $str = '增加';
                        break;
                    case 2:
                        $str = '减少';
                        break;
                    default:
                        $str = '未知';
                        break;
                }
                return $str;
            });

            $grid->column('p_value', '积分值');

            $grid->column('msg', '日志详情');

            $grid->column('updated_at');

            // 隐藏操作列
            $grid->disableActions();

            // 隐藏创建数据按钮
            $grid->disableCreateButton();

            // filter($callback)方法用来设置表格的简单搜索框
            $grid->filter(function ($filter) {
                // 作品内容
                $filter->like('user.nickname', '用户');
            });
        });
    }

}
