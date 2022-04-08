<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderLog extends Model
{
    use HasFactory;

    protected $table = 'order_logs';
    protected $primaryKey = 'id';
//    public $timestamps = false;

}
