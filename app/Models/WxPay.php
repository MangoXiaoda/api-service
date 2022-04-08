<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class WxPay extends Model
{
    use HasFactory;
    use SoftDeletes; // 启用软删除

    protected $table = 'wx_pays';
    protected $primaryKey = 'id';
//    public $timestamps = false;



}
