<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // موقتاً کامنت کنید
        // $this->middleware('permission:dashboard.access');
    }

    public function index()
    {
        // آمار ساده بدون وابستگی به مدل‌های ناموجود
        $stats = [
            'total_users' => User::count(),
            'total_posts' => 0, // موقتاً صفر
            'published_posts' => 0, // موقتاً صفر
            'draft_posts' => 0, // موقتاً صفر
            'pending_comments' => 0, // موقتاً صفر
            'approved_comments' => 0, // موقتاً صفر
            'total_categories' => 0, // موقتاً صفر
            'recent_posts' => collect([]), // مجموعه خالی
            'recent_users' => User::latest()->take(5)->get(),
            'recent_comments' => collect([]), // مجموعه خالی
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function stats()
    {
        // API endpoint برای AJAX
        return response()->json([
            'total_users' => User::count(),
            'total_posts' => 0,
            'published_posts' => 0,
            'pending_comments' => 0,
        ]);
    }
}
