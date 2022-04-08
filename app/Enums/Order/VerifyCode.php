<?php

namespace App\Enums\Order;

use BenSampo\Enum\Enum;

/**
 *
 * 订单日志操作类型 action
 *
 * Class VerifyCode
 * @package App\Enums\Model
 */
final class VerifyCode extends Enum
{

    /**
     * 店铺核销普通商品验证操作
     */
    const STORE_VERIFY_GOODS_UPDATE = 1;

    /**
     * 主订单核销码状态更新操作
     */
    const ORDER_VERIFY_CODE_UPDATE = 2;

    /**
     * 用户支付创建
     */
    const USER_PAY_CREATE = 3;

    /**
     * 用户删除订单
     */
    const USER_ORDER_DELETE = 5;

    /**
     * 用户提交退款信息
     */
    const USER_SUBMIT_REFUND_INFO = 2001;

    /**
     * 用户提交取消信息
     */
    const USER_SUBMIT_CANCEL_REFUND = 2002;

    /**
     * 订单退款审核失败
     */
    const SYSTEM_REFUND_AUDIT_FALSE = 2003;

    /**
     * 退款审核成功
     */
    const SYSTEM_REFUND_AUDIT_SUCCESS = 2004;

    /**
     * 订单退款成功
     */
    const SYSTEM_REFUND_SUCCESS = 2005;

    /**
     * 订单退款失败
     */
    const SYSTEM_REFUND_FALSE = 2006;

}
