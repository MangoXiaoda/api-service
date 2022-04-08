<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class SystemSettings extends Model
{
    use HasFactory;

    protected $table = 'system_settings';
    protected $primaryKey = 'id';
//    public $timestamps = false;

}
