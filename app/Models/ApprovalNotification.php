<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalNotification extends Model
{
    protected $fillable = [
        'user_id',
        'role',
        'title',
        'message',
        'status',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];
}
