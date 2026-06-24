<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'project_id',
        'name',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function divisions()
    {
        return $this->hasMany(Division::class);
    }

    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    public function setSalaries()
    {
        return $this->hasMany(SetSalary::class);
    }

    public function groupSignatures()
    {
        return $this->hasMany(GroupSignatures::class);
    }
}
