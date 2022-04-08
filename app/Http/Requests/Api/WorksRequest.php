<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\FormRequest;

class WorksRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
//            'topics_category_id' => 'required',
            'content'            => 'required',
//            'introduction'       => 'required',
        ];
    }


    public function attributes()
    {
        return [
//            'topics_category_id' => '话题分类',
            'content'            => '作品内容',
//            'introduction'       => '作品简介',
        ];
    }

}
