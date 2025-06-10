<?php
// app/Modules/Category/Requests/CategoryRequest.php

namespace App\Modules\Category\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('categories')->ignore($categoryId)
            ],
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'color' => 'nullable|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'نام دسته‌بندی الزامی است',
            'slug.unique' => 'این اسلاگ قبلاً استفاده شده است',
            'image.image' => 'فایل تصویر باید تصویر باشد',
            'image.max' => 'حجم تصویر نباید بیش از 2 مگابایت باشد',
            'color.regex' => 'فرمت رنگ صحیح نیست',
            'parent_id.exists' => 'دسته‌بندی والد انتخاب شده معتبر نیست',
        ];
    }
}