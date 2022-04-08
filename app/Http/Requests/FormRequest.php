<?php
/*
 * @Description:表单验证器基类
 * @Version:
 * @Author: LiZhongDa
 * @Date: 2022-03-11 16:40:33
 * @LastEditors: LiZhongDa
 * @LastEditTime: 2022-03-11 16:40:33
 */

namespace App\Http\Requests;

use App\Enums\Http\ResponseCode;
use App\Traits\HttpResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FormRequest extends BaseFormRequest
{
    use HttpResponseTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * 重构参数验证返回信息
     * @param Validator $validator
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        if ($this->container['request'] instanceof Request) {
            throw new HttpResponseException(
                response($this->type(ResponseCode::PARAM_ERROR)->httpResponse(request()->all(), $validator->errors()->first()))
            );
        }
        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }

}
