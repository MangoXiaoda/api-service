<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Works extends Model
{
    use HasFactory;
    use SoftDeletes; // 启用软删除

    protected $table = 'works';
    protected $primaryKey = 'id';
//    public $timestamps = false;


    protected $hidden = [
        'deleted_at'
    ];

    /**
     * 作品表 与 用户表 多对一关联
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * 作品表 与 话题表 多对一关联
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function topics_categories()
    {
        return $this->belongsTo(TopicsCategory::class, 'topics_category_id', 'id');
    }

    /**
     * 作品表 与 作品资源表 一对多关联
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function works_resource()
    {
        return $this->hasMany(WorksResource::class, 'works_id', 'id');
    }

    /**
     * 作品表 与 作品评论表 一对多关联
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function works_comment()
    {
        return $this->hasMany(WorksComment::class, 'works_id', 'id');
    }

    /**
     * 作品表 与 作品点赞表 一对多关联
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function works_likes()
    {
        return $this->hasMany(WorksLikes::class, 'works_id', 'id');
    }

    /**
     * 作品表 与 用户收藏表一对多关联
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user_collections()
    {
        return $this->hasMany(UserCollection::class, 'works_id', 'id');
    }

    /**
     * 作品表 user_id 与 粉丝表 一对多关联
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user_fans()
    {
        return $this->hasMany(UserFans::class, 'user_id', 'user_id');
    }

    /**
     * 访问器 拼接资源全地址
     */
    public function getCoverUrlAttribute()
    {
        return fileUrlToWebUrl($this->attributes['cover_url']);
    }


//    public function getImagesAttribute($images)
//    {
//        $images = explode(',', $images);
//
//        return $images;
//    }

}
