<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\FormRequest;

class GoodsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'   => 'required',
            'gs_pics' => 'required',
            'content' => 'required',
            'price'   => 'required',
            'total'   => 'required',
        ];
    }

    /**
     * @return string[]
     */
    public function attributes()
    {
        return [
            'title'   => '商品名称',
            'gs_pics' => '商品图片',
            'content' => '商品详情',
            'price'   => '商品售价',
            'total'   => '商品库存',
        ];
    }

}
