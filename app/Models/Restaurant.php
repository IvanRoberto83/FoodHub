<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = [
        'user_id', 'name', 'slug', 'description',
        'address', 'phone', 'image', 'is_open',
    ];

    protected $casts = ['is_open' => 'boolean'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}