<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    protected $casts = [
        'products' => 'json',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
