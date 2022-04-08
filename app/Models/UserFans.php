<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserFans extends Model
{
    use HasFactory;

    protected $table = 'user_fans';
    protected $primaryKey = 'id';
//    public $timestamps = false;

    /**
     * 用户粉丝表 与 用户表一对一关联（本人）
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * 用户粉丝表 与 用户表一对一关联（粉丝）
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user_fans()
    {
        return $this->hasOne(User::class, 'id', 'fans_id');
    }

    /**
     * 用户粉丝表 与 作品表一对多关联（粉丝）
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fans_works()
    {
        return $this->hasMany(Works::class, 'user_id', 'fans_id');
    }

}
