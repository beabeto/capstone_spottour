<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodMenu extends Model
{
    public static function categories(): array
    {
        return [
            'meal' => 'Meal',
            'appetizer' => 'Appetizer',
            'dessert' => 'Dessert',
            'drink' => 'Drink',
        ];
    }

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