@extends('layouts.app')

@section('title', $announcement->title . ' | تفاصيل الإعلان')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4" dir="rtl">

    @php
        $isMediaStaff = auth()->check() && in_array(auth()->user()->role, ['media_officer', 'admin']);
        $publicAnnouncementUrl = route('public.announcements.show', $announcement);
    @endphp

    <!-- Hero Header -->
    <x-page-hero
        title="تفاصيل الإعلان الرسمي"
        description="{{ $isMediaStaff ? 'معاينة كاملة لنص وبيانات الإعلان وفترة الصلاحية كما تظهر للمستفيدين' : 'وحدة الإعلام والإعلانات الرسمية — مكتب تدريب الخريجين' }}"
        icon="fas fa-bullhorn"
        :breadcrumbs="$isMediaStaff ? [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم الميديا', 'url' => route('media.dashboard')],
            ['label' => 'إدارة الإعلانات', 'url' => route('media.announcements.index')],
            ['label' => \Illuminate\Support\Str::limit($announcement->title, 35)]
        ] : [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'الإعلانات والتعميمات', 'url' => route('home') . '#announcements-section'],
            ['label' => \Illuminate\Support\Str::limit($announcement->title, 35)]
        ]"
        secondaryBadge="{{ $announcement->status_data['label'] }}"
        secondaryBadgeIcon="{{ $announcement->status_data['icon'] }}"
    >
        @if($isMediaStaff)
            <a href="{{ $publicAnnouncementUrl }}" target="_blank" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem;" title="فتح رابط الإعلان العام للجمهور">
                <i class="fas fa-external-link-alt text-primary"></i>
                <span>عرض الرابط العام للجمهور</span>
            </a>
            <a href="{{ route('media.announcements.edit', $announcement) }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem;">
                <i class="fas fa-edit fs-6"></i>
                <span>تعديل هذا الإعلان</span>
            </a>
            <a href="{{ route('media.announcements.index') }}" class="btn btn-outline-light text-white fw-bold py-2.5 px-3 rounded-3 d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem;">
                <i class="fas fa-arrow-right fs-6"></i>
                <span>لوحة الإعلانات</span>
            </a>
        @else
            <a href="{{ route('home') }}" class="btn btn-outline-light text-white fw-bold py-2.5 px-3.5 rounded-3 d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem;">
                <i class="fas fa-home fs-6"></i>
                <span>الرئيسية</span>
            </a>
        @endif
    </x-page-hero>

    <!-- تنبيهات النظام -->
    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 shadow-sm py-3 px-4 d-flex align-items-center gap-3 mb-4" role="alert" style="background: rgba(16, 185, 129, 0.1); color: #065f46;">
            <i class="fas fa-check-circle fs-5 text-success"></i>
            <span class="fw-semibold">{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- المحتوى الرئيسي للإعلان (8 أعمدة) -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: #ffffff;">
                <div class="card-body p-4 p-md-5">

                    <!-- شريط الحالة والفترة -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pb-3 mb-4 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge rounded-pill px-3 py-1.5 fw-semibold {{ $announcement->status_data['class'] }}" style="font-size: 0.82rem;">
                                <i class="{{ $announcement->status_data['icon'] }} me-1"></i>{{ $announcement->status_data['label'] }}
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted small" style="font-size: 0.82rem;">
                            <i class="far fa-calendar-alt text-primary"></i>
                            <span>ساري من <strong>{{ $announcement->start_date ? $announcement->start_date->format('Y-m-d') : '—' }}</strong> إلى <strong>{{ $announcement->end_date ? $announcement->end_date->format('Y-m-d') : '—' }}</strong></span>
                        </div>
                    </div>

                    <!-- عنوان الإعلان -->
                    <h2 class="fw-bold text-dark mb-4" style="line-height: 1.45; font-size: 1.75rem;">
                        {{ $announcement->title }}
                    </h2>

                    <!-- بطاقة معلومات الناشر -->
                    <div class="p-3 rounded-4 mb-4 d-flex align-items-center gap-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 44px; height: 44px; background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 0.92rem;">{{ $announcement->creator->name ?? 'المكتب الإعلامي' }}</div>
                            <small class="text-muted" style="font-size: 0.78rem;">جامعة طرابلس • منظومة تأهيل الخريجين</small>
                        </div>
                        <div class="ms-auto text-muted small d-none d-sm-block" style="font-size: 0.78rem;">
                            نُشر بتاريخ: {{ $announcement->created_at->translatedFormat('d M Y - H:i') }}
                        </div>
                    </div>

                    <!-- نص الإعلان -->
                    <div class="announcement-content text-dark mb-4 py-2" style="font-size: 1.05rem; line-height: 1.85; white-space: pre-line;">
                        {!! nl2br(e($announcement->content)) !!}
                    </div>

                    <!-- رابط إضافي / زر الإجراء إن وجد -->
                    @if($announcement->link)
                        <div class="p-4 rounded-4 mb-4 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3" style="background: linear-gradient(135deg, rgba(37, 99, 235, 0.06) 0%, rgba(30, 58, 138, 0.06) 100%); border: 1px dashed rgba(37, 99, 235, 0.3);">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 40px; height: 40px; background: rgba(37, 99, 235, 0.12);">
                                    <i class="fas fa-link fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 0.95rem;">رابط خارجي متعلق بالإعلان</div>
                                    <small class="text-muted text-break" style="font-size: 0.8rem;">{{ $announcement->link }}</small>
                                </div>
                            </div>
                            <a href="{{ $announcement->link }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary fw-bold px-4 py-2.5 rounded-3 shadow-sm d-inline-flex align-items-center justify-content-center gap-2 text-nowrap" style="background: #1d4ed8;">
                                <span>زيارة الرابط</span>
                                <i class="fas fa-external-link-alt small"></i>
                            </a>
                        </div>
                    @endif

                    <!-- شريط الإجراءات السريعة ومشاركة الرابط -->
                    <div class="pt-4 border-top d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="text-muted small">مشاركة الإعلان:</span>
                            <button onclick="copyAnnouncementLink('{{ $publicAnnouncementUrl }}')" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-1.5">
                                <i class="fas fa-share-alt me-1"></i>نسخ الرابط العام للنشر
                            </button>
                            <a href="https://api.whatsapp.com/send?text={{ urlencode($announcement->title . ' - ' . $publicAnnouncementUrl) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3 d-flex align-items-center gap-1.5" title="مشاركة عبر واتساب">
                                <i class="fab fa-whatsapp"></i> واتساب
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($publicAnnouncementUrl) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 d-flex align-items-center gap-1.5" title="مشاركة عبر فيسبوك">
                                <i class="fab fa-facebook"></i> فيسبوك
                            </a>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            @if($isMediaStaff)
                                <a href="{{ route('media.announcements.edit', $announcement) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="fas fa-edit me-1"></i>تعديل
                                </a>
                                <a href="{{ route('media.announcements.index') }}" class="btn btn-sm btn-secondary rounded-pill px-3">
                                    <i class="fas fa-list me-1"></i>قائمة الإعلانات
                                </a>
                            @else
                                <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                    <i class="fas fa-home me-1"></i>الصفحة الرئيسية
                                </a>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- الشريط الجانبي لمعلومات وإجراءات الإعلان (4 أعمدة) -->
        <div class="col-12 col-lg-4">
            <!-- بطاقة التحكم والبيانات -->
            <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: #ffffff;">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                        <i class="fas fa-cog text-primary"></i>
                        <span>بيانات وإجراءات الإعلان</span>
                    </h6>

                    <ul class="list-unstyled mb-4" style="font-size: 0.88rem;">
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">حالة الظهور:</span>
                            <span>
                                @if($announcement->is_active)
                                    <span class="text-success fw-bold"><i class="fas fa-circle small text-success me-1"></i>مفعل ومعروض</span>
                                @else
                                    <span class="text-secondary fw-bold"><i class="fas fa-circle small text-secondary me-1"></i>معطل (مسودة)</span>
                                @endif
                            </span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">تاريخ البدء:</span>
                            <span class="fw-semibold text-dark">{{ $announcement->start_date ? $announcement->start_date->format('Y-m-d') : 'غير محدد' }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">تاريخ الانتهاء:</span>
                            <span class="fw-semibold text-dark">{{ $announcement->end_date ? $announcement->end_date->format('Y-m-d') : 'غير محدد' }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">تاريخ الإنشاء:</span>
                            <span class="fw-semibold text-dark">{{ $announcement->created_at->format('Y-m-d') }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2">
                            <span class="text-muted">الناشر:</span>
                            <span class="fw-semibold text-dark">{{ $announcement->creator->name ?? 'المسؤول' }}</span>
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
                                <input type="text" class="form-control form-control-sm bg-white text-dark font-monospace" style="font-size: 0.78rem; direction: ltr; text-align: left;" value="{{ $publicAnnouncementUrl }}" readonly onclick="this.select()">
                                <button class="btn btn-success" type="button" onclick="copyAnnouncementLink('{{ $publicAnnouncementUrl }}')" title="نسخ الرابط العام">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <a href="{{ $publicAnnouncementUrl }}" target="_blank" class="small text-success fw-bold text-decoration-none d-flex align-items-center gap-1">
                                    <i class="fas fa-external-link-alt"></i>
                                    <span>فتح الرابط كزائر</span>
                                </a>
                                <small class="text-muted font-monospace" style="font-size: 0.72rem;">/announcements/{{ $announcement->id }}</small>
                            </div>
                        </div>
                    @endif

                    @if(auth()->check() && in_array(auth()->user()->role, ['media_officer', 'admin']))
                        <!-- أزرار الإجراءات -->
                        <div class="d-flex flex-column gap-2">
                            <form action="{{ route('media.announcements.toggle-status', $announcement) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn w-100 py-2.5 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2 {{ $announcement->is_active ? 'btn-outline-warning text-dark' : 'btn-outline-success' }}">
                                    <i class="fas {{ $announcement->is_active ? 'fa-eye-slash' : 'fa-check' }}"></i>
                                    <span>{{ $announcement->is_active ? 'إلغاء تفعيل الإعلان (تحويل لمسودة)' : 'تفعيل ونشر الإعلان فوراً' }}</span>
                                </button>
                            </form>

                            <a href="{{ route('media.announcements.edit', $announcement) }}" class="btn btn-primary w-100 py-2.5 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm" style="background: #1d4ed8;">
                                <i class="fas fa-edit"></i>
                                <span>تعديل محتوى الإعلان</span>
                            </a>

                            <form action="{{ route('media.announcements.destroy', $announcement) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذا الإعلان نهائياً؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100 py-2.5 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2">
                                    <i class="fas fa-trash-alt"></i>
                                    <span>حذف الإعلان</span>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- إعلانات أخرى حديثة -->
            @if(isset($recentAnnouncements) && $recentAnnouncements->count() > 0)
                <div class="card border-0 shadow-sm rounded-4" style="background: #ffffff;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                            <i class="fas fa-bullhorn text-warning"></i>
                            <span>إعلانات أخرى</span>
                        </h6>
                        <div class="d-flex flex-column gap-3">
                            @foreach($recentAnnouncements as $item)
                                <a href="{{ route('media.announcements.show', $item) }}" class="text-decoration-none p-2 rounded-3 hover-bg-light transition d-block" style="border: 1px solid #f1f5f9;">
                                    <div class="fw-bold text-dark small mb-1 text-truncate">{{ $item->title }}</div>
                                    <div class="d-flex align-items-center justify-content-between text-muted" style="font-size: 0.72rem;">
                                        <span><i class="far fa-calendar-alt me-1"></i>{{ $item->created_at->format('Y-m-d') }}</span>
                                        <span class="badge rounded-pill {{ $item->status_data['class'] }}" style="font-size: 0.68rem;">{{ $item->status_data['label'] }}</span>
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

<script>
function copyAnnouncementLink(url) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(function() {
            showAnnounceAlert(url);
        }).catch(function() {
            fallbackAnnounceCopy(url);
        });
    } else {
        fallbackAnnounceCopy(url);
    }
}

function fallbackAnnounceCopy(url) {
    var input = document.createElement('input');
    input.value = url;
    document.body.appendChild(input);
    input.select();
    document.execCommand('copy');
    document.body.removeChild(input);
    showAnnounceAlert(url);
}

function showAnnounceAlert(url) {
    alert('تم نسخ الرابط العام للإعلان بنجاح:\n\n' + url + '\n\nيمكنك الآن مشاركته في وسائل التواصل أو إرساله لأي شخص ليفتحه مباشرة دون الحاجة لتسجيل دخول.');
}
</script>
@endsection
