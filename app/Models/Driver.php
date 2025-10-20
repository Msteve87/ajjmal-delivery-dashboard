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
    use HasApiTokens, Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //         ->useLogName('user')
    //         ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
    //             default => "Driver {$eventName}",
    //         });
    // }

    protected static function booted(): void
    {
        static::created(function ($driver) {
            if (auth()->check()) {
                $activity = __('activitylogs.names.driver_created', [], 'ar');
                activity($activity)
                    ->performedOn($driver)
                    ->causedBy(auth()->user())
                    ->log("قام المستخدم " . auth()->user()->name . " بإضافة السائق {$driver->name}");
            }
        });

        static::updated(function ($driver) {
            if (!auth()->check()) {
                return;
            }

            $actor = auth()->user();
            $changes = $driver->getChanges();

            $isSelfUpdate = $driver->user_id === $actor->id;

            if (array_key_exists('password', $changes)) {
                $activity = __('activitylogs.names.driver_password_updated', [], 'ar');
                $description = $isSelfUpdate
                    ? "قام السائق {$driver->name} بتحديث كلمة المرور الخاصة به"
                    : "قام المستخدم {$actor->name} بتحديث كلمة مرور السائق {$driver->name}";
            } else {
                $activity = __('activitylogs.names.driver_profile_updated', [], 'ar');
                $description = $isSelfUpdate
                    ? "قام السائق {$driver->name} بتحديث ملفه الشخصي"
                    : "قام المستخدم {$actor->name} بتحديث ملف السائق {$driver->name}";
            }

            activity($activity)
                ->performedOn($driver)
                ->causedBy($actor)
                ->log($description);
        });

        static::deleted(function ($driver) {
            if (auth()->check()) {
                $activity = __('activitylogs.names.driver_deleted', [], 'ar');
                activity($activity)
                    ->performedOn($driver)
                    ->causedBy(auth()->user())
                    ->log("قام المستخدم " . auth()->user()->name . " بحذف السائق {$driver->name}");
            }
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
