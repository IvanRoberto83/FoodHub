<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'restaurant_id', 'category_id', 'name', 'description',
        'price', 'image', 'stock', 'is_available',
    ];

    protected $casts = ['is_available' => 'boolean'];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}