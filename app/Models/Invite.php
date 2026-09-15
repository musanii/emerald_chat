<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'roken',
        'department_id',
        'expires_at',
        'accepted_at',
    ];

    protected $casts = [
        'expires_at'=>'datetime',
        'accepted_at'=>'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function isValid()
    {
        return is_null($this->accepted_at) && $this->expires_at->isFuture();
    }
}
