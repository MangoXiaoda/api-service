<?php

namespace App\Admin\Controllers\User;

use App\Models\User;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use App\Admin\Actions\Switchery;

class UserController extends AdminController
{
    // 页面标题
    protected $title = '用户';

    /**
     * 列表页
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new User(), function (Grid $grid) {

            // 第一列显示 id字段，并将这一列设置为可排序列
            $grid->column('id', 'ID')->sortable();

            // 第二列
            $grid->column('avatar', '头像')
//                ->gravatar('');
                ->image('http://xxx.com', 50, 50);

            // 第三列
            $grid->column('name', '真实姓名');

            // 第四列
            $grid->column('nickname', '微信昵称');

            // 第五列
            $grid->column('gender', '性别')->display(function ($gender) {
                $str = '';
                switch ($gender) {
                    case 1:
                        $str = '男';
                        break;
                    case 0:
                        $str = '女';
                        break;
                    case 3:
                        $str = '未知';
                        break;
                }
                return $str;
            });

            // 第六列
            $grid->column('phone', '手机号');

            // 第七列
            $grid->column('status', '账号状态')->action(Switchery::class);

            // 第八列
            $grid->column('permission_status', '权限状态')->action(Switchery::class);

            // 第七列
            $grid->column('point_count', '用户积分');

            // 第八列
            $grid->column('updated_at');

            // 隐藏操作列
            $grid->disableActions();

            // 隐藏创建数据按钮
            $grid->disableCreateButton();

            // 去掉行选择
            $grid->disableRowSelector();

            // filter($callback)方法用来设置表格的简单搜索框
            $grid->filter(function ($filter) {
                // 真实姓名
                $filter->like('name', '真实姓名');
                // 手机号
                $filter->like('phone', '手机号');
                // 昵称搜索
                $filter->like('nickname', '昵称');
            });
        });
    }



}
