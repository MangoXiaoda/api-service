<?php

namespace App\Admin\Controllers\Setting;

use App\Admin\Forms\WorksSetting;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Layout\Content;

class WorksSettingController extends AdminController
{

    public function index(Content $content)
    {
        return $content
            ->header('作品设置')
            ->body(new Card(new WorksSetting()));
    }

}
