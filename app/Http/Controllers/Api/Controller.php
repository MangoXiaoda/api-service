<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller as ApiBaseController;
use App\Traits\UserTrait;
use Illuminate\Http\Request;

class Controller extends ApiBaseController
{
    use UserTrait;

    /**
     * @var 用户ID
     */
    protected $userinfo;

    /**
     * 全局通用
     * Controller constructor.
     */
    public function __construct()
    {

        $this->userinfo = $this->user();
    }

}
