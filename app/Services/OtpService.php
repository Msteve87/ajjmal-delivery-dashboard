<?php

namespace App\Services;

use App\Jobs\SendOtpSmsJob;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;

class OtpService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    /**
     * Generate an OTP (alphanumeric or numeric).
     *
     * @param int $length Length of the OTP.
     * @param bool $alphanumeric Whether the OTP should be alphanumeric.
     * @return string Generated OTP.
     */
    public function generateOtp($length = 6, $alphanumeric = false)
    {
        if ($alphanumeric) {
            return $this->generateAlphanumericOtp($length);
        }
        return $this->generateNumericOtp($length);
    }

    /**
     * Generate an alphanumeric OTP.
     *
     * @param int $length Length of the OTP.
     * @return string Alphanumeric OTP.
     */
    protected function generateAlphanumericOtp($length)
    {
        return Str::random($length);
    }

    /**
     * Generate a numeric OTP.
     *
     * @param int $length Length of the OTP.
     * @return string Numeric OTP.
     */
    protected function generateNumericOtp($length)
    {
        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;
        return str_pad(random_int($min, $max), $length, '0', STR_PAD_LEFT);
    }

    /**
     * Get OTP expiry time in seconds.
     *
     * @return int The OTP expiry time in seconds.
     */
    public function getOtpExpiryTime()
    {
        return env('OTP_EXPIRE_IN_SECONDS', 300);
    }

    /**
     * Store OTP in Redis with an expiration time
     *
     * @param string $phone
     * @param string $otp
     * @param int $expiryInSeconds
     * @return void
     */
    public function storeOtpInRedis($key, $otp, $expiryInSeconds)
    {
        try {
            Redis::setex($key, $expiryInSeconds, hash('sha256', $otp));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Send OTP via SMS and store it in Redis.
     *
     * @param string $phone
     * @param string $key
     * @param ?string $otp
     * @return void
     */
    public function sendSmsOtp(string $phone, string $key, ?string $otp = null)
    {
        try {
            $otp = $otp ?: $this->generateOtp();

            SendOtpSmsJob::dispatchAfterResponse(
                $phone,
                $otp,
                fn() => $this->storeOtpInRedis($key, $otp, env('OTP_EXPIRE_IN_SECONDS', 300))
            );
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
