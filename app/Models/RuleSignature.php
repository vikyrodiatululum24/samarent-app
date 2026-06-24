<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuleSignature extends Model
{
    protected $fillable = [
        'group_signature_id',
        'rules',
        'urutan'
    ];

    public function groupSignatures()
    {
        return $this->belongsTo(GroupSignature::class);
    }

    public function signatures()
    {
        return $this->hasMany(Signature::class);
    }
}
