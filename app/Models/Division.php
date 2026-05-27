<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $fillable = [
        'project_id',
        'branch_id',
        'name',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    public function setSalaries()
    {
        return $this->hasMany(SetSalary::class);
    }
}
