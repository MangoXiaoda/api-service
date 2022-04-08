<?php

/**
 * 将文件地址转为绝对地址
 * @param $url
 * @return string 结果数据
 */
function fileUrlToWebUrl ($url) {
    // 已经为绝对地址不处理
    if (strpos($url, 'http') === 0)
        return $url;

    if (strpos($url, '/') === 0) {
        $url = substr($url, 1);
    }

    // Dcat_admin 后台上传地址
    if (strpos($url, 'files/') !== false || strpos($url, 'images/') !== false) {
        $app_env = config('app.env');
        if ($app_env === 'local')
            return config('app.url') . '/uploads/' . $url;
        // 测试环境 env 参数
        if ($app_env === 'lj')
            return config('app.url') . '/uploads/' . $url;
        return '' . $url;
    }

    // 自定义上传接口地址
    if (strpos($url, 'storage/') !== false) {
        $app_env = config('app.env');
        if ($app_env === 'local')
            return config('app.url') . '/' . $url;
        // 测试环境 env 参数
        if ($app_env === 'lj')
            return config('app.url') . '/' . $url;
        return '' . $url;
    }

    return $url;
}

/**
 * 去除url前缀域名
 * @param $url
 * @return string 结果数据
 */
function delWebPrefixUrl($url) {
    $app_url = config('app.url');

    // 已去除前缀不处理
    if (strpos($url, $app_url) === false)
        return $url;

    // 目前只处理 laravel 目录文件
    if (strpos($url, 'storage/') !== false)
        return str_replace($app_url, '', $url);

    return $url;
}

/**
 * 富文本中图片的域名转换
 * @param string $content 要替换的内容
 * @param string $strUrl 内容中图片要加或去除的域名
 * @param integer $type 类型：0 = 减少域名,1 = 增加域名
 * @return string
 */
function replaceEditorPicUrl($content = null, $strUrl = null, $type=0)
{
    if (!$content || !$strUrl)
        return $content;

    //提取图片路径的src的正则表达式 并把结果存入$matches中
    preg_match_all("/(*ANY)<img\s?src=\"(.*?)\".*?>/",$content,$matches);
    $img = "";

    if(!empty($matches)) {
        //注意，上面的正则表达式说明src的值是放在数组的第二个中
        $img = $matches[1];
    }else {
        $img = "";
    }

    if (empty($img))
        return $content;

    $patterns = [];
    $replacements = [];

    foreach($img as $imgItem){
        if($type){
            // 检测是否已添加过域名 如果添加过则不需要拼接域名
            $final_imgUrl = strpos($imgItem, $strUrl) === 0 ? $imgItem : $strUrl.$imgItem;
        }else{
            $final_imgUrl = str_replace($strUrl, '', $imgItem);
        }
        $replacements[] = $final_imgUrl;
        $img_new = "/".preg_replace("/\//i","\/",$imgItem)."/";
        $patterns[] = $img_new;
    }

    //让数组按照key来排序
    ksort($patterns);
    ksort($replacements);

    //替换内容
    $vote_content = preg_replace($patterns, $replacements, $content);
    return $vote_content;
}

/**
 * 返回数据
 * @param int $code 返回码
 * @param array $data 结果数据
 * @param string $desc  问题描述
 * @param false $toJson 是否返回json 格式
 * @return array|false|string 结果数据
 */
function r_result($code, $desc = '', $data = [], $toJson = false)
{
    $arr = [
        'code' => $code,
        'data' => $data,
        'desc' => $desc
    ];

    if (!$toJson)
        return $arr;

    return LjJencode($arr);
}

/**
 * 将数组转换为json 字符串
 * @param $arr
 * @param string $options
 * @return false|string
 */
function LjJencode($arr, $options = "JSON_UNESCAPED_UNICODE")
{
    if (!is_array($arr))
        return $arr;

    return json_encode($arr, constant($options));
}

/**
 * 将json 字符串转换为数组
 * @param $str
 * @return array|mixed 结果数据
 */
function LjJdecode($str)
{
    if (is_array($str))
        return $str;

    return json_decode($str, true);
}

/**
 * 使用CURL的方式发送请求
 * @param string $url 请求地址
 * @param string $data POST数组
 * @param string $method POST/GET，默认GET方式
 * @return bool|string
 */
