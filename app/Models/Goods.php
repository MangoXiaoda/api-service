<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Goods extends Model
{
    use HasFactory;
    use SoftDeletes; // 启用软删除

    protected $table = 'goods';
    protected $primaryKey = 'id';
//    public $timestamps = false;

    protected $hidden = ['deleted_at'];

    /**
     * 商品表 与 用户表 多对一关联
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * 商品表 与 商品分类表 多对一关联
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function goods_category()
    {
        return $this->belongsTo(GoodsCategory::class, 'category_id', 'id');
    }

    /**
     * 商品表 与 商品图片表 一对多关联
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goods_images()
    {
        return $this->hasMany(GoodsImg::class, 'goods_id', 'id');
    }

    /**
     * 访问器 拼接资源全地址
     */
    public function getThumbAttribute()
    {
        return fileUrlToWebUrl($this->attributes['thumb']);
    }

}
