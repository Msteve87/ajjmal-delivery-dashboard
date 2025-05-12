<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $guarded = [];

    protected $casts = [
        'products' => 'json',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }
}
