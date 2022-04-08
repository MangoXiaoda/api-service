<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\FormRequest;

class WorksCommentsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'works_id' => 'required',
            'content'  => 'required',
        ];
    }


    public function attributes()
    {
        return [
            'works_id' => '作品',
            'content'  => '评论内容',
        ];
    }


}
