<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'user_id', 'parent_id'];

    // One to many realtionship
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // One to many realtionship -> Users has many categories
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // I use this function to get Active Posts in the current category
    public function publishedPosts()
    {
        return SELF::posts()->with(['user:id,name', 'category'])->published();
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    // Eager-loadable recursive relation
    public function childrenRecursive()
    {
        return $this->children()->with(['childrenRecursive' => function ($q) {
            $q->orderBy('name');
        }]);
    }

    // Scope roots (top-level categories)
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }
}
