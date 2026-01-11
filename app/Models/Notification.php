<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    // public $incrementing = false; // karena UUID
    // protected $keyType = 'string';
    // protected $table = 'notifications';

    // protected $fillable = ['id', 'type', 'notifiable_id', 'notifiable_type', 'data', 'read_at'];

    // protected $casts = [
    //     'data' => 'array',
    //     'read_at' => 'datetime',
    // ];

    // public function notifiable(): MorphTo
    // {
    //     return $this->morphTo();
    // }

    // public function scopeUnread($query)
    // {
    //     return $query->whereNull('read_at');
    // }
}
