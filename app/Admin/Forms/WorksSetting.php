<?php

namespace App\Admin\Forms;

use App\Models\SystemSettings;
use Dcat\Admin\Widgets\Form;

class WorksSetting extends Form
{

    /**
     * 作品 code 列表
     * @var string[]
     */
    private static $code_arr = [
        'UserReleaseWorks',
        'UserCommentWorks',
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
        $this->switch('UserReleaseWorks', '发布是否需要审核')->help('CODE码：UserReleaseWorks');
        $this->switch('UserCommentWorks', '评论是否需要审核')->help('CODE码：UserCommentWorks');
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
