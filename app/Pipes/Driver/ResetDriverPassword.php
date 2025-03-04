<?php

namespace App\Pipes\Driver;
use App\Models\Driver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ResetDriverPassword
{
    /**
     * Create a new class instance.
     */
    public function __construct(
    ) {
    }

    public function handle($request, \Closure $next)
    {
        try {
            $driverPasswordResetsToken = DB::table('driver_password_reset_tokens')
                ->where('token', $request->reset_token)
                ->first();

            if (!$driverPasswordResetsToken || $driverPasswordResetsToken->expires_at < now()) {
                throw new BadRequestException('This password reset token is invalid.');
            }

            $driver = Driver::where('phone', $driverPasswordResetsToken->phone)->firstOrFail();

            $driver->update(['password' => Hash::make($request->password)]);

            return $next($request);
        } catch (BadRequestException $e) {
            throw new BadRequestException('This password reset token is invalid', 400);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Driver not found with the provided phone number.', 404);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
