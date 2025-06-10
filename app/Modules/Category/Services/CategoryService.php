<?php
// app/Modules/Category/Services/CategoryService.php

namespace App\Modules\Category\Services;

use App\Modules\Category\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryService
{
    public function create(array $data): Category
    {
        // تولید slug خودکار
        if (!isset($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }

        // آپلود تصویر
        if (isset($data['image'])) {
            $data['image'] = $this->uploadImage($data['image']);
        }

        // تنظیم ترتیب
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = Category::max('sort_order') + 1;
        }

        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        // بروزرسانی slug در صورت تغییر نام
        if (isset($data['name']) && $data['name'] !== $category->name) {
            $data['slug'] = $this->generateSlug($data['name'], $category->id);
        }

        // آپلود تصویر جدید
        if (isset($data['image'])) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $this->uploadImage($data['image']);
        }

        $category->update($data);
        return $category;
    }

    public function delete(Category $category): bool
    {
        // حذف فرزندان
        $category->children()->delete();

        // حذف تصویر
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        return $category->delete();
    }

    private function generateSlug(string $name, int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (Category::where('slug', $slug)->when($excludeId, function($query) use ($excludeId) {
            $query->where('id', '!=', $excludeId);
        })->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function uploadImage($file): string
    {
        return $file->store('categories', 'public');
    }
}