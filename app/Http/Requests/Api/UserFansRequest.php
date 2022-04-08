<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UserFansRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required',
            'f_type'  => 'required'
        ];
    }


    public function attributes()
    {
        return [
            'user_id' => '用户',
            'f_type'  => '操作',
        ];
    }

}
