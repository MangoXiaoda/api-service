<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserMessage extends Model
{
    use HasFactory;

    protected $table = 'user_messages';
    protected $primaryKey = 'id';
//    public $timestamps = false;

    protected $hidden = ['del_time'];

    /**
     * 消息表 与 消息状态表 一对多关联
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user_message_status()
    {
        return $this->hasMany(UserMessageStatus::class, 'messages_id', 'id');
    }

}
