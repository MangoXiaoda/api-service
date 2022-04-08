<?php

namespace App\Admin\Forms;

use App\Models\Works;
use Dcat\Admin\Widgets\Form;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Contracts\LazyRenderable;

class ReviewWorks extends Form implements LazyRenderable
{
    use LazyWidget;

    /**
     * Handle the form request.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function handle(array $input)
    {
        // 获取外部传递参数
        $id = $this->payload['id'] ?? 0;

        // 表单参数
        $review_status = $input['review_status'] ?? null; // 状态:0未审核,1审核通过,2审核未通过
        $review_remark = $input['review_remark'] ?? '';   // 审核说明(包含不通过原因)

        if (!$id)
            return $this->response()->error('参数错误');

        $works = Works::query()->find($id);

        if (!$works)
            return $this->response()->error('作品不存在');

        $works->update(['review_status' => $review_status, 'review_remark' => $review_remark]);

        return $this->response()->success('操作成功')->refresh();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $select_options = [
            0 => '未审核',
            1 => '审核通过',
            2 => '审核未通过'
        ];
        $this->select('review_status', '状态')->options($select_options)->default(1);
        $this->textarea('review_remark', '审核说明');
    }

}
