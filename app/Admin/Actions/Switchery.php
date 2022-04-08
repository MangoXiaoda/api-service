<?php
/*
 * @Description: 开关操作
 * @Author: lizhongda
 * @Date: 2022/3/25 下午16:22
 */

namespace App\Admin\Actions;

use Dcat\Admin\Grid\RowAction;
use Illuminate\Http\Request;

class Switchery extends RowAction
{
    protected function html()
    {
        $icon = ($this->row->{$this->getColumnName()}) == 1 ? 'fa-toggle-on' : 'fa-toggle-off';

        return <<<HTML
<i class="{$this->getElementClass()} fa {$icon}"></i>
HTML;
    }

    public function handle(Request $request)
    {
        try {
            $class = $request->class;
            $column = $request->column;
            $id = $this->getKey();

            $model = $class::find($id);

            // 账号状态 数据修改
            if ($column == 'status')
                $model->{$column} = $model->{$column} == 1 ? -1 : 1;

            // 权限状态 数据修改
            if ($column == 'permission_status')
                $model->{$column} = (int) !$model->{$column};

            $model->save();

            return $this->response()->success("操作成功
            ")->refresh();
        } catch (\Exception $e) {
            return $this->response()->error($e->getMessage());
        }
    }

    public function parameters()
    {
        return [
            'class' => $this->modelClass(),
            'column' => $this->getColumnName(),
        ];
    }

    public function getColumnName()
    {
        return $this->column->getName();
    }

    public function modelClass()
    {
        return get_class($this->parent->model()->repository()->model());
    }
}
