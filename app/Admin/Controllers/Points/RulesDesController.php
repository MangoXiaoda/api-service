<?php

namespace App\Admin\Controllers\Points;

use App\Admin\Forms\RulesDescription;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Layout\Content;

class RulesDesController extends AdminController
{

    public function index(Content $content)
    {
        return $content
            ->header('积分规则说明')
            ->body(new Card(new RulesDescription()));
    }

}
