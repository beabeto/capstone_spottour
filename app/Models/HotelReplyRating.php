<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class HotelReplyRating extends Model { protected $fillable = ['reply_id', 'user_id', 'rating']; }
