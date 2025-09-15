<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewsItem extends Model
{
    use HasFactory;

    protected $table = 'news_items';

    protected $fillable = [
        'title', 'href', 'tag', 'image', 'domain', 'published_at', 'fetched_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'fetched_at' => 'datetime',
    ];
}
