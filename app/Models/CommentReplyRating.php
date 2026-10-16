<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CommentReplyRating extends Model { protected $fillable = ['reply_id', 'user_id', 'rating']; }
