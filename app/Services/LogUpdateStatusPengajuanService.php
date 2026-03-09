<?php

namespace App\Services;

use App\Models\LogUpdateStatusPengajuan;

class LogUpdateStatusPengajuanService
{
    public function logStatusChange($userId, $pengajuanId, $statusLama, $statusBaru)
    {
        LogUpdateStatusPengajuan::create([
            'user_id' => $userId,
            'pengajuan_id' => $pengajuanId,
            'status_lama' => $statusLama,
            'status_baru' => $statusBaru,
        ]);
    }
}
