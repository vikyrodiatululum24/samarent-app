<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Get the pengajuans associated with this project.
     */
    public function pengajuans()
    {
        return $this->hasMany(Pengajuan::class, 'project_id');
    }

    public function setSalary()
    {
        return $this->hasOne(SetSalary::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function divisions()
    {
        return $this->hasManyThrough(Division::class, Branch::class);
    }

    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    public function groupSignatures()
    {
        return $this->hasMany(GroupSignatures::class);
    }
}
