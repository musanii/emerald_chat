<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    /**
     * Get all channels belonging to this dept
     */

    public function channels()
    {
        return $this->hasMany(Channel::class);
    }

    /**
     * Get all users assigned to this dept
     */

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
