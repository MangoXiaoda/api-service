<?php

namespace App\Services;

use App\Models\SystemSettings;

class SettingService extends Service
{

    /**
     * 获取系统设置value值
     * @param $code
     * @return int|mixed
     */
    public static function getSystemSettingValue($code)
    {
        if (!$code)
            return 0;

        $value = SystemSettings::query()->where('code', $code)->value('value');

        return $value;
    }

}
