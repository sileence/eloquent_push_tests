<?php

namespace App;

class UserProfile extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
