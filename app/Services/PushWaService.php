<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PushWaService
{
    protected $baseUrl;
    protected $token;
    protected $timeout;

    public function __construct()
    {
        $this->baseUrl = config('pushwa.base_url');
        $this->token   = config('pushwa.token');
        $this->timeout = config('pushwa.timeout');
    }

    protected function post(string $endpoint, array $data)
    {
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');

        $payload = array_merge(['token' => $this->token], $data);

        $response = Http::timeout($this->timeout)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($url, $payload);

        if ($response->successful()) {
            return $response->json();
        }

        // bisa lempar exception agar konsumennya tahu ada error
        throw new \Exception("PushWa API Error: " . $response->body());
    }

    public function startDevice()
    {
        return $this->post('startDevice', []);
    }

    public function sendMessage(string $target, string $type, string $message, array $extra = [])
    {
        $data = [
            'target'  => $target,
            'type'    => $type,
            'delay'   => '1',
            'message' => $message,
        ];

        $data = array_merge($data, $extra);

        return $this->post('kirimPesan', $data);
    }

    public function statusMessage(string $idMsg)
    {
        return $this->post('statusMessage', [
            'idMsg' => $idMsg,
        ]);
    }

    // kamu bisa tambahkan wrapper lain sesuai fitur API PushWa lainnya
}
