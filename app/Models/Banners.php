<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Banners extends Model
{
    use HasFactory;

    protected $table = 'banners';
    protected $primaryKey = 'id';


    // 定义一个public方法访问图片或文件
//    public function getImage()
//    {
//        if (Str::contains($this->image, '//')) {
//            return $this->image;
//        }
//
//        return Storage::disk('admin')->url($this->image);
//    }

    /**
     * 访问器 拼接资源全地址
     */
    public function getImageAttribute()
    {
        return fileUrlToWebUrl($this->attributes['image']);
    }

}
