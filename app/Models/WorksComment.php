<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorksComment extends Model
{
    use HasFactory;

    protected $table = 'works_comments';
    protected $primaryKey = 'id';
    // public $timestamps = false;

    protected $hidden = [
        'deleted_at'
    ];

    /**
     * 作品评论表 与 作品表 多对一关联
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function works()
    {
        return $this->belongsTo(Works::class, 'works_id', 'id');
    }

    /**
     * 作品评论表 与 用户表 一对一关联
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * 作品评论表 与 作品评论点赞表 一对多 关联
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function works_comment_likes()
    {
        return $this->hasMany(WorksCommentLikes::class, 'comment_id', 'id');
    }

    /**
     * 从属关联本身评论模型
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(WorksComment::class, 'comment_id');
    }

    /**
     * 一对多 关联本身模型
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function child()
    {
        return $this->hasMany(WorksComment::class, 'comment_id');
    }

    /**
     * 无限级关联子模型数据
     * @return HasMany
     */
    public function children()
    {
        return $this->child()->with(['children']);
    }

}
