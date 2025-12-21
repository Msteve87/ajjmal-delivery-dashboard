<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocalRate extends Model
{
    protected $fillable = ['areas', 'rate'];

    protected $casts = [
        'areas' => 'array',
        'rate'  => 'float',
    ];
}
