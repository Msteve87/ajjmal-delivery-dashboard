<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOtpSmsJob implements ShouldQueue
{
    use Queueable;

    protected $recipient;
    protected $otp;
    /**
     * Create a new job instance.
     */
    public function __construct($recipient, $otp, \Closure $storeOtp)
    {
        $this->recipient = $recipient;
        $this->otp = $otp;
        $storeOtp();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $response = Http::withToken(env('ISEND_API_TOKEN'))
                ->post(
                    env('ISEND_URL', ''),
                    [
                        'recipient' => $this->recipient,
                        'sender_id' => env('SENDER_ID'),
                        'type' => 'unicode',
                        'message' => $this->otp
                    ]
                );

            if ($response->getStatusCode() !== 200) {
                $responseBody = $response->getBody()->getContents();
                $decodedBody = json_decode($responseBody, true);
                Log::error('HTTP Request failed', [
                    'status_code' => $response->getStatusCode(),
                    'response_body' => $responseBody,
                    'decoded_body' => $decodedBody,
                ]);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
