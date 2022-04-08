<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserPointsLog extends Model
{
    use HasFactory;

    protected $table = 'user_points_log';
    protected $primaryKey = 'id';
//    public $timestamps = false;

    /**
     * 用户积分日志表 与 用户表 多对一关联
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
