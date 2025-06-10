<?php
// app/Modules/Post/Services/PostService.php

namespace App\Modules\Post\Services;

use App\Modules\Post\Models\Post;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostService
{
    public function create(array $data): Post
    {
        // تولید slug خودکار
        if (!isset($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['title']);
        }

        // آپلود تصویر شاخص
        if (isset($data['featured_image'])) {
            $data['featured_image'] = $this->uploadFeaturedImage($data['featured_image']);
        }

        // تنظیم تاریخ انتشار
        if ($data['status'] === 'published' && !isset($data['published_at'])) {
            $data['published_at'] = now();
        }

        // تنظیم نویسنده
        $data['user_id'] = auth()->id();

        $post = Post::create($data);

        // اتصال دسته‌بندی‌ها
        if (isset($data['categories'])) {
            $post->categories()->sync($data['categories']);
        }

        return $post;
    }

    public function update(Post $post, array $data): Post
    {
        // بروزرسانی slug در صورت تغییر عنوان
        if (isset($data['title']) && $data['title'] !== $post->title) {
            $data['slug'] = $this->generateSlug($data['title'], $post->id);
        }

        // آپلود تصویر جدید
        if (isset($data['featured_image'])) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $data['featured_image'] = $this->uploadFeaturedImage($data['featured_image']);
        }

        // تنظیم تاریخ انتشار
        if ($data['status'] === 'published' && $post->status !== 'published') {
            $data['published_at'] = now();
        }

        $post->update($data);

        // بروزرسانی دسته‌بندی‌ها
        if (isset($data['categories'])) {
            $post->categories()->sync($data['categories']);
        }

        return $post;
    }

    public function delete(Post $post): bool
    {
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }

        return $post->delete();
    }

    public function publish(Post $post): Post
    {
        $post->update([
            'status' => 'published',
            'published_at' => now()
        ]);

        return $post;
    }

    private function generateSlug(string $title, int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (Post::where('slug', $slug)->when($excludeId, function($query) use ($excludeId) {
            $query->where('id', '!=', $excludeId);
        })->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function uploadFeaturedImage($file): string
    {
        return $file->store('posts', 'public');
    }
}