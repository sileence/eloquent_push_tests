<?php

namespace App;

class Image extends Model
{
    public function gallery()
    {
        return $this->morphTo();
    }
}
