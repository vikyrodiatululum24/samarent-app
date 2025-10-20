<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetSalary extends Model
{
    protected $fillable = [
        'project_id',
        'amount',
        'overtime1',
        'overtime2',
        'overtime3',
        'overtime4',
        'transport',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

}
