<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodMenu extends Model
{
    protected $fillable = [

        'food_id',

        'name',

        'description',

        'price',

        'category',

        'image'

    ];

    public function food()
    {
        return $this->belongsTo(
            Food::class
        );
    }
}