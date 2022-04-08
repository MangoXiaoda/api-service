<?php
/*
 * @Description: http 返回参数配置
 * @Version:
 * @Author: LiZhongDa
 * @Date: 2022-03-11 16:40:33
 * @LastEditors: LiZhongDa
 * @LastEditTime: 2022-03-11 16:40:33
 */
use App\Enums\Http\ResponseCode;

return [
    'message' => [
        /**
         * Http response
         */
        ResponseCode::PARAM_ERROR => '参数有误',
        ResponseCode::DATA_EXIST => '数据已存在',
        ResponseCode::DATA_ERROR => '数据有误',
        ResponseCode::REQUEST_SUCCESS => '操作成功',
        ResponseCode::REQUEST_DENY => '禁止访问',
        ResponseCode::REQUEST_WITHOUT_AUTH => '权限不足',
        ResponseCode::QUERY_VOID => '查询为空',
        ResponseCode::TOKEN_EXPIRED => 'token无效',
        ResponseCode::TOO_MANY_REQUEST => '请求过于频繁',
        ResponseCode::REQUEST_FAILS => '操作失败',
        ResponseCode::REQUEST_WITHOUT_WORKS => '作品权限不足',
    ]
];

