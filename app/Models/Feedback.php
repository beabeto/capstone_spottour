<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $fillable = [
        'user_id',
        'subject',
        'message',
        'reply',
        'replied_by',
        'replied_at',
        'status',
    ];

    protected function casts(): array
    {
        return ['replied_at' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }
}