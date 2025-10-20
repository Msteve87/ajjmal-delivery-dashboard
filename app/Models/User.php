<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Event;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected static $logName = 'user';
    protected static $logFillable = true;

    /**
     * Configure which attributes and events to log.
     */
    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //         ->useLogName('user')
    //         ->logOnly(['name', 'email'])
    //         ->setDescriptionForEvent(fn(string $eventName) => "User has been {$eventName}");
    // }


    protected static function booted(): void
    {
        static::created(function ($user) {
            $activity = __('activitylogs.names.user_created', [], 'ar');

            if (auth()->check()) {
                activity($activity)
                    ->performedOn($user)
                    ->causedBy(auth()->user())
                    ->log("قام المستخدم " . auth()->user()->name . " بإضافة المستخدم {$user->name}");
            }
        });

        static::updated(function ($user) {
            if (!auth()->check() || auth()->id() !== $user->id) {
                return;
            }

            $changes = $user->getChanges();

            if (array_key_exists('password', $changes)) {
                $activity = __('activitylogs.names.password_updated', [], 'ar');
                $description = "قام المستخدم {$user->name} بتحديث كلمة المرور الخاصة به";
            } else {
                $activity = __('activitylogs.names.profile_updated', [], 'ar');
                $description = "قام المستخدم {$user->name} بتحديث ملفه الشخصي";
            }

            activity($activity)
                ->performedOn($user)
                ->causedBy($user)
                ->log($description);
        });

        static::deleted(function ($user) {
            if (auth()->check()) {
                $activity = __('activitylogs.names.user_deleted', [], 'ar');
                activity($activity)
                    ->performedOn($user)
                    ->causedBy(auth()->user())
                    ->log("قام المستخدم " . auth()->user()->name . " بحذف المستخدم {$user->name}");
            }
        });
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected string $guard_name = 'sanctum';

    protected function getDefaultGuardName(): string
    {
        return $this->guard_name;
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
