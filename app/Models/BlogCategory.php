<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $fillable = ['name', 'slug', 'color'];

    public function posts()
    {
        return $this->hasMany(BlogPost::class);
    }

    public function publishedPosts()
    {
        return $this->hasMany(BlogPost::class)->where('status', 'published');
    }
}
