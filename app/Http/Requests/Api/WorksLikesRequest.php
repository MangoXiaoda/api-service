<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\FormRequest;

class WorksLikesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'works_id'   => 'required',
            'likes_type' => 'required',
        ];
    }


    public function attributes()
    {
        return [
            'works_id'   => '作品',
            'likes_type' => '赞类型'
        ];
    }


}
