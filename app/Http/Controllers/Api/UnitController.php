<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Get all units untuk sync
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllUnits()
    {
        try {
            $units = Unit::select('id', 'merk', 'jenis', 'type', 'nopol')->get();

            return response()->json([
                'success' => true,
                'message' => 'Data unit berhasil diambil',
                'total' => $units->count(),
                'data' => $units
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data unit',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
