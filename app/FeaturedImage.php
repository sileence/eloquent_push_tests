<?php

namespace App;

class FeaturedImage extends Model
{
    public function featured()
    {
        return $this->morphTo();
    }
}
