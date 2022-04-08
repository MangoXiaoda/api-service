<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderGoods extends Model
{
    use HasFactory;

    use SoftDeletes; // 启用软删除

    protected $table = 'order_goods';
    protected $primaryKey = 'id';
//    public $timestamps = false;
}
