<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserCollection extends Model
{
    use HasFactory;

    protected $table = 'user_collections';
    protected $primaryKey = 'id';
    // public $timestamps = false;

    /**
     * 用户收藏表 与 用户表一对一关联
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(UserCollection::class, 'user_id', 'id');
    }

    /**
     * 用户收藏表 与 作品表 一对一关联
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function works()
    {
        return $this->hasOne(Works::class, 'id', 'works_id');
    }

}
