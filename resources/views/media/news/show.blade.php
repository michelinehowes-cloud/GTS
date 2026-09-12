@extends('layouts.app')

@section('title', $news->title . ' | وحدة الإعلام')
@section('page-title', 'تفاصيل الخبر الصحفي')

@section('content')
<div class="container-fluid py-4">

    @php
        $isMediaStaff = auth()->check() && in_array(auth()->user()->role, ['media_officer', 'admin']);
        $publicNewsUrl = route('public.news.show', $news);
    @endphp

    <!-- الشريط الترحيبي بالهوية الرسمية للمنظومة -->
    <x-page-hero
        title="تفاصيل الخبر الصحفي"
        subtitle="{{ $isMediaStaff ? 'معاينة كاملة للخبر والتغطية الإعلامية كما تظهر للجمهور' : 'وحدة الإعلام والتوثيق الصحفي — مكتب تدريب الخريجين' }}"
        icon="fas fa-newspaper"
        :breadcrumbs="$isMediaStaff ? [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم الميديا', 'url' => route('media.dashboard')],
            ['label' => 'إدارة الأخبار', 'url' => route('media.news.index')],
            ['label' => \Illuminate\Support\Str::limit($news->title, 35)]
        ] : [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'أخبار المنظومة', 'url' => route('home') . '#news-section'],
            ['label' => \Illuminate\Support\Str::limit($news->title, 35)]
        ]"
        secondaryBadge="{{ $news->status_data['label'] }}"
        secondaryBadgeIcon="{{ $news->status_data['icon'] }}"
    >
        @if($isMediaStaff)
            <a href="{{ $publicNewsUrl }}" target="_blank" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem;" title="فتح رابط الخبر العام للجمهور">
                <i class="fas fa-external-link-alt text-primary"></i>
                <span>عرض الرابط العام للجمهور</span>
            </a>
            <a href="{{ route('media.news.edit', $news) }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem;">
                <i class="fas fa-edit"></i>
                <span>تعديل هذا الخبر</span>
            </a>
            <a href="{{ route('media.news.index') }}" class="btn btn-outline-light text-white fw-bold py-2.5 px-3 rounded-3 d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem;">
                <i class="fas fa-arrow-right"></i>
                <span>لوحة الأخبار</span>
            </a>
        @else
            <a href="{{ route('home') }}" class="btn btn-outline-light text-white fw-bold py-2.5 px-3.5 rounded-3 d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem;">
                <i class="fas fa-home"></i>
                <span>الرئيسية</span>
            </a>
        @endif
    </x-page-hero>

    <div class="row g-4">
        <!-- مقال الخبر الرئيسي -->
        <div class="col-12 col-lg-8">
            <article class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4" style="background: #ffffff;">
                <!-- الغلاف الصحفي البارز -->
                @if($news->thumbnail_url)
                    <div class="position-relative w-100" style="max-height: 420px; overflow: hidden; background: #0f172a;">
                        <img src="{{ $news->thumbnail_url }}" alt="{{ $news->title }}" class="w-100 h-100" style="object-fit: cover; min-height: 260px;">
                    </div>
                @endif

                <div class="card-body p-4 p-md-5">
                    <!-- شارات وتاريخ النشر -->
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge rounded-pill px-3 py-1.5 small {{ $news->status_data['class'] }}" style="font-size: 0.78rem;">
                                <i class="{{ $news->status_data['icon'] }} me-1"></i>{{ $news->status_data['label'] }}
                            </span>
                            <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1.5 small" style="font-size: 0.75rem;">
                                <i class="far fa-clock me-1 text-warning"></i>{{ $news->reading_time }} دقائق قراءة
                            </span>
                        </div>

                        <div class="text-muted small d-flex align-items-center gap-3">
                            <span><i class="far fa-calendar-alt text-primary me-1"></i>{{ $news->published_at ? $news->published_at->translatedFormat('l، d F Y - h:i A') : 'غير محدد' }}</span>
                        </div>
                    </div>

                    <!-- العنوان الرئيسي -->
                    <h2 class="fw-bold text-dark mb-4" style="line-height: 1.4; font-size: 1.65rem;">
                        {{ $news->title }}
                    </h2>

                    <!-- بطاقة الكاتب والناشر -->
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 44px; height: 44px; font-size: 1.1rem;">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div>
                            <span class="d-block fw-bold text-dark small">{{ $news->creator->name ?? 'وحدة الإعلام والتواصل' }}</span>
                            <small class="text-muted" style="font-size: 0.75rem;">جامعة طرابلس • مكتب تأهيل وتدريب الخريجين</small>
                        </div>
                    </div>

                    <!-- المحتوى النصي الكامل للخبر -->
                    <div class="article-content text-dark mb-4" style="line-height: 2; font-size: 1.05rem; font-family: system-ui, -apple-system, sans-serif;">
                        {!! nl2br(e($news->content)) !!}
                    </div>

                    <!-- شريط المشاركة والإجراءات السفلية -->
                    <div class="pt-4 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="small fw-bold text-muted me-1">مشاركة الخبر:</span>
                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 shadow-sm d-flex align-items-center gap-1.5" onclick="copyNewsLink('{{ $publicNewsUrl }}')">
                                <i class="fas fa-link me-1"></i> نسخ الرابط العام للنشر
                            </button>
                            <a href="https://api.whatsapp.com/send?text={{ urlencode($news->title . ' - ' . $publicNewsUrl) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1.5 d-flex align-items-center gap-1.5" title="مشاركة عبر واتساب">
                                <i class="fab fa-whatsapp"></i> واتساب
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($publicNewsUrl) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 d-flex align-items-center gap-1.5" title="مشاركة عبر فيسبوك">
                                <i class="fab fa-facebook"></i> فيسبوك
                            </a>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            @if($isMediaStaff)
                                <a href="{{ route('media.news.edit', $news) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3 py-1.5 fw-bold">
                                    <i class="fas fa-edit me-1"></i> تعديل
                                </a>
                                <a href="{{ route('media.news.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5">
                                    <i class="fas fa-arrow-left me-1"></i> قائمة الأخبار
                                </a>
                            @else
                                <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5">
                                    <i class="fas fa-home me-1"></i> الصفحة الرئيسية
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <!-- العمود الجانبي -->
        <div class="col-12 col-lg-4">
            <!-- بطاقة التحكم السريع بالخبر -->
            <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: #ffffff;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <i class="fas fa-cogs text-primary"></i>
                        <h6 class="fw-bold mb-0 text-dark">بيانات وإجراءات الخبر</h6>
                    </div>

                    <ul class="list-unstyled mb-4 small" style="line-height: 2.2;">
                        <li class="d-flex justify-content-between border-bottom py-1">
                            <span class="text-muted">حالة الظهور:</span>
                            <span class="fw-bold {{ $news->is_active ? 'text-success' : 'text-secondary' }}">
                                {{ $news->is_active ? '🟢 نشط ومعروض' : '⚪ مسودة معطلة' }}
                            </span>
                        </li>
                        <li class="d-flex justify-content-between border-bottom py-1">
                            <span class="text-muted">تاريخ الإنشاء:</span>
                            <span class="fw-semibold text-dark">{{ $news->created_at ? $news->created_at->format('Y-m-d H:i') : '-' }}</span>
                        </li>
                        <li class="d-flex justify-content-between border-bottom py-1">
                            <span class="text-muted">تاريخ النشر:</span>
                            <span class="fw-semibold text-dark">{{ $news->published_at ? $news->published_at->format('Y-m-d H:i') : 'فوري' }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-1">
                            <span class="text-muted">الناشر:</span>
                            <span class="fw-semibold text-dark">{{ $news->creator->name ?? 'غير محدد' }}</span>
                        </li>
                    </ul>

                    @if($isMediaStaff)
                        <!-- صندوق رابط النشر العام المباشر -->
                        <div class="mb-4 p-3 rounded-3" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                            <div class="d-flex align-items-center justify-content-between mb-1.5">
                                <span class="small fw-bold text-success"><i class="fas fa-globe me-1"></i>رابط النشر للجمهور:</span>
                                <span class="badge bg-success small" style="font-size: 0.68rem;">متاح للجميع</span>
                            </div>
                            <p class="text-muted small mb-2" style="font-size: 0.75rem; line-height: 1.5;">هذا هو الرابط المباشر العام الذي يراه أي شخص والطلاب دون الحاجة لتسجيل دخول.</p>
                            <div class="input-group input-group-sm mb-2">
                                <input type="text" class="form-control form-control-sm bg-white text-dark font-monospace" style="font-size: 0.78rem; direction: ltr; text-align: left;" value="{{ $publicNewsUrl }}" id="publicNewsUrlInput" readonly onclick="this.select()">
                                <button class="btn btn-success" type="button" onclick="copyNewsLink('{{ $publicNewsUrl }}')" title="نسخ الرابط العام">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <a href="{{ $publicNewsUrl }}" target="_blank" class="small text-success fw-bold text-decoration-none d-flex align-items-center gap-1">
                                    <i class="fas fa-external-link-alt"></i>
                                    <span>فتح الرابط كزائر</span>
                                </a>
                                <small class="text-muted font-monospace" style="font-size: 0.72rem;">/news/{{ $news->id }}</small>
                            </div>
                        </div>
                    @endif

                    @if(auth()->check() && in_array(auth()->user()->role, ['media_officer', 'admin']))
                        <!-- تبديل الحالة بنقرة واحدة -->
                        <form action="{{ route('media.news.toggle-status', $news) }}" method="POST" class="mb-2">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-light border w-100 py-2 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2">
                                <i class="fas {{ $news->is_active ? 'fa-eye-slash text-warning' : 'fa-check-circle text-success' }}"></i>
                                <span>{{ $news->is_active ? 'إلغاء تفعيل الخبر (تحويل لمسودة)' : 'تفعيل ونشر الخبر للجمهور' }}</span>
                            </button>
                        </form>

                        <!-- تعديل الخبر -->
                        <a href="{{ route('media.news.edit', $news) }}" class="btn btn-sm btn-primary w-100 py-2 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2 mb-2" style="background: #1d4ed8;">
                            <i class="fas fa-edit"></i>
                            <span>تعديل محتوى الخبر</span>
                        </a>

                        <!-- حذف الخبر -->
                        <form action="{{ route('media.news.destroy', $news) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الخبر نهائياً؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger w-100 py-2 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2">
                                <i class="fas fa-trash-alt"></i>
                                <span>حذف الخبر</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- أحدث الأخبار الأخرى -->
            @if(isset($relatedNews) && $relatedNews->count() > 0)
                <div class="card border-0 rounded-4 shadow-sm" style="background: #ffffff;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                            <i class="fas fa-newspaper text-primary"></i>
                            <h6 class="fw-bold mb-0 text-dark">أخبار وتغطيات أخرى</h6>
                        </div>

                        <div class="d-flex flex-column gap-3">
                            @foreach($relatedNews as $other)
                                <a href="{{ route('media.news.show', $other) }}" class="text-decoration-none text-dark d-flex gap-3 align-items-center p-2 rounded-3 hover-bg-light" style="transition: background 0.2s;">
                                    <div class="rounded-3 overflow-hidden flex-shrink-0" style="width: 58px; height: 58px; background: #0f172a;">
                                        @if($other->thumbnail_url)
                                            <img src="{{ $other->thumbnail_url }}" alt="{{ $other->title }}" class="w-100 h-100" style="object-fit: cover;">
                                        @else
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-white-50">
                                                <i class="fas fa-newspaper"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="fw-bold small mb-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                                            {{ $other->title }}
                                        </h6>
                                        <small class="text-muted" style="font-size: 0.72rem;">
                                            <i class="far fa-calendar-alt me-1"></i>{{ $other->published_at ? $other->published_at->diffForHumans() : 'مسودة' }}
                                        </small>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>

<style>
.hover-bg-light:hover {
    background: #f1f5f9;
}
.article-content p {
    margin-bottom: 1.5rem;
}
</style>

<script>
function copyNewsLink(url) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(function() {
            showCopyAlert(url);
        }).catch(function() {
            fallbackCopy(url);
        });
    } else {
        fallbackCopy(url);
    }
}

function fallbackCopy(url) {
    var input = document.createElement('input');
    input.value = url;
    document.body.appendChild(input);
    input.select();
    document.execCommand('copy');
    document.body.removeChild(input);
    showCopyAlert(url);
}

function showCopyAlert(url) {
    alert('تم نسخ الرابط العام للخبر بنجاح:\n\n' + url + '\n\nيمكنك الآن مشاركته في وسائل التواصل أو إرساله لأي شخص ليفتحه مباشرة دون الحاجة لتسجيل دخول.');
}
</script>
@endsection
