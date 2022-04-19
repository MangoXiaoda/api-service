<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoodsImg extends Model
{
    use HasFactory;

    protected $table = 'goods_images';
    protected $primaryKey = 'id';
//    public $timestamps = false;

    /**
     * 访问器 拼接资源全地址
     */
    public function getImageAttribute()
    {
        return fileUrlToWebUrl($this->attributes['image']);
    }
}
