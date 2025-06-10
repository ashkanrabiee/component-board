<?php

namespace App\Modules\Media\Services;

use App\Modules\Media\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;



class MediaService
{
    public function upload(UploadedFile $file, array $metadata = []): Media
    {
        // تولید نام منحصر به فرد
        $fileName = $this->generateFileName($file);
        
        // آپلود فایل
        $path = $file->storeAs('media', $fileName, 'public');
        
        // استخراج metadata
        $fileMetadata = $this->extractMetadata($file);
        
        // ایجاد رکورد در دیتابیس
        return Media::create([
            'name' => $fileName,
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'disk' => 'public',
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'metadata' => array_merge($fileMetadata, $metadata),
            'alt_text' => $metadata['alt_text'] ?? null,
            'description' => $metadata['description'] ?? null,
            'user_id' => auth()->id(),
        ]);
    }

    public function update(Media $media, array $data): Media
    {
        $media->update($data);
        return $media;
    }

    public function delete(Media $media): bool
    {
        // حذف فایل فیزیکی
        if (Storage::disk($media->disk)->exists($media->path)) {
            Storage::disk($media->disk)->delete($media->path);
        }

        // حذف thumbnails (اگر وجود دارد)
        $this->deleteThumbnails($media);

        return $media->delete();
    }

    private function generateFileName(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        
        return $name . '_' . time() . '_' . Str::random(8) . '.' . $extension;
    }

    private function extractMetadata(UploadedFile $file): array
    {
        $metadata = [];

        // برای تصاویر
        if (str_starts_with($file->getMimeType(), 'image/')) {
            $imageSize = getimagesize($file->getPathname());
            if ($imageSize) {
                $metadata['width'] = $imageSize[0];
                $metadata['height'] = $imageSize[1];
            }
        }

        return $metadata;
    }

    private function deleteThumbnails(Media $media): void
    {
        // منطق حذف thumbnail ها
        $thumbnailPath = dirname($media->path) . '/thumbnails/';
        $thumbnailFiles = Storage::disk($media->disk)->files($thumbnailPath);
        
        foreach ($thumbnailFiles as $file) {
            if (str_contains($file, pathinfo($media->name, PATHINFO_FILENAME))) {
                Storage::disk($media->disk)->delete($file);
            }
        }
    }

    public function getThumbnailUrl(Media $media, string $size = 'thumb'): string
    {
        if (!$media->isImage()) {
            return $media->url;
        }

        $thumbPath = 'media/thumbs/' . $size . '_' . $media->name;
        
        if (Storage::disk($media->disk)->exists($thumbPath)) {
            return asset('storage/' . $thumbPath);
        }

        return $media->url;
    }
}