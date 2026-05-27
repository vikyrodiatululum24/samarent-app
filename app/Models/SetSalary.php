<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Branch;
use App\Models\Division;

class SetSalary extends Model
{
    protected $fillable = [
        'project_id',
        'branch_id',
        'division_id',
        'name',
        'policy_type', // 'pilihan : flat, goverment, custom',
        // 'workdays',
        // 'workhours',
        'rules',
        'is_active',
        'effective_date',
        'expired_date',
        // 'amount',
        // 'overtime1',
        // 'overtime2',
        // 'overtime3',
        // 'overtime4',
        // 'transport',
    ];

    protected $casts = [
        // 'workdays' => 'array',
        'rules' => 'array',
        'is_active' => 'boolean',
        'effective_date' => 'date',
        'expired_date' => 'date',
        // 'workhours' => 'integer',
        // 'amount' => 'decimal:2',
        // 'overtime1' => 'decimal:2',
        // 'overtime2' => 'decimal:2',
        // 'overtime3' => 'decimal:2',
        // 'overtime4' => 'decimal:2',
        // 'transport' => 'decimal:2',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

}
