<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','slug','type','status','source','source_url','image_url',
        'excerpt','body','tags','fetched_at','publish_at','published_at',
        'evergreen','meta_title','meta_description','canonical_url'
    ];

    protected $casts = [
        'tags' => 'array',
        'fetched_at' => 'datetime',
        'publish_at' => 'datetime',
        'published_at' => 'datetime',
        'evergreen' => 'boolean',
    ];

    // Slug automático sencillo
    public static function booted() 
    {
        static::saving(function($post){
            if (!$post->slug) {
                $base = Str::slug(Str::limit($post->title, 60, ''));
                $slug = $base; $i=1;
                while (static::where('slug',$slug)->where('id','!=',$post->id)->exists()) {
                    $slug = $base.'-'.(++$i);
                }
                $post->slug = $slug;
            }
        });
    }

    // Scopes útiles
    public function scopePublished($query)
    {
        return $query->where('status','published');
    }

    public function scopePendingIA($query)
    {
        return $query->whereNull('body');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status','scheduled');
    }

    public function scopeDraft($query) 
    {
        return $query->where('status','draft');
    }
}
