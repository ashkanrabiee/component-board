<?php
// app/Modules/Category/Controllers/CategoryController.php

namespace App\Modules\Category\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Category\Models\Category;
use App\Modules\Category\Requests\CategoryRequest;
use App\Modules\Category\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
        $this->middleware('auth');
        $this->middleware('permission:categories.index')->only('index');
        $this->middleware('permission:categories.create')->only(['create', 'store']);
        $this->middleware('permission:categories.edit')->only(['edit', 'update']);
        $this->middleware('permission:categories.delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $categories = Category::with('parent')
            ->when($request->search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->status !== null, function($query) use ($request) {
                $query->where('is_active', $request->status);
            })
            ->when($request->parent_id, function($query, $parentId) {
                if ($parentId === 'null') {
                    $query->whereNull('parent_id');
                } else {
                    $query->where('parent_id', $parentId);
                }
            })
            ->orderBy('sort_order')
            ->paginate(15);

        $parentCategories = Category::whereNull('parent_id')->active()->get();

        return view('admin.categories.index', compact('categories', 'parentCategories'));
    }

    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->active()->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(CategoryRequest $request)
    {
        $category = $this->categoryService->create($request->validated());
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'دسته‌بندی با موفقیت ایجاد شد');
    }

    public function show(Category $category)
    {
        $category->load(['parent', 'children', 'posts']);
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->active()
            ->get();
        
        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $this->categoryService->update($category, $request->validated());
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'دسته‌بندی با موفقیت بروزرسانی شد');
    }

    public function destroy(Category $category)
    {
        if ($category->posts()->count() > 0) {
            return redirect()->back()
                ->with('error', 'نمی‌توان دسته‌بندی که دارای مقاله است را حذف کرد');
        }

        $this->categoryService->delete($category);
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'دسته‌بندی با موفقیت حذف شد');
    }

    public function toggleStatus(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);
        
        $status = $category->is_active ? 'فعال' : 'غیرفعال';
        return redirect()->back()
            ->with('success', "وضعیت دسته‌بندی به {$status} تغییر یافت");
    }
}