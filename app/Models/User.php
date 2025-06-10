<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'username', 'password', 'phone', 
        'bio', 'avatar', 'status', 'last_login_at'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relations
    public function posts()
    {
        return $this->hasMany(\App\Modules\Post\Models\Post::class);
    }

    public function comments()
    {
        return $this->hasMany(\App\Modules\Comment\Models\Comment::class);
    }

    public function media()
    {
        return $this->hasMany(\App\Modules\Media\Models\Media::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAdmins($query)
    {
        return $query->whereHas('roles', function($q) {
            $q->whereIn('name', ['super-admin', 'admin']);
        });
    }

    // Accessors
    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? 
            asset('storage/' . $this->avatar) : 
            asset('images/default-avatar.png');
    }

    public function getIsAdminAttribute()
    {
        return $this->hasAnyRole(['super-admin', 'admin']);
    }

    public function getPostsCountAttribute()
    {
        return $this->posts()->count();
    }

    // Methods
    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }
}