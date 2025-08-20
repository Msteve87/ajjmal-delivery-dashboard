<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AjjmalMarketApiService
{
    public $ajjmalBaseUrl;

    public function __construct()
    {
        $this->ajjmalBaseUrl = config('services.ajjmal.api_url');
    }

    public function getNewOrders()
    {
        $params = [
            'state' => 'Processing in Progress',
            'sort_by' => 'total_paid',
            'order' => 'desc',
        ];

        $response = Http::get($this->ajjmalBaseUrl, $params);

        if ($response->json()['success']) {
            return $response->json()['data'];
        } else {
            throw new \Exception('Error while fetching JM orders by status');
        }
    }

    public function getSubOrders($reference)
    {
        $response = Http::get(env('JM_API_URL') . "?reference={$reference}&order=desc");

        if (empty(json_decode($response, associative: true)['data'])) {
            throw new HttpException(404, 'Order not found');
        }

        $items = $response->json()['data'];

        return $items;
    }
}
