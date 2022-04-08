<?php
/*
 * @Description:自定义Http返回状态码
 * @Version:
 * @Author: LiZhongDa
 * @Date: 2022-03-11 16:40:33
 * @LastEditors: LiZhongDa
 * @LastEditTime: 2022-03-11 16:40:33
 */

namespace App\Enums\Http;

use BenSampo\Enum\Enum;

final class ResponseCode extends Enum
{
    /**
     * 通用返回code
     */
    /**
     * 参数有误
     */
    const PARAM_ERROR =   'K1001';
    /**
     * 数据已存在
     */
    const DATA_EXIST = 'K1002';
    /**
     * 数据有误
     */
    const DATA_ERROR =   'K1003';
    /**
     * 操作成功
     */
    const REQUEST_SUCCESS = 'K2000';
    /**
     * 禁止访问
     */
    const REQUEST_DENY = 'K4001';
    /**
     * 权限不足
     */
    const REQUEST_WITHOUT_AUTH = 'K4003';
    /**
     * 查询为空
     */
    const QUERY_VOID = 'K4004';
    /**
     * Token无效或过期
     */
    const TOKEN_EXPIRED = 'K4005';
    /**
     * 请求过于频繁
     */
    const TOO_MANY_REQUEST = 'K4029';
    /**
     * 操作失败
     */
    const REQUEST_FAILS = 'K5001';
    /**
     * 自定义
     */
    const CUSTOMIZE = 'K9999';
    /**
     * 登录失败
     */
    const LOGIN_FAIL = 'K6001';
    /**
     * 作品权限不足
     */
    const REQUEST_WITHOUT_WORKS = 'K40030';
}
