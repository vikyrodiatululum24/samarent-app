<?php

namespace App\Http\Controllers\Api\Absen;

use App\Models\EndUser;
use App\Models\Project;
use App\Models\Unit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EndUserController extends Controller
{
    public function project()
    {
        $projects = Project::select('id', 'name')->get();
        return response()->json($projects);
    }

    public function getEndUsers($id)
    {
        $endUsers = EndUser::where('project_id', $id)->get();
        return response()->json($endUsers);
    }

    public function unit()
    {
        $units = Unit::select('id', 'nopol', 'type')->get();
        return response()->json($units);
    }
}
