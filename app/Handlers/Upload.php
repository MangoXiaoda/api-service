<?php
/*
 * @Description: 上传工具类
 * @Author: lizhongda
 * @Date: 2022/3/17 下午2:54
 */

namespace App\Handlers;

use App\Enums\Http\ResponseCode;
use App\Traits\HttpResponseTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Upload
{
    use HttpResponseTrait;

    private $config = [
        'path'         => 'img',                         // 上传文件保存子目录，默认img,统一可选：img图片资源，tmp缓存文件, file文件
        'allowed_type' => ['png', 'jpg', 'gif', 'jpeg'], // 可接受的类型，默认图片
        'max_size'     => 20480,                         // 上传文件最大值默认20m，以kb计算
        'disk'         => 'uploads',                     // 默认保存disk，可生成对外访问链接，不需要再次对外开放的上传文件可设为local
        'isurl'        => 0,                             // 是否生成对外可访问链接，默认为否
        'is_compress'  => 0,                             // 是否压缩图片，目前只能压缩静态图片文件
        'cback'        => '',                            // 冗余参数，用于接口回调
    ];

    private $url;

    public function __construct($config)
    {
        $this->config = array_merge($this->config, $config);
    }

    public function upload(UploadedFile $file, $data = '')
    {
        if(!$this->validateDisk($this->config['disk'])){
            return response()->json($this->type(ResponseCode::PARAM_ERROR)->httpResponse('', '存储disk不存在，请检查参数'));
        }
        if(!$this->validatePath($this->config['path'])){
            return response()->json($this->type(ResponseCode::PARAM_ERROR)->httpResponse('', '存储path参数不存在或错误，请检查参数'));
        }
        if (!$file->isValid()) {
            return response()->json($this->type(ResponseCode::PARAM_ERROR)->httpResponse('', '上传失败'));
        }
        $file_size = $file->getSize();
        if (!$this->validateSize($file_size)) {
            return response()->json($this->type(ResponseCode::PARAM_ERROR)->httpResponse('', '上传文件过大'));
        }
        $ext = $file->getClientOriginalExtension();   // 扩展名

        if (!$this->validateTypes($ext)) {
            return response()->json($this->type(ResponseCode::PARAM_ERROR)->httpResponse('', '文件类型错误'));
        }
        $new_name = $this->reNameFile($ext);

        $saveinfo = $this->saveFile($file, $new_name);

        if ($saveinfo == false) {
            return response()->json($this->type(ResponseCode::PARAM_ERROR)->httpResponse('', '上传失败'));
        }
        if ($this->config['isurl'] && $this->config['disk'] == 'uploads') {
            $this->url = $this->getUrl($saveinfo);
        }

        if ($this->config['is_compress']) {
            $this->compressImgResource($new_name, $file_size, $ext);
        }

        $data = [
            'name' => $new_name,
            'path' => $saveinfo,
            'url'  => $this->url,
            'field_url'=>fileUrlToWebUrl($this->url),
            'cback'=> $this->config['cback']
        ];
        return response()->json($this->type(ResponseCode::REQUEST_SUCCESS)->httpResponse($data,'上传成功'));
    }

    public function delfile($path)
    {
        if(!$this->validateDisk($this->config['disk'])){
            return false;
            // return response()->json($this->type(ResponseCode::PARAM_ERROR)->httpResponse('', '存储disk不存在，请检查参数'));
        }
        //获取项目内文件相对地址
        if($this->config['disk'] == 'uploads'){
            $path = $this->handleStorageFile($path);
        }

        if ($path && Storage::disk($this->config['disk'])->exists($path) && Storage::disk($this->config['disk'])->delete($path)) {
            return true;
            // return response()->json($this->type(ResponseCode::REQUEST_SUCCESS)->httpResponse('','删除文件成功'));
        }
        return false;
        // return response()->json($this->type(ResponseCode::PARAM_ERROR)->httpResponse('', '删除文件失败'));
    }

    /**
     * 获取项目内文件相对地址
     * @param $path
     * @return bool
     */
    private function handleStorageFile($path)
    {

        return Str::after($path, '/storage/');
    }

    /**
     * 检查是否为允许上传文件类型
     * @param $ext 文件后缀
     * @return bool
     */
    protected function validateTypes($ext)
    {
        return in_array(strtolower($ext), $this->config['allowed_type']);
    }

    /**
     * 验证文件大小，$file->getSize()获取数值为Bytes
     * @param $size 文件大小。单位 Bytes
     * @return bool
     */
    protected function validateSize($size)
    {
        return $this->config['max_size'] * 1024 >= $size;
    }

    /**
     * 验证path是否存在
     * 目前验证不同环境下公有云bucket保存path
     * @param $path
     * @return string
     */
    protected function validatePath($path)
    {
        if (!$this->config['path']) {
            return false;
        }

        return true;
    }

    /**
     * 文件重命名，使用laravel自带hash重命名方法在部分字段下长度过长
     * @param UploadedFile $file
     * @return mixed
     */
    protected function reNameFile($ext)
    {
//        return $file->hashName();
        return Str::random(15).'.'.$ext;

    }

    /**
     * 保存文件到对应disk，对应文件夹为日期/pathname
     * @param UploadedFile $file
     * @param string $filename
     * @return mixed
     */
    protected function saveFile(UploadedFile $file, string $filename)
    {
        return $file->storeAs($this->config['path'] . '/' . date('Ymd'),$filename,$this->config['disk']);
    }

    /**
     * 获取对外访问链接
     * @param $fileinfo
     * @return string
     */
    protected function getUrl($fileinfo)
    {
        return Storage::url($fileinfo);
    }

    protected function validateDisk($disk)
    {
        return isset($this->getDiskList()[$disk]);
    }

    protected function getDiskList()
    {
        return config('filesystems.disks');
    }

    protected function compressImgResource($new_name, $size, $ext)
    {
        // 只压缩图片类资源，gif图及其它资源暂不支持压缩
        if (!in_array(strtolower($ext), ['png','jpg','jpeg']))
            return false;

        $percent = 0;
        switch (true) {
            case ($size > 1024 * 1024): // 大于1M 按 0.5压缩
                $percent = 0.5;
                break;
            case ($size >= 500 * 1024 && $size < 1024 * 1024): // 大于500k 小于 1M 按 0.3压缩
                $percent = 0.3;
                break;
            default:
                return;
        }

        $imgcom = new ImgCompress(fileUrlToWebUrl($this->url), $percent);
        $imgcom->compressImg($new_name);

        $destination = 'storage/'.$this->config['path'] . '/' . date('Ymd') .'/'. $new_name;
        if(copy($new_name, $destination))
            unlink($new_name);
    }

}
