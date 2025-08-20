<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubOrder extends Model
{
    protected $guarded = [];

    protected $casts = [
        'products' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
