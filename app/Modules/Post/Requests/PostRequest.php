<?php

namespace App\Modules\Post\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $postId = $this->route('post')?->id;

        return [
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('posts')->ignore($postId)
            ],
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'status' => 'required|in:draft,published,scheduled',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:300',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان مقاله الزامی است',
            'content.required' => 'محتوای مقاله الزامی است',
            'featured_image.image' => 'فایل تصویر شاخص باید تصویر باشد',
            'featured_image.max' => 'حجم تصویر نباید بیش از 5 مگابایت باشد',
            'status.required' => 'وضعیت مقاله الزامی است',
            'categories.*.exists' => 'دسته‌بندی انتخاب شده معتبر نیست',
        ];
    }
}