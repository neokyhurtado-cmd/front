<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id','title','slug','type','status','source','source_url','image_url',
        'excerpt','body','tags','fetched_at','publish_at','published_at',
        'evergreen','meta_title','meta_description','canonical_url','featured_image',
        'pinned','pinned_until','is_pinned','image_source_label','image_source_url',
        'seo_keywords','raw_text','content'
    ];

    protected $casts = [
        'tags' => 'array',
        'fetched_at' => 'datetime',
        'publish_at' => 'datetime',
        'published_at' => 'datetime',
        'evergreen' => 'boolean',
        'pinned' => 'boolean',
        'is_pinned' => 'boolean',
        'pinned_until' => 'datetime',
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

    public function getRouteKeyName(): string 
    { 
        return 'slug'; 
    }

    // Scopes útiles
    public function scopePublished($query)
    {
        return $query->where('status','published')
                    ->where(function($q){
                        $q->whereNull('publish_at')->orWhere('publish_at','<=',now());
                    });
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

    // Scope para posts destacados activos
    public function scopeActivePinned($query) 
    {
        return $query->where('pinned', true)
                     ->where(function($x){
                         $x->whereNull('pinned_until')
                           ->orWhere('pinned_until','>=',now());
                     });
    }

    // Métodos para gestión de destacados
    public function pin($daysFromNow = 30)
    {
        $base = $this->published_at ?? $this->publish_at ?? now();
        $this->update([
            'pinned' => true,
            'pinned_until' => \Illuminate\Support\Carbon::parse($base)->addDays($daysFromNow)
        ]);
    }

    public function unpin()
    {
        $this->update([
            'pinned' => false,
            'pinned_until' => null
        ]);
    }

    // Accessor para URLs de imágenes con fallback
    public function getImageUrlAttribute(): ?string
    {
        // Si hay featured_image (subida por Filament)
        if ($this->featured_image) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->featured_image);
        }
        
        // Si hay image_url (URL externa del RSS)
        if ($this->attributes['image_url'] ?? null) {
            return $this->attributes['image_url'];
        }
        
        return null;
    }

    // Helper para obtener primer tag
    public function firstTag(): ?string
    {
        return $this->tags && count($this->tags) > 0 ? $this->tags[0] : null;
    }

    // Scope para posts destacados con orden fijo
    public function scopeFeatured($query)
    {
        return $query->where(function($q) {
                $q->where('pinned', true)->orWhere('is_pinned', true);
            })
            ->where(function($q) {
                $q->whereNull('pinned_until')
                  ->orWhere('pinned_until', '>=', now());
            })
            ->orderBy('pinned', 'desc')
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc');
    }

    // Accessor unificado para is_pinned
    public function getIsFeaturedAttribute(): bool
    {
        return $this->pinned || $this->is_pinned;
    }
}
