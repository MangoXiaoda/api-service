<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorksCommentLikes extends Model
{
    use HasFactory;

    protected $table = 'works_comment_likes';
    protected $primaryKey = 'id';
    // public $timestamps = false;

    /**
     * 作品评论点赞表 与 作品评论表 多对一关联
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function works_comment()
    {
        return $this->belongsTo(WorksComment::class, 'comment_id', 'id');
    }

}
