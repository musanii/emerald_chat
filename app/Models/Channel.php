<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name',
        'slug',
        'type',
        'description'
    ];

    /**
     * Get the dept that owns the channel
     * 
     */

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get all users enrolled in this channel
     */

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role', 'last_read_at')
            ->withTimestamps();
    }

    /**
     * Get all messages sent inside this channel.
     */

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
