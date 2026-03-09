<?php

namespace App\Http\Controllers\Api\Absen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUser(Request $request)
    {
        $user = $request->user()->load('driver');
        $user = new \App\Http\Resources\UserResource($user);
        return response()->json($user);
    }
}
