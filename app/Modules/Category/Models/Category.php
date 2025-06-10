<?php

namespace App\Modules\Category\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Post\Models\Post;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'image', 'color',
        'parent_id', 'sort_order', 'is_active', 'meta_data'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'meta_data' => 'array',
    ];

    // Relations
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeParent($query)
    {
        return $query->whereNull('parent_id');
    }

    // Accessors
    public function getImageUrlAttribute()
    {
        return $this->image ?
            asset('storage/' . $this->image) :
            asset('images/default-category.png');
    }

    public function getPostsCountAttribute()
    {
        return $this->posts()->count();
    }
}
