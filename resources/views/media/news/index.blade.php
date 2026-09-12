@extends('layouts.app')

@section('title', 'إدارة الأخبار الصحفية | وحدة الإعلام')
@section('page-title', 'إدارة الأخبار الصحفية')

@section('content')
<div class="container-fluid py-4">

    <!-- الشريط الترحيبي بالهوية الرسمية للمنظومة -->
    <x-page-hero
        title="وحدة الإعلام وإدارة الأخبار"
        subtitle="نشر وتحرير التغطيات الصحفية والأخبار الرسمية الموجهة للطلاب والخريجين وسوق العمل"
        icon="fas fa-newspaper"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم الميديا', 'url' => route('media.dashboard')],
            ['label' => 'إدارة الأخبار']
        ]"
        secondaryBadge="{{ $stats['total'] }} خبر مسجل"
        secondaryBadgeIcon="fas fa-file-alt"
    >
        <a href="{{ route('media.news.create') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-plus-circle fs-6"></i>
            <span>+ إضافة خبر جديد</span>
        </a>
        <a href="{{ route('media.live-studio') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem;">
            <i class="fas fa-broadcast-tower fs-6 text-danger"></i>
            <span>أستوديو البث الحي</span>
        </a>
        <a href="{{ route('media.dashboard') }}" class="btn btn-outline-light text-white fw-bold py-2.5 px-3 rounded-3 d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem;">
            <i class="fas fa-th-large fs-6"></i>
            <span>لوحة الميديا</span>
        </a>
    </x-page-hero>

    <!-- تنبيهات النظام -->
    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 shadow-sm py-3 px-4 d-flex align-items-center gap-3 mb-4" role="alert" style="background: rgba(16, 185, 129, 0.1); color: #065f46;">
            <i class="fas fa-check-circle fs-5 text-success"></i>
            <span class="fw-semibold">{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 rounded-4 shadow-sm py-3 px-4 d-flex align-items-center gap-3 mb-4" role="alert" style="background: rgba(239, 68, 68, 0.1); color: #991b1b;">
            <i class="fas fa-exclamation-circle fs-5 text-danger"></i>
            <span class="fw-semibold">{{ session('error') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- صف بطاقات إحصائيات Bento المعتمدة للمنصة -->
    <div class="row g-3 mb-4">
        @include('components.stat-card', [
            'col' => 'col-6 col-lg-3',
            'title' => 'إجمالي الأخبار',
            'value' => $stats['total'],
            'icon' => 'fas fa-newspaper',
            'color' => 'primary',
            'description' => 'جميع التغطيات المسجلة'
        ])

        @include('components.stat-card', [
            'col' => 'col-6 col-lg-3',
            'title' => 'أخبار منشورة',
            'value' => $stats['active'],
            'icon' => 'fas fa-check-circle',
            'color' => 'success',
            'badge' => 'معتمدة',
            'badgeClass' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
            'description' => 'معروضة للجمهور حالياً'
        ])

        @include('components.stat-card', [
            'col' => 'col-6 col-lg-3',
            'title' => 'مسودات معطلة',
            'value' => $stats['draft'],
            'icon' => 'fas fa-pencil-alt',
            'color' => 'warning',
            'badge' => 'مسودة',
            'badgeClass' => 'bg-warning bg-opacity-15 text-dark border border-warning border-opacity-25',
            'description' => 'بانتظار المراجعة أو النشر'
        ])

        @include('components.stat-card', [
            'col' => 'col-6 col-lg-3',
            'title' => 'أخبار مصورة',
            'value' => $stats['with_images'],
            'icon' => 'fas fa-image',
            'color' => 'info',
            'badge' => 'مدعمة بصور',
            'badgeClass' => 'bg-info bg-opacity-10 text-primary border border-info border-opacity-25',
            'description' => 'تحتوي غلافاً صحفياً بارزاً'
        ])
    </div>

    <!-- بطاقة البحث والتصفية المتقدمة -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: #ffffff;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-filter text-primary"></i>
                    <h6 class="fw-bold mb-0 text-dark">البحث وتصفية الأخبار</h6>
                </div>
                @if(request('search') || request('status'))
                    <a href="{{ route('media.news.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        <i class="fas fa-undo me-1"></i> إعادة ضبط الفلاتر
                    </a>
                @endif
            </div>

            <form action="{{ route('media.news.index') }}" method="GET" class="row g-3">
                <div class="col-12 col-md-7">
                    <label class="form-label small fw-bold text-dark mb-1.5">
                        <i class="fas fa-search text-primary me-1"></i> كلمة البحث
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control bg-light border-start-0 rounded-end-3" placeholder="ابحث في عنوان الخبر أو في نصه..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label small fw-bold text-dark mb-1.5">
                        <i class="fas fa-tag text-primary me-1"></i> حالة النشر
                    </label>
                    <select name="status" class="form-select bg-light rounded-3">
                        <option value="">جميع الحالات</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>🟢 نشط (معروض للجمهور)</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>⚪ مسودة (غير معروض)</option>
                    </select>
                </div>

                <div class="col-12 col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm" style="background: #1d4ed8;">
                        <i class="fas fa-search"></i>
                        <span>بحث</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- شبكة بطاقات الأخبار -->
    <div class="d-flex align-items-center justify-content-between mb-3 px-1">
        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="fas fa-newspaper text-primary"></i>
            <span>قائمة الأخبار الصحفية</span>
            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-2.5 py-1" style="font-size: 0.75rem;">{{ $news->total() }}</span>
        </h5>
        <a href="{{ route('media.news.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm d-none d-sm-inline-flex align-items-center gap-1.5" style="background: #1d4ed8;">
            <i class="fas fa-plus"></i>
            <span>إضافة خبر جديد</span>
        </a>
    </div>

    @if($news->count() > 0)
        <div class="row g-4 mb-4">
            @foreach($news as $item)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm h-100 overflow-hidden d-flex flex-column news-bento-card" style="transition: transform 0.2s ease, box-shadow 0.2s ease; background: #ffffff;">
                        <!-- غلاف الصورة -->
                        <div class="position-relative" style="height: 190px; background: #0f172a; overflow: hidden;">
                            @if($item->thumbnail_url)
                                <img src="{{ $item->thumbnail_url }}" alt="{{ $item->title }}" class="w-100 h-100" style="object-fit: cover; transition: transform 0.3s ease;">
                            @else
                                <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-white-50" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%);">
                                    <i class="fas fa-newspaper fa-3x mb-2 opacity-50 text-white"></i>
                                    <small class="text-white-50">لا توجد صورة غلاف</small>
                                </div>
                            @endif

                            <!-- شارة الحالة فوق الصورة -->
                            <div class="position-absolute top-0 start-0 m-3 d-flex gap-2">
                                <span class="badge rounded-pill px-2.5 py-1.5 small {{ $item->status_data['class'] }}" style="backdrop-filter: blur(8px); font-size: 0.75rem;">
                                    <i class="{{ $item->status_data['icon'] }} me-1"></i>{{ $item->status_data['label'] }}
                                </span>
                            </div>

                            <!-- وقت القراءة التقديري -->
                            <div class="position-absolute bottom-0 end-0 m-2.5">
                                <span class="badge bg-black bg-opacity-70 text-white rounded-pill px-2 py-1 small" style="backdrop-filter: blur(4px); font-size: 0.72rem;">
                                    <i class="far fa-clock me-1 text-warning"></i>{{ $item->reading_time }} دقيقة
                                </span>
                            </div>
                        </div>

                        <!-- تفاصيل الخبر -->
                        <div class="card-body p-4 d-flex flex-column flex-grow-1">
                            <div class="d-flex align-items-center gap-2 text-muted small mb-2" style="font-size: 0.78rem;">
                                <span><i class="far fa-calendar-alt text-primary me-1"></i>{{ $item->published_at ? $item->published_at->translatedFormat('d M Y') : 'غير محدد' }}</span>
                                <span>•</span>
                                <span><i class="far fa-user text-secondary me-1"></i>{{ $item->creator->name ?? 'المكتب الإعلامي' }}</span>
                            </div>

                            <h5 class="fw-bold text-dark mb-2" style="line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="{{ $item->title }}">
                                <a href="{{ route('media.news.show', $item) }}" class="text-dark text-decoration-none hover-primary">
                                    {{ $item->title }}
                                </a>
                            </h5>

                            <p class="text-secondary small mb-3 flex-grow-1" style="line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $item->excerpt }}
                            </p>

                            <!-- شريط الإجراءات -->
                            <div class="pt-3 border-top d-flex align-items-center justify-content-between gap-2 mt-auto">
                                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                    <a href="{{ route('media.news.show', $item) }}" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-primary" title="معاينة وقراءة الخبر داخل المنظومة">
                                        <i class="fas fa-eye me-1"></i>عرض
                                    </a>
                                    <a href="{{ route('media.news.edit', $item) }}" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-warning" title="تعديل الخبر">
                                        <i class="fas fa-edit me-1"></i>تعديل
                                    </a>
                                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-success" title="نسخ رابط النشر العام للمشاركة" onclick="navigator.clipboard.writeText('{{ route('public.news.show', $item) }}'); alert('تم نسخ رابط الخبر العام للنشر بنجاح:\n{{ route('public.news.show', $item) }}\n\nيمكنك الآن مشاركته مع الجمهور مباشرة دون تسجيل دخول.');">
                                        <i class="fas fa-share-alt me-1"></i>نسخ الرابط
                                    </button>
                                </div>

                                <div class="d-flex align-items-center gap-1">
                                    <form action="{{ route('media.news.toggle-status', $item) }}" method="POST" class="d-inline m-0">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-light border rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="{{ $item->is_active ? 'إلغاء التفعيل (جعله مسودة)' : 'تفعيل الخبر ونشره' }}">
                                            <i class="fas {{ $item->is_active ? 'fa-eye-slash text-secondary' : 'fa-check text-success' }}" style="font-size: 0.8rem;"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('media.news.destroy', $item) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('هل أنت متأكد تماماً من حذف هذا الخبر نهائياً؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="حذف الخبر">
                                            <i class="fas fa-trash-alt" style="font-size: 0.8rem;"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- الترقيم والتنقل -->
        <div class="d-flex justify-content-center mt-3">
            {{ $news->appends(request()->query())->links() }}
        </div>
    @else
        <!-- حالة عدم وجود بيانات (Empty State) -->
        <div class="card border-0 rounded-4 shadow-sm text-center py-5 px-3" style="background: #ffffff;">
            <div class="my-4">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                    <i class="fas fa-newspaper"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">لا توجد أخبار مسجلة حالياً</h4>
                <p class="text-secondary small mb-4" style="max-width: 480px; margin: 0 auto; line-height: 1.6;">
                    @if(request('search') || request('status'))
                        لم يتم العثور على أي نتائج تطابق معايير البحث المحددة. جرب تغيير كلمة البحث أو إعادة ضبط الفلاتر.
                    @else
                        لم يتم إضافة أي أخبار أو تغطيات صحفية حتى الآن. يمكنك البدء بنشر أول خبر للجمهور بضغطة زر.
                    @endif
                </p>
                <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                    @if(request('search') || request('status'))
                        <a href="{{ route('media.news.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="fas fa-undo me-1"></i> إعادة ضبط الفلاتر
                        </a>
                    @endif
                    <a href="{{ route('media.news.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm" style="background: #1d4ed8;">
                        <i class="fas fa-plus-circle me-1"></i> إضافة خبر جديد
                    </a>
                </div>
            </div>
        </div>
    @endif

</div>

<style>
.news-bento-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(0,0,0,0.08) !important;
}
.hover-primary:hover {
    color: #1d4ed8 !important;
}
</style>
@endsection
