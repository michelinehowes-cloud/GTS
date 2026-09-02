@extends('layouts.app')

@section('title', 'محرك البحث المتقدم - ' . $fair->title)

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1 text-primary"><i class="fas fa-search me-2"></i>البحث المتقدم عن الكفاءات</h2>
            <p class="text-muted">ابحث بين {{ $graduates->total() }} خريج مسجل في {{ $fair->title }}</p>
        </div>
        <a href="{{ route('company.job-fairs.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right"></i> عودة للمعرض
        </a>
    </div>

    <!-- Filter Form -->
    <div class="card shadow-sm border-0 rounded-lg mb-4">
        <div class="card-body p-4 bg-light">
            <form action="{{ route('company.job-fairs.search', $fair->id) }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">كلمة مفتاحية (اسم الخريج أو البريد)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-font text-muted"></i></span>
                        <input type="text" name="keyword" class="form-control" placeholder="ابحث بالاسم..." value="{{ request('keyword') }}">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-bold">التخصص</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-graduation-cap text-muted"></i></span>
                        <input type="text" name="major" class="form-control" placeholder="هندسة برمجيات..." value="{{ request('major') }}">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-bold">الحد الأدنى للمعدل التراكمي (%)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-star text-muted"></i></span>
                        <input type="number" step="0.01" min="0" max="100" name="gpa_min" class="form-control" placeholder="مثال: 75" value="{{ request('gpa_min') }}">
                    </div>
                </div>
                
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Grid -->
    <div class="row g-4">
        @forelse($graduates as $graduate)
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="card shadow-sm border-0 h-100 rounded-lg hover-shadow">
                <div class="card-body text-center p-4">
                    <div class="avatar-circle mx-auto mb-3 bg-primary text-white fs-2 fw-bold d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; border-radius: 50%;">
                        {{ mb_substr($graduate->name, 0, 1) }}
                    </div>
                    
                    <h5 class="fw-bold mb-1">{{ $graduate->name }}</h5>
                    <p class="text-muted small mb-3">{{ $graduate->major ?? 'تخصص غير محدد' }}</p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <span class="badge bg-light text-dark border"><i class="fas fa-star text-warning"></i> المعدل: {{ $graduate->graduateData->gpa ? $graduate->graduateData->gpa . '%' : 'N/A' }}</span>
                        <span class="badge bg-light text-dark border"><i class="fas fa-calendar-alt text-secondary"></i> دفعة: {{ $graduate->graduateData->graduation_year ?? 'N/A' }}</span>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('graduate.profile.public', $graduate->id) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-user me-1"></i> عرض السيرة الذاتية
                        </a>
                        <a href="{{ route('messages.show', $graduate->id) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-envelope me-1"></i> مراسلة
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted mb-3">
                <i class="fas fa-search fa-3x"></i>
            </div>
            <h4 class="fw-bold text-gray-800">لا توجد نتائج مطابقة</h4>
            <p class="text-muted">حاول تغيير معايير البحث أو استخدام كلمات مفتاحية أخرى.</p>
            <a href="{{ route('company.job-fairs.search', $fair->id) }}" class="btn btn-outline-primary mt-2">إعادة ضبط البحث</a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-5">
        {{ $graduates->links() }}
    </div>

</div>

<style>
    .hover-shadow {
        transition: box-shadow 0.3s ease, transform 0.3s ease;
    }
    .hover-shadow:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        transform: translateY(-3px);
    }
</style>
@endsection
