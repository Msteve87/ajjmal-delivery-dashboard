<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AjjmalMarketApiService
{
    public $ajjmalBaseUrl;
    public $ajjmalStandaloneUrl;

    public function __construct()
    {
        $this->ajjmalBaseUrl = config('services.api_url');
        $this->ajjmalStandaloneUrl = config('services.ajjmal.standalone_url') . '/delivery';
    }

    public function getNewOrders()
    {
        $params = [
            'state' => 'Processing in Progress',
            'sort_by' => 'total_paid',
            'order' => 'desc',
        ];

        $response = Http::get($this->ajjmalStandaloneUrl, $params);


        if ($response->json()['success']) {
            return $response->json()['data'];
        } else {
            throw new \Exception('Error while fetching JM orders by status');
        }
    }

    public function getAllJmOrders()
    {
        $params = [
            'sort_by' => 'total_paid',
            'order' => 'desc',
        ];

        $response = Http::get($this->ajjmalStandaloneUrl, $params);

        if ($response->json()['success']) {
            return $response->json()['data'];
        } else {
            throw new \Exception('Error while fetching JM orders by status');
        }
    }

    public function getSubOrders($reference)
    {
        $response = Http::get($this->ajjmalStandaloneUrl . "?reference={$reference}&order=desc");

        if (empty(json_decode($response, associative: true)['data'])) {
            throw new HttpException(404, 'Order not found');
        }

        $items = $response->json()['data'];

        return $items;
    }

    public function getJmOrderById($id)
    {
        $params = [
            'state' => 'Processing in Progress',
            'sort_by' => 'total_paid',
            'order' => 'desc',
        ];

        $response = Http::get($this->ajjmalStandaloneUrl, $params);

        if (empty(json_decode($response, associative: true)['data'])) {
            throw new HttpException(404, 'not found');
        }

        $item = collect($response->json()['data'])->firstWhere('id_order', $id);

        if ($item === null) {
            throw new HttpException(404, 'not found');
        }

        return $item;
    }

    public function getJmOrderStates()
    {
        $response = Http::get($this->ajjmalBaseUrl, ["route" => "states"]);

        if (empty(json_decode($response, associative: true)['data'])) {
            throw new HttpException(404, 'not found');
        }

        $items = $response->json()['data'];

        return $items;
    }
}
