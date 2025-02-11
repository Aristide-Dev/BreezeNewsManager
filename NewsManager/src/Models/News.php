<?php

namespace AristechDev\NewsManager\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $table = 'news';

    protected $fillable = ['title', 'content', 'published_at'];

    protected $dates = ['published_at'];
} 