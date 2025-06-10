<?php

namespace App\Modules\Media\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Modules\Media\Models\Media;
use App\Modules\Media\Requests\MediaRequest;
use App\Modules\Media\Services\MediaService;
use Illuminate\Http\Request;

class MediaController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $mediaService;

   public function __construct(MediaService $mediaService)
{
    $this->mediaService = $mediaService;
    $this->middleware('auth');

    // موقتاً کامنت کنید
    // $this->middleware('permission:media.index')->only('index');
    // $this->middleware('permission:media.upload')->only(['create', 'store', 'upload']);
    // $this->middleware('permission:media.delete')->only('destroy');
}

    public function index(Request $request)
    {
        $media = Media::with('user')
            ->when($request->search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('original_name', 'like', "%{$search}%");
            })
            ->when($request->type, function($query, $type) {
                switch($type) {
                    case 'image':
                        $query->images();
                        break;
                    case 'video':
                        $query->videos();
                        break;
                    case 'audio':
                        $query->audios();
                        break;
                    case 'document':
                        $query->documents();
                        break;
                }
            })
            ->when($request->user_id, function($query, $userId) {
                $query->where('user_id', $userId);
            })
            ->latest()
            ->paginate(24);

        $types = ['image', 'video', 'audio', 'document'];
        $users = \App\Models\User::select('id', 'name')->get();

        return view('admin.media.index', compact('media', 'types', 'users'));
    }

    public function create()
    {
        return view('admin.media.create');
    }

    public function store(MediaRequest $request)
    {
        $files = $request->file('files');
        $uploadedFiles = [];

        foreach ($files as $file) {
            $media = $this->mediaService->upload($file, $request->only(['alt_text', 'description']));
            $uploadedFiles[] = $media;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'files' => $uploadedFiles
            ]);
        }

        return redirect()->route('admin.media.index')
            ->with('success', count($uploadedFiles) . ' فایل با موفقیت آپلود شد');
    }

    public function show(Media $media)
    {
        return view('admin.media.show', compact('media'));
    }

    public function edit(Media $media)
    {
        return view('admin.media.edit', compact('media'));
    }

    public function update(Request $request, Media $media)
    {
        $request->validate([
            'alt_text' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500'
        ]);

        $this->mediaService->update($media, $request->only(['alt_text', 'description']));

        return redirect()->route('admin.media.index')
            ->with('success', 'اطلاعات فایل بروزرسانی شد');
    }

    public function destroy(Media $media)
    {
        $this->mediaService->delete($media);

        return redirect()->route('admin.media.index')
            ->with('success', 'فایل با موفقیت حذف شد');
    }

    // AJAX Upload
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240'
        ]);

        try {
            $media = $this->mediaService->upload($request->file('file'));

            return response()->json([
                'success' => true,
                'media' => $media,
                'url' => $media->url,
                'thumbnail' => $this->mediaService->getThumbnailUrl($media)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در آپلود فایل: ' . $e->getMessage()
            ], 422);
        }
    }

    // Bulk Delete
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'media_ids' => 'required|array',
            'media_ids.*' => 'exists:media,id'
        ]);

        $media = Media::whereIn('id', $request->media_ids)->get();

        foreach ($media as $item) {
            $this->mediaService->delete($item);
        }

        return redirect()->back()
            ->with('success', count($media) . ' فایل حذف شد');
    }

    // Media Browser for editors
    public function browser(Request $request)
    {
        $media = Media::when($request->type, function($query, $type) {
            if ($type === 'image') {
                $query->images();
            }
        })->latest()->paginate(20);

        if ($request->wantsJson()) {
            return response()->json($media);
        }

        return view('admin.media.browser', compact('media'));
    }
}
