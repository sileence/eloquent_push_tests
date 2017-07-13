<?php

namespace App;

class Post extends Model
{
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->inversedBy('post');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function featuredImage()
    {
        return $this->morphOne(FeaturedImage::class, 'featured')->inversedBy('featured');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'gallery')->inversedBy('gallery');
    }
}