function CurlRequest($url, $data = '', $method = 'GET')
{
    $curl = curl_init();                                                        // 启动一个CURL会话

    curl_setopt($curl, CURLOPT_URL, $url);                               // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);              // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);              // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);   // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);                  // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);                     // 自动设置Referer

    if($method=='POST'){
        curl_setopt($curl, CURLOPT_POST, 1);         // 发送一个常规的Post请求
        if ($data != ''){
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        }
    }

    if($method == 'json'){
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);     // $data JSON类型字符串
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data)));
    }

    curl_setopt($curl, CURLOPT_TIMEOUT, 30);       // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0);         // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回

    $tmpInfo = curl_exec($curl); // 执行操作
    curl_close($curl);           // 关闭CURL会话

    return $tmpInfo;             // 返回数据
}

/**
 * 切割并重组字符串
 * @param $str 字符串
 * @param int $length 可选。规定每个数组元素的长度。默认是 1
 * @param string $separator 可选。规定数组元素之间放置的内容。默认是 ""（空字符串）
 * @return string|null
 */
function LjStrSplit($str, $length = 4, $separator = ' ')
{
    if (!$str)
        return $str;

    $arr = str_split($str, $length);

    if (!$arr)
        return $str;

    $new_str = implode($separator, $arr);

    return $new_str;
}

/**
 * 获取程序执行开始的时间戳
 * @param  $mse 是否精确到毫秒，默认为  false
 * @return int | float 结果数据
 */
function GetStartTimeStamp ($mse = false) {
    if (!$mse)
        return (int)LARAVEL_START;
    return LARAVEL_START;
}

/**
 * 获取当前的系统时间戳
 * @param bool $mse 返回结果是否精确到好秒值
 * @return int | float 结果数据
 */
function GetNowTimeStamp ($mse = false) {
    if ($mse)
        return microtime(true);

    return (int)microtime(true);
}

/**
 * 获取一天的开始和结束时间戳
 * @param int $time 指定时间戳
 * @param int $is_format 是否需要格式化时间戳
 * @return array 结果数据
 */
function GetDayStarEndTime ($time = 0, $is_format = 0) {
    if (!$time)
        $time = GetStartTimeStamp();
    $stime = mktime(0,0,0,date('m', $time),date('d', $time),date('Y', $time));
    $etime = $stime + 3600*24 - 1;

    // 是否需要格式化时间戳
    if ($is_format)
        return ['stime' => date('Y-m-d H:i:s', $stime), 'etime' => date('Y-m-d H:i:s', $etime)];

    return ['stime' => $stime, 'etime' => $etime];
}

/**
 * 获取一天内 多个时间段的时间 （如 0时，6时，12时，18时等）
 * @param int $time
 * @return array
 */
function GetDayStarEndDifferentTimeRange($time = 0)
{
    $time_arr = GetDayStarEndTime($time);
    $stime = $time_arr['stime'];

    // 获取 一天内 0时 6时 12时 18时 24时 时间
    $six_time         = GetExtendTime($stime, 6);
    $twelve_time      = GetExtendTime($stime, 12);
    $eignteen_time    = GetExtendTime($stime, 18);
    $twenty_four_time = GetExtendTime($stime, 24);

    return[
        'start_time'       => $stime,
        'six_time'         => $six_time,
        'twelve_time'      => $twelve_time,
        'eignteen_time'    => $eignteen_time,
        'twenty_four_time' => $twenty_four_time
    ];
}

/**
 * 获取延长的时间
 * @param $time 指定时间戳
 * @param int $extend_num 延长时间 单位小时
 * @param int $is_format 是否格式化时间戳
 * @return false|float|int|string
 */
function GetExtendTime($time, $extend_num = 0, $is_format = 0)
{
    if (!$time)
        $time = GetStartTimeStamp();

    $extend_time = $time + 3600 * $extend_num - 1;

    if ($is_format)
        return date('Y-m-d H:i:s', $time + 3600 * $extend_num - 1);

    return $extend_time;
}

/**
 * 时间戳转日期
 * @param $time
 * @return false|string
 */
function timeToDate($time = 0)
{
    if (!$time)
        $time = GetStartTimeStamp();

    return date('Y-m-d H:i:s', $time);
}

/**
 * 获取月的开始与结束时间戳
 * @param int $time
 * @return array
 */
function GetMonthStartEndTime($time = 0) {
    if (!$time)
        $time = GetNowTimeStamp();

    $stime = mktime(0, 0, 0, date('m', $time), 1, date('Y', $time));
    $etime = mktime(23, 59, 59, date('m', $time), date('t', $time), date('Y', $time));
    return [
        'stime' => $stime,
        'etime' => $etime
    ];
}

/**
 * 获取周一开始时间
 * @param int $time
 * @return false|float|int
 */
function GetWeekStart($time = 0)
{
    if (!$time)
        $time = GetNowTimeStamp();

    return strtotime(date('Ymd',$time)) - (date('N',$time) - 1) * 86400;
}


