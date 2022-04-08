<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoodsCategory extends Model
{
    use HasFactory;

    protected $table = 'goods_category';
    protected $primaryKey = 'id';
//    public $timestamps = false;
}
