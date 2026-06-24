<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Signature extends Model
{
    protected $fillable = ['rule_signature_id', 'nama', 'jabatan', 'nip', 'ttd', 'urutan', 'is_active'];

    protected $casts = [
        'urutan' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {

        static::deleting(function (Signature $signature) {
            if (filled($signature->ttd) && Storage::disk('public')->exists($signature->ttd)) {
                Storage::disk('public')->delete($signature->ttd);
            }
        });
    }

    public function rule_signature()
    {
        return $this->belongsTo(RuleSignature::class);
    }
}
