<?php
namespace App\Http\Controllers\Api\V1\Driver;

use App\Models\Driver;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\RateLimiter;
use App\Http\Requests\Api\V1\ResetPasswordRequest;
use App\Http\Requests\Api\V1\ForgotPasswordRequest;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AuthController extends Controller
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected \App\Services\OtpService $otpService,
        protected \App\Services\DeviceTokenService $deviceTokenService
    ) {
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        if (RateLimiter::tooManyAttempts("driver-identifier {$request->identifier}", $perMinute = 10)) {
            return Response::json([
                'error' => 'Too many login attempts. Please try again after a minute.',
            ], 429);
        }

        RateLimiter::increment("driver-identifier {$request->identifier}", $decaySeconds = 86400);

        $driver = Driver::where('is_active', true)
            ->where('phone', $request->identifier)->first();

        if ($driver && Hash::check($request->password, $driver->password)) {

            $driver->setRememberToken(Str::random(60));

            if ($request->has('device_token')) {
                $this->deviceTokenService->storeDeviceToken($driver->id, $request->device_token);
            }

            RateLimiter::clear("driver-identifier {$request->identifier}");

            return Response::json(
                [
                    'data' => $driver,
                    'accessToken' => $driver->createToken("{$driver->phone}{$driver->id}")->plainTextToken,
                ]
            );
        }

        return Response::json([
            "error" => [
                "code" => 401,
                "message" => "Unauthorised",
                "errors" => ["message" => "Unauthorised"],
            ]
        ], 401);
    }


    /**
     * Send reset password OTP to the driver's phone number.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendForgotPasswordOtp(ForgotPasswordRequest $request)
    {
        if (RateLimiter::tooManyAttempts('driver-phone' . $request->phone, $perMinute = 5)) {
            return Response::json([
                'error' => 'Too many login attempts. Please try again after a minute.',
            ], 429);
        }

        RateLimiter::increment('driver-phone' . $request->phone, $decaySeconds = 900);

        try {
            $this->otpService->sendSmsOtp($request->phone, "otp_driver_{$request->phone}");

            RateLimiter::clear('driver-phone' . $request->phone);

            return Response::json([
                'message' => 'Your OTP has been sent',
            ]);

        } catch (BadRequestException $e) {
            return Response::json([
                "error" => [
                    "code" => $e->getCode(),
                    "message" => $e->getMessage(),
                    "errors" => $e->getTrace()
                ]
            ], $e->getCode());

        } catch (\Exception $e) {
            return Response::json([
                "error" => [
                    "code" => 500,
                    "message" => $e->getMessage(),
                    "errors" => $e->getTrace()
                ]
            ], 500);
        }
    }

    /**
     * Confirms the OTP for password reset.
     *
     * @param \Illuminate\Http\Request $request The request containing the OTP and reset token.
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmResetOtp(Request $request)
    {
        try {
            Pipeline::send($request)
                ->through([
                    \App\Pipes\Driver\ConfirmDriverResetOtp::class,
                ])
                ->thenReturn();

            return Response::json([
                'message' => 'OTP confirmed',
                'resetToken' => app('resetToken'),
            ]);
        } catch (\Exception $e) {
            return Response::json([
                "error" => [
                    "code" => 500,
                    "message" => $e->getMessage(),
                    "errors" => $e->getTrace()
                ]
            ], 500);
        }
    }

    /**
     * Resets Driver password.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            Pipeline::send($request)
                ->through([
                    \App\Pipes\Driver\ResetDriverPassword::class,
                ])
                ->thenReturn();

            return Response::json([
                'message' => 'Password reset successful',
            ], 201);

        } catch (BadRequestException $e) {
            return Response::notFound([$e->getMessage()]);
        } catch (\Exception $e) {
            return Response::json([
                "error" => [
                    "code" => 500,
                    "message" => $e->getMessage(),
                    "errors" => $e->getTrace()
                ]
            ], 500);
        }
    }
}
