<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserMessageStatus extends Model
{
    use HasFactory;

    protected $table = 'user_message_status';
    protected $primaryKey = 'id';
//    public $timestamps = false;


    /**
     * 用户消息状态表 多对一关联 用户消息表
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user_message()
    {
        return $this->belongsTo(UserMessage::class, 'messages_id', 'id');
    }

}
