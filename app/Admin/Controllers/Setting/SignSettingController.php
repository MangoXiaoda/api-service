<?php

namespace App\Admin\Controllers\Setting;

use App\Admin\Forms\SignSetting;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Layout\Content;

class SignSettingController extends AdminController
{

    public function index(Content $content)
    {
        return $content
            ->header('签到设置')
            ->body(new Card(new SignSetting()));
    }

}
