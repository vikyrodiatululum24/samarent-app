<?php

namespace App\Http\Controllers;

use App\Helpers\HolidayDates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HolidayController extends Controller
{
    public function index()
    {
        $year = now()->year;
        $holidays = HolidayDates::getHolidayDates($year);
        $holidayDates = $holidays->toArray();

        return response()->json($holidayDates);
    }
}
