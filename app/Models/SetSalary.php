<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetSalary extends Model
{
    protected $fillable = [
        'project_id',
        'amount',
        'transport',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
