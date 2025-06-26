<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    protected $fillable = [
        'session_id',
        'ip',
        'device_hash',
        'origin',
        'paths',
        'file_path'
    ];

    protected $casts = [
        'paths' => 'array'
    ];
}
