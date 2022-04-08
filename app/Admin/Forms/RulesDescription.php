<?php

namespace App\Admin\Forms;

use App\Models\SystemSettings;
use Dcat\Admin\Widgets\Form;

class RulesDescription extends Form
{
    /**
     * Handle the form request.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function handle(array $input)
    {
        foreach ($input as $key => $value) {

            if ($key == 'RulesDescription')
                SystemSettings::query()->updateOrCreate(['code' => $key], [
                    'name' => '积分规则说明',
                    'code' => 'RulesDescription',
                    'value' => $value,
                ]);
        }

        return $this->response()->success('操作成功')->refresh();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->editor('RulesDescription', '');
    }

    /**
     * The data of the form.
     *
     * @return array
     */
    public function default()
    {
        $data = SystemSettings::query()
            ->where('code', 'RulesDescription')
            ->pluck('value', 'code')
            ->toArray();

        return $data;
    }
}
