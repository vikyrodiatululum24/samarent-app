<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GroupSignature extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'branch_id',
        'nama',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function rule_signatures()
    {
        return $this->hasMany(RuleSignature::class);
    }
}
