<?php

namespace App\Admin\Repositories;

use App\Models\TopicsCategory as Model;
use Dcat\Admin\Form;
use Dcat\Admin\Admin;
use Dcat\Admin\Repositories\EloquentRepository;

class TopicsCategory extends EloquentRepository
{
    /**
     * 话题数据表模型
     *
     * @var string
     */
    protected $eloquentClass = Model::class;



}
