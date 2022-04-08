<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\FormRequest;

class AuthRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->route()->getActionMethod())
        {
            case 'weappLogin':
            {
                return [
                    'code' => 'required'
                ];
            }
        }
    }

    public function attributes()
    {
        return [
            'code' => '微信 code'
        ];
    }
}
