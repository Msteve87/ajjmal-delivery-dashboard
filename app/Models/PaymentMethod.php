<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PaymentMethod extends Model
{
    protected function icon(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => asset($value),
        );
    }
}
