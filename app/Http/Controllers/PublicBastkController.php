<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicBastkController extends Controller
{
    public function createBastk()
    {
        return view('public.bastk.create');
    }

    public function storeBastk(Request $request)
    {
        // Logic for storing BASTK data
    }
}
