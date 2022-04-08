<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderSub extends Model
{
    use HasFactory;
    use SoftDeletes; // 启用软删除

    protected $table = 'order_subs';
    protected $primaryKey = 'id';
//    public $timestamps = false;

}
