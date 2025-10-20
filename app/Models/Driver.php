<?php
namespace App\Models;

use App\Models\DeviceToken;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Driver extends Authenticatable
{
    use HasApiTokens, Notifiable, LogsActivity;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('user')
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                default => "Driver {$eventName}",
            });
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function subOrders()
    {
        return $this->hasMany(SubOrder::class);
    }

    public function documents()
    {
        return $this->hasMany(DriverDocument::class, 'driver_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function routeNotificationForFcm()
    {
        return $this->deviceTokens()
            ->where('active', true)
            ->value('token');
    }
}
