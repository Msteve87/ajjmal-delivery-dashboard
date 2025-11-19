<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SubOrder extends Model
{
    protected $guarded = [];

    protected $casts = [
        'products' => 'array',
    ];

    protected static function booted()
    {
        static::updating(function ($subOrder) {
            if ($subOrder->isDirty('sub_order_status_id') && $subOrder->sub_order_status_id == 5) {
                $subOrder->delivered_at = now();
            }
        });
    }

    protected function isPickedUp(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->picked_up_by !== null
        );
    }

    public function scopeIsPickedUp($query)
    {
        return $query->whereNotNull('picked_up_by');
    }

    public function scopeDelivered($query)
    {
        return $query->where('sub_order_status_id', 5)
            ->where('sub_order_status_id', 4);
    }

    public function scopeDeliveredToday($query)
    {
        return $query->whereDate('delivered_at', today());
    }

    public function scopeUnsettled($query)
    {
        return $query->whereNull('settlement_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function pickedUpBy()
    {
        return $this->belongsTo(Driver::class, 'picked_up_by');
    }

    public function subOrderStatus()
    {
        return $this->belongsTo(SubOrderStatus::class);
    }
}
