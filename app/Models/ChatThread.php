<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatThread extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'assignee_role',
        'assignee_id',
        'listing_type',
        'listing_id',
        'listing_name',
        'traveller_unread',
        'staff_unread',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return ['last_message_at' => 'datetime'];
    }

    public function traveller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }
}
