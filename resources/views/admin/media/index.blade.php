@extends('admin.layouts.app')

@section('title', 'مدیریت رسانه‌ها')
@section('page-title', 'مدیریت رسانه‌ها')

@section('page-actions')
    @can('media.upload')
        <a href="{{ route('admin.media.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>آپلود فایل
        </a>
    @endcan
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">لیست فایل‌ها</h5>
            </div>
            <div class="col-auto">
                <form method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control" placeholder="جستجو..." 
                           value="{{ request('search') }}">
                    <select name="type" class="form-select">
                        <option value="">همه انواع</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        @if($media->count() > 0)
            <div class="row">
                @foreach($media as $item)
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                @if($item->isImage())
                                    <img src="{{ $item->url }}" alt="{{ $item->alt_text }}" 
                                         class="img-fluid mb-3" style="max-height: 150px; object-fit: cover;">
                                @elseif($item->isVideo())
                                    <i class="fas fa-video fa-4x text-primary mb-3"></i>
                                @elseif($item->isAudio())
                                    <i class="fas fa-music fa-4x text-success mb-3"></i>
                                @else
                                    <i class="fas fa-file fa-4x text-muted mb-3"></i>
                                @endif
                                
                                <h6 class="card-title">{{ Str::limit($item->original_name, 20) }}</h6>
                                <p class="card-text small text-muted">
                                    {{ $item->formatted_size }}<br>
                                    {{ $item->created_at->format('Y/m/d') }}
                                </p>
                                
                                <div class="btn-group w-100" role="group">
                                    <a href="{{ route('admin.media.show', $item) }}" 
                                       class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.media.edit', $item) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @can('media.delete')
                                        <form method="POST" action="{{ route('admin.media.destroy', $item) }}" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger delete-btn">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="d-flex justify-content-center">
                {{ $media->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-photo-video fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">هیچ فایلی یافت نشد</h5>
                @can('media.upload')
                    <a href="{{ route('admin.media.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-2"></i>اولین فایل را آپلود کنید
                    </a>
                @endcan
            </div>
        @endif
    </div>
</div>
@endsection