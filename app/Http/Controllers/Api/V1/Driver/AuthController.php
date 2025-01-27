<?php
namespace App\Http\Controllers\Api\V1\Driver;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        if (RateLimiter::tooManyAttempts(`driver-identifier {$request->identifier}`, $perMinute = 10)) {
            return Response::json([
                'error' => 'Too many login attempts. Please try again after a minute.',
            ], 429);
        }

        RateLimiter::increment(`driver-identifier {$request->identifier}`, $decaySeconds = 86400);

        $driver = Driver::where('is_active', true)
            ->where('phone', $request->identifier)->first();

        if ($driver && Hash::check($request->password, $driver->password)) {

            $driver->setRememberToken(Str::random(60));

            RateLimiter::clear(`driver-identifier {$request->identifier}`);

            return Response::json(
                [
                    'data'        => $driver,
                    'accessToken' => $driver->createToken("{$driver->phone}{$driver->id}")->plainTextToken,
                ]
            );
        }

        return Response::json(["error" => [
            "apiVersion" => "1.0",
            "code"       => 401,
            "message"    => "Unauthorised",
            "errors"     => ["message" => "Unauthorised"],
        ]], 401);
    }
}
