<?php

namespace App\Pipes\Driver;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ConfirmDriverResetOtp
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function handle($request, \Closure $next)
    {
        try {
            $key = "otp_driver_{$request->phone}";

            $storedOtpHash = Redis::get($key);

            if ($storedOtpHash === hash('sha256', $request->otp)) {
                $resetToken = Str::random(60);

                DB::table('driver_password_reset_tokens')
                    ->upsert([
                        'phone' => $request->phone,
                        'expires_at' => now()->addMinutes(30),
                        'token' => $resetToken,
                    ], 'phone');

                Redis::del($key);

                app()->instance('resetToken', $resetToken);

                return $next($request);
            }
            throw new BadRequestException();
        } catch (BadRequestException $e) {
            throw new BadRequestException('Invalid or expired OTP');
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
