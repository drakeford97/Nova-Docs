<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    /**
     * Get the user the profile is belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the profile latest post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function latestPost()
    {
        return $this->hasOne(Post::class, 'user_id')->latestOfMany();
    }
}
