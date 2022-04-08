<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class TopicsCategory extends Model
{
    use HasFactory;

    protected $table = 'topics_categories';
    protected $primaryKey = 'id';
    // public $timestamps = false;

    /**
     * 话题表 与 作品表 一对多关联
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function works()
    {
        return $this->hasMany(Works::class,'topics_category_id', 'id');
    }

}
