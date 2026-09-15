<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'body',
        'image',
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function getImageAttribute($value)
    {
        return $value
            ? asset('storage/'.$value)
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeSearch($query, ?string $search)
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('body', 'like', "%{$search}%")
                ->orWhere('category', 'like', "%{$search}%");
        });
    }

    public function scopeCategory($query, ?string $category)
    {
        if (! $category) {
            return $query;
        }

        return $query->where('category', $category);
    }

    public static function categories(): \Illuminate\Support\Collection
    {
        return self::pluck('category')->filter()->unique()->values();
    }
}
