<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the department the user belongs to
     */

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the channels the user is a member of
     */

    public function channels()
    {
        return $this->belongsToMany(Channel::class)
            ->withPivot('role', 'last_read_at')
            ->withTimestamps();
    }

    /**
     * Get all messages posted bt the user
     */

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
