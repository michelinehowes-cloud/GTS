@extends('layouts.app')

@section('title', 'لوحة تحكم مسؤول الميديا')

@section('page-title', 'لوحة تحكم مسؤول الميديا')

@section('content')
<div class="row">
    <!-- إحصائيات سريعة -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-primary">
                        <i class="fas fa-video text-white"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="card-title mb-1">إجمالي الوسائط</h6>
                        <h4 class="mb-0">{{ $totalMedia }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-success">
                        <i class="fas fa-newspaper text-white"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="card-title mb-1">الأخبار النشطة</h6>
                        <h4 class="mb-0">{{ $activeNews }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-warning">
                        <i class="fas fa-bullhorn text-white"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="card-title mb-1">الإعلانات النشطة</h6>
                        <h4 class="mb-0">{{ $activeAnnouncements }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-info">
                        <i class="fas fa-graduation-cap text-white"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="card-title mb-1">التدريبات المغطاة</h6>
                        <h4 class="mb-0">{{ $coveredTrainings }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- التدريبات القادمة -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>التدريبات القادمة
                </h5>
            </div>
            <div class="card-body">
                @if($upcomingTrainings->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($upcomingTrainings->take(5) as $training)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $training->title }}</h6>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>{{ $training->start_date->format('d/m/Y') }}
                                        <i class="fas fa-map-marker-alt ms-2 me-1"></i>{{ $training->location }}
                                    </small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $training->media_coverage_status == 'covered' ? 'success' : ($training->media_coverage_status == 'pending' ? 'warning' : 'secondary') }}">
                                        {{ $training->getMediaCoverageStatusText() }}
                                    </span>
                                    <br>
                                    <a href="{{ route('media.trainings.show', $training) }}" class="btn btn-sm btn-outline-primary mt-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if($upcomingTrainings->count() > 5)
                    <div class="text-center mt-3">
                        <a href="{{ route('media.trainings.index') }}" class="btn btn-outline-primary">
                            عرض المزيد
                        </a>
                    </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد تدريبات قادمة</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- الأخبار والإعلانات الأخيرة -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-newspaper me-2"></i>المحتوى الأخير
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-newspaper me-1"></i>آخر الأخبار
                        </h6>
                        @if($recentNews->count() > 0)
                            @foreach($recentNews->take(3) as $news)
                            <div class="mb-2">
                                <small class="d-block text-muted">{{ $news->published_at->diffForHumans() }}</small>
                                <a href="{{ route('media.news.show', $news) }}" class="text-decoration-none">
                                    {{ Str::limit($news->title, 30) }}
                                </a>
                            </div>
                            @endforeach
                        @else
                            <small class="text-muted">لا توجد أخبار</small>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-warning mb-3">
                            <i class="fas fa-bullhorn me-1"></i>آخر الإعلانات
                        </h6>
                        @if($recentAnnouncements->count() > 0)
                            @foreach($recentAnnouncements->take(3) as $announcement)
                            <div class="mb-2">
                                <small class="d-block text-muted">{{ $announcement->created_at->diffForHumans() }}</small>
                                <a href="{{ route('media.announcements.show', $announcement) }}" class="text-decoration-none">
                                    {{ Str::limit($announcement->title, 30) }}
                                </a>
                            </div>
                            @endforeach
                        @else
                            <small class="text-muted">لا توجد إعلانات</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- الوسائط الأخيرة -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-images me-2"></i>الوسائط المرفوعة مؤخراً
                </h5>
                <div>
                    <a href="{{ route('media.gallery') }}" class="btn btn-outline-primary btn-sm me-2">
                        <i class="fas fa-eye me-1"></i>عرض المعرض
                    </a>
                    <a href="{{ route('media.reports.coverage') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-chart-bar me-1"></i>التقارير
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($recentMedia->count() > 0)
                    <div class="row">
                        @foreach($recentMedia->take(8) as $media)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                            <div class="card media-card">
                                <div class="position-relative">
                                    @if($media->file_type === 'image')
                                        <img src="{{ Storage::url($media->file_path) }}" class="card-img-top" alt="{{ $media->caption }}" style="height: 150px; object-fit: cover;">
                                    @else
                                        <video class="card-img-top" style="height: 150px; object-fit: cover;">
                                            <source src="{{ Storage::url($media->file_path) }}" type="video/mp4">
                                        </video>
                                        <div class="position-absolute top-50 start-50 translate-middle">
                                            <i class="fas fa-play-circle fa-2x text-white"></i>
                                        </div>
                                    @endif
                                    <div class="media-overlay">
                                        <a href="{{ Storage::url($media->file_path) }}" target="_blank" class="btn btn-light btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <small class="text-muted d-block">{{ $media->created_at->diffForHumans() }}</small>
                                    @if($media->training)
                                        <small class="text-primary">{{ $media->training->title }}</small>
                                    @else
                                        <small class="text-info">واجهة الترحيب</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد وسائط مرفوعة</p>
                        <a href="{{ route('media.upload') }}" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i>رفع وسائط جديدة
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.stats-card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.stats-card:hover {
    transform: translateY(-2px);
}

.stats-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.media-card {
    border: none;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.media-card:hover {
    transform: translateY(-2px);
}

.media-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s;
}

.media-card:hover .media-overlay {
    opacity: 1;
}
</style>
@endpush
