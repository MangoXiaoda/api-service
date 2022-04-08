<?php

namespace App\Http\Controllers\Api;

use App\Services\WeChatService;
use Illuminate\Http\Request;

class WeChatController extends Controller
{

    public function wxPay(Request $request)
    {
        $data = $request->all();
        $data['user_info'] = $this->userinfo ?? [];

        $service = new WeChatService();


    }


}
