<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    protected $fillable = ['name', 'slug', 'name_ar', 'description'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
