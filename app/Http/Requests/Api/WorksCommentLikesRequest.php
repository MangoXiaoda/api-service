<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\FormRequest;

class WorksCommentLikesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'comment_id' => 'required',
            'likes_type' => 'required',
        ];
    }

    /**
     * @return string[]
     */
    public function attributes()
    {
        return [
            'comment_id' => '作品评论',
            'likes_type' => '赞类型'
        ];
    }
}
