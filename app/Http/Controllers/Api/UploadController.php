<?php

namespace App\Http\Controllers\Api;

use App\Enums\Http\ResponseCode;
use App\Handlers\Upload;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadController extends Controller
{

    /**
     * 上传接口
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadFile(Request $request)
    {
        $data = $request->all();

        // 前端接口文件格式限制
        $data['allowed_type'] = ['png', 'jpg', 'gif', 'jpeg', 'mp4', '3gp', 'm3u8'];

        $file = $request->file('file');
        if (!$file)
            return response()->json($this->type(ResponseCode::DATA_ERROR)->httpResponse('','请上传文件'));

        $upload = new Upload($data);
        $result = $upload->upload($file);

        return $result;
    }
}
