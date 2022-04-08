<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorksResource extends Model
{
    use HasFactory;

    protected $table = 'works_resource';
    protected $primaryKey = 'id';
    // public $timestamps = false;

    /**
     * 访问器 拼接资源全地址
     */
    public function getRUrlAttribute()
    {
        return fileUrlToWebUrl($this->attributes['r_url']);
    }

}
