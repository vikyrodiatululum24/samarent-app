<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReimbursementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'km_awal' => $this->km_awal,
            'foto_odometer_awal' => $this->foto_odometer_awal,
            'foto_odometer_awal_url' => $this->foto_odometer_awal_url,
            'km_akhir' => $this->km_akhir,
            'foto_odometer_akhir' => $this->foto_odometer_akhir,
            'foto_odometer_akhir_url' => $this->foto_odometer_akhir_url,
            'nota' => $this->nota,
            'nota_url' => $this->nota_url,
            'type' => $this->type,
            'tujuan_perjalanan' => $this->tujuan_perjalanan,
            'keterangan' => $this->keterangan,
            'dana_masuk' => $this->dana_masuk,
            'dana_keluar' => $this->dana_keluar,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
        ];
    }
}
