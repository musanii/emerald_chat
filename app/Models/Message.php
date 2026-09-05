<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'channel_id',
        'user_id',
        'parent_id',
        'body'
    ];


    /**
     * Get the channel where this message was posted.
     * 
     */

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * Get the parent message if this is a thread reply
     */
    public function parent()
    {
        return $this->belongsTo(Message::class,'parent_id');
    }

    /**
     * Get all thread replies to this message
     */

    public function replies()
    {
        return $this->hasMany(Message::class,'parent_id');
    }

    /**
     * Get all attachments associated with this message.
     */

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
