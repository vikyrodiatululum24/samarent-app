<?php

namespace App\Http\Controllers\Vue;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VueController extends Controller
{
    public function getUser(Request $request)
    {
        return response()->json(['user' => $request->user()]);
    }
}
