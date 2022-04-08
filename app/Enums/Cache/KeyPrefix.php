<?php

namespace App\Enums\Cache;

use BenSampo\Enum\Enum;

/**
 * Class KeyPrefix
 * 命名规范(注意大写):Redis key命名以key所代表的value类型结尾，以提高可读性
 * 一般:
 *   1) 第一段放置项目名或缩写 如 project
 *   2) 第二段把表名转换为key前缀 如, user:
 *   3) 第三段放置用于区分区key的字段,对应mysql中的主键的列名,如userid
 *   4) 第四段放置主键值或关键数据值,如18,16
 * 结合起来=>:PRO:USER:UID:18
 * 若表名为蛇形，则应以蛇形规定
 * @package App\Enums
 */
final class KeyPrefix extends Enum
{
    // 例: const ORDER = 'LSH:USER_ORDER:'

    const APP_PREFIX = 'LJ:';

    /**
     * 小程序相关数据
     */
    const MINI_PROGRAM =  self::APP_PREFIX.'MINI_PROGRAM:';


}
