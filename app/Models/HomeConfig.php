<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeConfig extends Model
{
    use HasFactory;
    
    protected $fillable = ['key', 'value', 'type', 'group', 'description'];
    
    protected $casts = [
        // No casteamos aquí, lo hacemos manualmente en getValue()
    ];
    
    public static function get($key, $default = null)
    {
        $config = static::where('key', $key)->first();
        return $config ? $config->getValue() : $default;
    }
    
    public static function set($key, $value, $type = 'text', $group = 'general', $description = null)
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description
            ]
        );
    }
    
    public function getValue()
    {
        return match($this->type) {
            'json' => is_string($this->value) ? json_decode($this->value, true) : $this->value,
            'number' => (float) $this->value,
            'boolean' => (bool) $this->value,
            default => (string) $this->value
        };
    }
    
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }
}
