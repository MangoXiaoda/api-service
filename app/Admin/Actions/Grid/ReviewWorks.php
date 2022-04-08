<?php

namespace App\Admin\Actions\Grid;

use Dcat\Admin\Grid\RowAction;
use App\Admin\Forms\ReviewWorks as ReviewWorksForm;
use Dcat\Admin\Widgets\Modal;

class ReviewWorks extends RowAction
{
    /**
     * @return string
     */
	protected $title = '审核作品';

    public function render()
    {
        // 实例化表单类并传递自定义参数
        $form = ReviewWorksForm::make()->payload(['id' => $this->getKey()]);

        return Modal::make()
            ->lg()
            ->title($this->title)
            ->body($form)
            ->button($this->title);
    }
}
