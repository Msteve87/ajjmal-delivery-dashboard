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

    protected static function booted(): void
    {
        static::updated(function ($localRate) {
            if (auth()->check()) {
                $activity = __('activitylogs.names.rate_updated', [], 'ar');
                $areas    = implode(', ', $localRate->areas);
                $userName = auth()->user()->name;

                if ($localRate->wasChanged('home_rate')) {
                    activity($activity)
                        ->performedOn($localRate)
                        ->causedBy(auth()->user())
                        ->log("قام المستخدم {$userName} بتحديث سعر التوصيل المباشر للمناطق ({$areas}) إلى: " . $localRate->home_rate . " LYD");
                }

                if ($localRate->wasChanged('locker_rate')) {
                    activity($activity)
                        ->performedOn($localRate)
                        ->causedBy(auth()->user())
                        ->log("قام المستخدم {$userName} بتحديث سعر التوصيل للخزينة للمناطق ({$areas}) إلى: " . $localRate->locker_rate . " LYD");
                }

                if ($localRate->wasChanged('areas') && ! $localRate->wasChanged('home_rate') && ! $localRate->wasChanged('locker_rate')) {
                    activity($activity)
                        ->performedOn($localRate)
                        ->causedBy(auth()->user())
                        ->log("قام المستخدم {$userName} بتعديل قائمة المناطق إلى: " . $areas);
                }
            }
        });
    }
}
