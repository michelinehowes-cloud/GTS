@extends('layouts.app')

@section('title', 'إدارة الإعلانات والتنبيهات | وحدة الإعلام')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4" dir="rtl">

    <!-- Hero Header بالهوية المعتمدة -->
    <x-page-hero
        title="وحدة الإعلام وإدارة الإعلانات"
        description="نشر وجدولة الإعلانات الرسمية والتنبيهات الموجهة للطلاب والخريجين والشركاء مع التنبيه الفوري"
        icon="fas fa-bullhorn"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم الميديا', 'url' => route('media.dashboard')],
            ['label' => 'إدارة الإعلانات']
        ]"
        secondaryBadge="{{ $stats['total'] }} إعلان مسجل"
        secondaryBadgeIcon="fas fa-bullhorn"
    >
        <a href="{{ route('media.announcements.create') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-plus-circle fs-6"></i>
            <span>+ إضافة إعلان جديد</span>
        </a>
        <a href="{{ route('media.news.index') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem;">
            <i class="fas fa-newspaper fs-6 text-primary"></i>
            <span>إدارة الأخبار</span>
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
            'title' => 'إجمالي الإعلانات',
            'value' => $stats['total'],
            'icon' => 'fas fa-bullhorn',
            'color' => 'primary',
            'description' => 'جميع التنبيهات المسجلة'
        ])

        @include('components.stat-card', [
            'col' => 'col-6 col-lg-3',
            'title' => 'إعلانات سارية',
            'value' => $stats['active'],
            'icon' => 'fas fa-check-circle',
            'color' => 'success',
            'badge' => 'ساري حالياً',
            'badgeClass' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
            'description' => 'معروضة للمستفيدين حالياً'
        ])

        @include('components.stat-card', [
            'col' => 'col-6 col-lg-3',
            'title' => 'إعلانات مجدولة',
            'value' => $stats['upcoming'],
            'icon' => 'fas fa-calendar-plus',
            'color' => 'warning',
            'badge' => 'مجدول',
            'badgeClass' => 'bg-warning bg-opacity-15 text-dark border border-warning border-opacity-25',
            'description' => 'ستبدأ تلقائياً في موعدها'
        ])

        @include('components.stat-card', [
            'col' => 'col-6 col-lg-3',
            'title' => 'إعلانات منتهية',
            'value' => $stats['expired'],
            'icon' => 'fas fa-history',
            'color' => 'secondary',
            'badge' => 'أرشيف',
            'badgeClass' => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25',
            'description' => 'انتهت فترة نشرها'
        ])
    </div>

    <!-- بطاقة البحث والتصفية المتقدمة -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: #ffffff;">
        <div class="card-body p-4">
            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="fas fa-filter text-primary"></i>
                <span>البحث وتصفية الإعلانات</span>
            </h6>
            <form method="GET" action="{{ route('media.announcements.index') }}" class="row g-3">
                <div class="col-12 col-md-5">
                    <label class="form-label small fw-bold text-dark mb-1.5">
                        <i class="fas fa-search text-primary me-1"></i> كلمة البحث
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0 ps-0"
                               placeholder="ابحث في عنوان الإعلان أو نصه..."
                               value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label small fw-bold text-dark mb-1.5">
                        <i class="fas fa-tag text-primary me-1"></i> حالة التفعيل
                    </label>
                    <select name="is_active" class="form-select bg-light rounded-3">
                        <option value="">جميع الحالات</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>🟢 مفعل (نشط)</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>⚪ معطل (مسودة)</option>
                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label small fw-bold text-dark mb-1.5">
                        <i class="far fa-clock text-primary me-1"></i> الفترة الزمنية
                    </label>
                    <select name="period" class="form-select bg-light rounded-3">
                        <option value="">جميع الفترات</option>
                        <option value="current" {{ request('period') === 'current' ? 'selected' : '' }}>ساري حالياً</option>
                        <option value="upcoming" {{ request('period') === 'upcoming' ? 'selected' : '' }}>قادم</option>
                        <option value="expired" {{ request('period') === 'expired' ? 'selected' : '' }}>منتهي</option>
                    </select>
                </div>

                <div class="col-12 col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1 py-2.5 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm" style="background: #1d4ed8;">
                        <i class="fas fa-search"></i>
                        <span>بحث</span>
                    </button>
                    @if(request()->anyFilled(['search', 'is_active', 'period']))
                        <a href="{{ route('media.announcements.index') }}" class="btn btn-outline-secondary py-2.5 px-3 rounded-3" title="إلغاء الفلاتر">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- شبكة بطاقات الإعلانات -->
    <div class="d-flex align-items-center justify-content-between mb-3 px-1">
        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="fas fa-bullhorn text-primary"></i>
            <span>قائمة الإعلانات المنشورة</span>
            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-2.5 py-1" style="font-size: 0.75rem;">{{ $announcements->total() }}</span>
        </h5>
        <a href="{{ route('media.announcements.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm d-none d-sm-inline-flex align-items-center gap-1.5" style="background: #1d4ed8;">
            <i class="fas fa-plus"></i>
            <span>إضافة إعلان جديد</span>
        </a>
    </div>

    @if($announcements->count() > 0)
        <div class="row g-4 mb-4">
            @foreach($announcements as $item)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm h-100 overflow-hidden d-flex flex-column" style="background: #ffffff; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                        
                        <!-- شريط رأس بطاقة الإعلان -->
                        <div class="p-4 pb-0">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="badge rounded-pill px-2.5 py-1.5 small {{ $item->status_data['class'] }}" style="font-size: 0.75rem;">
                                    <i class="{{ $item->status_data['icon'] }} me-1"></i>{{ $item->status_data['label'] }}
                                </span>
                                <small class="text-muted" style="font-size: 0.75rem;">
                                    <i class="far fa-user me-1 text-secondary"></i>{{ $item->creator->name ?? 'المكتب الإعلامي' }}
                                </small>
                            </div>

                            <h5 class="fw-bold text-dark mb-2" style="line-height: 1.45; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="{{ $item->title }}">
                                <a href="{{ route('media.announcements.show', $item) }}" class="text-dark text-decoration-none hover-primary">
                                    {{ $item->title }}
                                </a>
                            </h5>

                            <div class="d-flex align-items-center gap-2 text-muted small mb-3 p-2 rounded-3" style="background: #f8fafc; font-size: 0.75rem;">
                                <i class="far fa-calendar-alt text-primary"></i>
                                <span>الفترة: {{ $item->start_date ? $item->start_date->format('Y-m-d') : '—' }} إلى {{ $item->end_date ? $item->end_date->format('Y-m-d') : '—' }}</span>
                            </div>

                            <p class="text-secondary small mb-3" style="line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $item->excerpt }}
                            </p>
                        </div>

                        <!-- تذييل البطاقة والإجراءات -->
                        <div class="p-4 pt-3 mt-auto border-top d-flex align-items-center justify-content-between gap-2">
                            <div class="d-flex align-items-center gap-1">
                                <a href="{{ route('media.announcements.show', $item) }}" class="btn btn-sm btn-outline-primary rounded-3 px-2.5" title="معاينة الإعلان">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('media.announcements.edit', $item) }}" class="btn btn-sm btn-outline-warning text-dark rounded-3 px-2.5" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-info rounded-3 px-2.5" title="نسخ رابط النشر العام للمشاركة" onclick="navigator.clipboard.writeText('{{ route('public.announcements.show', $item) }}'); alert('تم نسخ رابط الإعلان العام للنشر بنجاح:\n{{ route('public.announcements.show', $item) }}\n\nيمكنك الآن مشاركته مع الجميع مباشرة دون تسجيل دخول.');">
                                    <i class="fas fa-share-alt"></i>
                                </button>
                                <form action="{{ route('media.announcements.toggle-status', $item) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $item->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }} rounded-3 px-2.5" title="{{ $item->is_active ? 'إلغاء التفعيل' : 'تفعيل ونشر' }}">
                                        <i class="fas {{ $item->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                    </button>
                                </form>
                            </div>

                            <form action="{{ route('media.announcements.destroy', $item) }}" method="POST" onsubmit="return confirm('هل تريد بالتأكيد حذف هذا الإعلان؟');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger border-0 rounded-3 px-2" title="حذف">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        @if($announcements->hasPages())
            <div class="d-flex justify-content-center my-4">
                {{ $announcements->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <!-- الحالة الفارغة -->
        <div class="card border-0 rounded-4 shadow-sm p-5 text-center my-4" style="background: #ffffff;">
            <div class="py-4">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-primary mb-3" style="width: 76px; height: 76px; background: rgba(37, 99, 235, 0.08);">
                    <i class="fas fa-bullhorn fa-2x"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">لا توجد إعلانات مسجلة حالياً</h5>
                <p class="text-secondary small max-w-md mx-auto mb-4" style="max-width: 480px;">
                    لم يتم العثور على أي إعلانات تطابق معايير البحث الحالية، أو لم يتم إنشاء إعلانات بعد. ابدأ بنشر أول إعلان ليصل لجميع رواد المنصة فوراً.
                </p>
                <a href="{{ route('media.announcements.create') }}" class="btn btn-primary px-4 py-2.5 rounded-3 fw-bold shadow-sm" style="background: #1d4ed8;">
                    <i class="fas fa-plus-circle me-1"></i>
                    <span>+ إنشاء إعلان جديد الآن</span>
                </a>
            </div>
        </div>
    @endif

</div>
@endsection
