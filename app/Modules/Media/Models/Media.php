<?php

namespace App\Modules\Media\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'original_name', 'path', 'disk', 'mime_type',
        'size', 'metadata', 'alt_text', 'description', 'user_id'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->path);
    }

    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getTypeAttribute()
    {
        return explode('/', $this->mime_type)[0];
    }

    // Scopes
    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    public function scopeVideos($query)
    {
        return $query->where('mime_type', 'like', 'video/%');
    }

    public function scopeAudios($query)
    {
        return $query->where('mime_type', 'like', 'audio/%');
    }

    public function scopeDocuments($query)
    {
        return $query->where('mime_type', 'like', 'application/%');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Methods
    public function isImage()
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isVideo()
    {
        return str_starts_with($this->mime_type, 'video/');
    }

    public function isAudio()
    {
        return str_starts_with($this->mime_type, 'audio/');
    }

    public function isDocument()
    {
        return str_starts_with($this->mime_type, 'application/');
    }
}