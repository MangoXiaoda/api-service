<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class NoticeLog extends Model
{
    use HasFactory;

    protected $table = 'notice_logs';
    protected $primaryKey = 'id';
//    public $timestamps = false;

}
