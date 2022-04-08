<?php

namespace App\Admin\Forms;

use App\Models\SystemSettings;
use Dcat\Admin\Widgets\Form;

class SignSetting extends Form
{

    /**
     * 签到 code 列表
     * @var string[]
     */
    private static $code_arr = [
        'SignOneDay',
        'SignTwoDay',
        'SignThreeDay',
        'SignFourDay',
        'SignFiveDay',
        'SignSixDay',
        'SignSevenDay',
        'SignFinishMonth'
    ];

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
            // 更新签到配置数据
            if (in_array($key, self::$code_arr))
                SystemSettings::query()->where('code', $key)->update(['value' => $value]);

        }

        return $this->response()->success('操作成功')->refresh();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->number('SignOneDay', '连续签到一天得积分')->min(0);
        $this->number('SignTwoDay', '连续签到二天得积分')->min(0);
        $this->number('SignThreeDay', '连续签到三天得积分')->min(0);
        $this->number('SignFourDay', '连续签到四天得积分')->min(0);
        $this->number('SignFiveDay', '连续签到五天得积分')->min(0);
        $this->number('SignSixDay', '连续签到六天得积分')->min(0);
        $this->number('SignSevenDay', '连续签到七天得积分')->min(0);
        $this->number('SignFinishMonth', '签到满月额外得积分')->help('每月签到满勤，即可额外再赠送积分值');
    }

    /**
     * The data of the form.
     *
     * @return array
     */
    public function default()
    {
        $data = SystemSettings::query()
            ->whereIn('code', self::$code_arr)
            ->pluck('value', 'code')
            ->toArray();

        return $data;
    }
}
