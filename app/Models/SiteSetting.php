<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'hero_title','hero_subtitle','notification_text',
        'sidebar_topics','corporate_url','dark_default',
    ];

    protected $casts = [
        'sidebar_topics' => 'array',
        'dark_default' => 'boolean',
    ];
}
