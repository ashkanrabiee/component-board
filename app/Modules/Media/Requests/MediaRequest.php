<?php

namespace App\Modules\Media\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'files' => 'required|array',
            'files.*' => 'file|max:10240|mimes:jpeg,png,jpg,gif,svg,webp,pdf,doc,docx,txt,zip,mp4,avi,mov,mp3,wav',
            'alt_text' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'files.required' => 'حداقل یک فایل برای آپلود انتخاب کنید',
            'files.*.file' => 'فایل انتخاب شده معتبر نیست',
            'files.*.max' => 'حجم فایل نباید بیش از 10 مگابایت باشد',
            'files.*.mimes' => 'فرمت فایل پشتیبانی نمی‌شود',
            'alt_text.max' => 'متن جایگزین نباید بیش از 255 کاراکتر باشد',
            'description.max' => 'توضیحات نباید بیش از 500 کاراکتر باشد',
        ];
    }
}