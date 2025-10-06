<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PushWaService;
use Illuminate\Support\Facades\Http;

class PushwaController extends Controller
{
    protected $pushwa;

    public function __construct(PushWaService $pushwa)
    {
        $this->pushwa = $pushwa;
    }

    public function sendText(Request $request)
    {
        $request->validate([
            'target'  => 'required|string',
            'message' => 'required|string',
        ]);

        try {
            $response = $this->pushwa->sendMessage(
                $request->target,
                'text',
                $request->message,
            );
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function sendImage(Request $request)
    {
        $request->validate([
            'target'  => 'required|string',
            'message' => 'sometimes|string',
            'url'     => 'required|url',
        ]);

        try {
            $response = $this->pushwa->sendMessage(
                $request->target,
                'image',
                $request->message ?? '',
                ['url' => $request->url]
            );
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // public function testSend()
    // {
    //     $response = Http::asJson()->post('https://dash.pushwa.com/api/kirimPesan', [
    //         'token'   => env('PUSHWA_TOKEN'), // sama persis dengan Postman
    //         'target'  => '6285714241420',
    //         'type'    => 'text',
    //         'delay'   => '1',
    //         'message' => 'Hello World dari Laravel!',
    //     ]);

    //     return $response->json();
    // }
}
