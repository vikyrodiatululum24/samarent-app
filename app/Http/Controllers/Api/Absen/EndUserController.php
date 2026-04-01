<?php

namespace App\Http\Controllers\Api\Absen;

use App\Models\EndUser;
use App\Models\Project;
use App\Models\Unit;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class EndUserController extends Controller
{
    public function project()
    {
        $projects = Project::select('id', 'name')->get();
        return response()->json($projects);
    }

    public function getEndUsers()
    {
        $driver = Driver::with('project')->where('user_id', auth()->id())->first();
        Log::info('Mendapatkan end users untuk driver ID: ' . $driver->id . ' dengan penempatan: ' . $driver->project->name);
        $endUsers = EndUser::where('project_id', $driver->project->id)->get();
        return response()->json($endUsers);
    }

    public function unit()
    {
        $units = Unit::select('id', 'nopol', 'type')->get();
        return response()->json($units);
    }
}
