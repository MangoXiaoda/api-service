<?php
/*
 * @Description:http返回数据标准trait
 * @Version:
 * @Author: LiZhongDa
 * @Date: 2022-03-11 16:40:33
 * @LastEditors: LiZhongDa
 * @LastEditTime: 2022-03-11 16:40:33
 */

namespace App\Traits;

trait HttpResponseTrait
{
    /**
     * @var string 返回code类型
     */
    public $responseCodeType;

    /**
     * 返回类型
     * @param $responseCodeType string code类型
     * @return $this|object
     */
    public function type($responseCodeType): object
    {
        $this->responseCodeType = $responseCodeType;
        return $this;
    }

    /**
     * 返回数据体
     * @param null $data 返回数据
     * @param null $msg 提示信息
     * @return array
     */
    public function httpResponse($data = null, $msg = null): array
    {
        return [
            'data' => $data,
            'code' => $this->responseCodeType,
            'msg' => $msg ?? $this->responseMessage($this->responseCodeType)
        ];
    }

    /**
     * 返回信息提示
     * @param $responseCodeType string code类型
     * @return string
     */
    private function responseMessage($responseCodeType): string
    {
        // 请在config目录下response.php文件进行查找配置
        $responseMessage = config('response.message');
        return $responseMessage[$responseCodeType] ?? '未能正常读取Response Message,请检查config目录下的response.php文件!';
    }
}
