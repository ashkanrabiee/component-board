@extends('admin.layouts.app')

@section('title', 'داشبورد')
@section('page-title', 'داشبورد')

@section('content')
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stats-card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title mb-0">کل کاربران</h6>
                        <h2 class="mt-2 mb-0">{{ number_format($stats['total_users']) }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stats-card text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title mb-0">کل مقالات</h6>
                        <h2 class="mt-2 mb-0">{{ number_format($stats['total_posts']) }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-newspaper fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stats-card text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title mb-0">مقالات منتشر شده</h6>
                        <h2 class="mt-2 mb-0">{{ number_format($stats['published_posts']) }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stats-card text-white" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title mb-0">نظرات در انتظار</h6>
                        <h2 class="mt-2 mb-0">{{ number_format($stats['pending_comments']) }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Users -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-users me-2"></i>آخرین کاربران
                </h5>
            </div>
            <div class="card-body p-0">
                @if($stats['recent_users']->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($stats['recent_users'] as $user)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $user->name }}</h6>
                                        <p class="mb-1 text-muted small">
                                            <i class="fas fa-envelope me-1"></i>{{ $user->email }}
                                        </p>
                                        <span class="badge bg-success">فعال</span>
                                    </div>
                                    <small class="text-muted">
                                        {{ $user->created_at ? $user->created_at->diffForHumans() : 'نامشخص' }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">هنوز کاربری وجود ندارد</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Welcome Card -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>خوش آمدید
                </h5>
            </div>
            <div class="card-body">
                <h5>سلام {{ auth()->user()->name }}! 👋</h5>
                <p class="text-muted">به پنل مدیریت خوش آمدید. از اینجا می‌توانید:</p>
                <ul>
                    <li>رسانه‌ها را مدیریت کنید</li>
                    <li>کاربران را بررسی کنید</li>
                    <li>محتوا ایجاد و مدیریت کنید</li>
                </ul>
                <p class="small text-muted mt-3">
                    آخرین ورود:
                    @if(auth()->user()->last_login_at)
                        {{ auth()->user()->last_login_at->diffForHumans() }}
                    @else
                        اولین ورود
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
