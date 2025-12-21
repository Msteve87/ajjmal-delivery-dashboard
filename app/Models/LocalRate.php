<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocalRate extends Model
{
    protected $fillable = ['areas', 'home_rate', 'locker_rate'];

    protected $casts = [
        'areas'       => 'array',
        'home_rate'   => 'float',
        'locker_rate' => 'float',
    ];
}
