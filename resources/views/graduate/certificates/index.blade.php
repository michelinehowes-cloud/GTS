@extends('layouts.app')

@section('title', 'شهاداتي المعتمدة')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #0369a1 100%);">
                <div class="card-body p-4 p-md-5 text-white position-relative">
                    <!-- Mobile Header Top Bar (Brand + Menu Trigger) -->
                    <div class="d-flex justify-content-between align-items-center w-100 mb-3 d-md-none">
                        <span class="badge rounded-pill px-3 py-1.5 small" style="background: rgba(255, 255, 255, 0.22) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.3) !important; font-size: 0.75rem; backdrop-filter: blur(6px);">
                            <i class="fas fa-certificate me-1 text-warning"></i>شهادات المتدرب
                        </span>
                        <div class="d-flex align-items-center gap-2">
                            <!-- زر الرسائل المنزلق بالموبايل -->
                            <button type="button" class="btn btn-sm btn-light bg-white bg-opacity-25 text-white rounded-circle d-flex align-items-center justify-content-center border-0 position-relative shadow-none" onclick="openMessagesDrawer()" style="width: 36px; height: 36px;" title="الرسائل">
                                <i class="fas fa-envelope"></i>
                                @php
                                    $unreadMsgs = \App\Models\Message::where('receiver_id', auth()->id())->whereNull('read_at')->count();
                                @endphp
                                @if($unreadMsgs > 0)
                                    <span class="position-absolute bg-danger rounded-circle border border-2 border-white" style="width: 10px; height: 10px; top: 0px; right: 0px;"></span>
                                @endif
                            </button>
                            <!-- زر الإشعارات المنزلق بالموبايل -->
                            <button type="button" class="btn btn-sm btn-light bg-white bg-opacity-25 text-white rounded-circle d-flex align-items-center justify-content-center border-0 position-relative shadow-none" onclick="openNotificationsDrawer()" style="width: 36px; height: 36px;" title="الإشعارات">
                                <i class="fas fa-bell"></i>
                                @php
                                    $unreadNotifs = \App\Models\Notification::forUser(auth()->id())->unread()->count();
                                @endphp
                                @if($unreadNotifs > 0)
                                    <span class="position-absolute bg-danger rounded-circle border border-2 border-white" style="width: 10px; height: 10px; top: 0px; right: 0px;"></span>
                                @endif
                            </button>
                            <!-- زر القائمة الجانبية للموبايل -->
                            <button type="button" class="btn btn-sm btn-light bg-white bg-opacity-25 text-white rounded-circle d-flex align-items-center justify-content-center border-0 shadow-none" style="width: 36px; height: 36px;" onclick="window.toggleSidebarFunc ? window.toggleSidebarFunc() : null">
                                <i class="fas fa-bars"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill bg-white bg-opacity-10 border border-white border-opacity-20 mb-3">
                                <i class="fas fa-certificate text-warning"></i>
                                <span class="small fw-semibold">سجل الإنجاز والشهادات الرسمية المعتمدة</span>
                            </div>
                            <h1 class="h2 fw-bold mb-2">شهادات المتدرب الخريج</h1>
                            <p class="mb-0 text-white-50 fs-6">
                                استعرض وتنزيل واطبع شهاداتك المعتمدة الصادرة عن مكتب تدريب الخريجين بجامعة طرابلس بالشراكة مع نخبة المؤسسات والشركات.
                            </p>
                        </div>
                        <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                            <div class="d-flex align-items-center justify-content-end gap-3 flex-wrap">
                                <!-- Desktop Notifications and Messages -->
                                <div class="d-none d-md-flex gap-2 align-items-center mb-3 mb-lg-0 me-0 me-lg-3">
                                    <button class="btn btn-light bg-white bg-opacity-25 text-white border-0 px-3 py-2 rounded-pill fw-bold" onclick="openMessagesDrawer()">
                                        <i class="fas fa-envelope me-1"></i>الرسائل
                                        @php
                                            $unreadMsgsDesktop = \App\Models\Message::where('receiver_id', auth()->id())->whereNull('read_at')->count();
                                        @endphp
                                        @if($unreadMsgsDesktop > 0)
                                            <span class="badge bg-danger ms-1 rounded-pill">{{ $unreadMsgsDesktop }}</span>
                                        @endif
                                    </button>
                                    <button class="btn btn-light bg-white bg-opacity-25 text-white border-0 px-3 py-2 rounded-pill fw-bold" onclick="openNotificationsDrawer()">
                                        <i class="fas fa-bell me-1"></i>الإشعارات
                                        @php
                                            $unreadNotifsDesktop = \App\Models\Notification::forUser(auth()->id())->unread()->count();
                                        @endphp
                                        @if($unreadNotifsDesktop > 0)
                                            <span class="badge bg-danger ms-1 rounded-pill">{{ $unreadNotifsDesktop }}</span>
                                        @endif
                                    </button>
                                </div>

                                <div class="d-inline-flex align-items-center gap-3 bg-white bg-opacity-10 p-3 rounded-4 backdrop-blur border border-white border-opacity-20">
                                    <div class="text-center px-3 border-end border-white border-opacity-20">
                                        <div class="h3 fw-bold mb-0 text-warning">{{ $stats['total'] ?? 0 }}</div>
                                        <small class="text-white-50">شهادة صادرة</small>
                                    </div>
                                    <div class="text-center px-3">
                                        <div class="h3 fw-bold mb-0 text-info">{{ $stats['total_hours'] ?? 0 }}</div>
                                        <small class="text-white-50">ساعة تدريبية</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards (Bento) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">شهادات التدريبات</span>
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1">برامج مكثفة</span>
                </div>
                <div class="h3 fw-bold text-dark mb-0">{{ $stats['training'] ?? 0 }}</div>
                <small class="text-muted mt-1"><i class="fas fa-chalkboard-teacher me-1 text-primary"></i> حضور تدريبات تخصصية</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">شهادات ورش العمل</span>
                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2.5 py-1">مهارات تفاعلية</span>
                </div>
                <div class="h3 fw-bold text-dark mb-0">{{ $stats['workshop'] ?? 0 }}</div>
                <small class="text-muted mt-1"><i class="fas fa-lightbulb me-1 text-info"></i> مشاركة في ورش مهنية</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">شهادات تدريب تعاوني</span>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1">شراكات عمل</span>
                </div>
                <div class="h3 fw-bold text-dark mb-0">{{ $stats['cooperative'] ?? 0 }}</div>
                <small class="text-muted mt-1"><i class="fas fa-handshake me-1 text-success"></i> بالتعاون مع الشركات</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">حالة الاعتماد</span>
                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2.5 py-1">رسمي QR</span>
                </div>
                <div class="h3 fw-bold text-dark mb-0">100%</div>
                <small class="text-muted mt-1"><i class="fas fa-qrcode me-1 text-warning"></i> قابلة للتحقق الفوري</small>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-3">
            <form action="{{ route('graduate.certificates.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0 ps-3">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control bg-light border-0 py-2" placeholder="ابحث باسم الشهادة، الكود، أو اسم الشركة الشريكة...">
                    </div>
                </div>

                <div class="col-lg-4">
                    <select name="type" class="form-select bg-light border-0 py-2" onchange="this.form.submit()">
                        <option value="">جميع أنواع الشهادات</option>
                        <option value="training_attendance" {{ request('type') == 'training_attendance' ? 'selected' : '' }}>شهادة حضور تدريبات</option>
                        <option value="workshop_attendance" {{ request('type') == 'workshop_attendance' ? 'selected' : '' }}>شهادة حضور ورش عمل</option>
                        <option value="cooperative_attendance" {{ request('type') == 'cooperative_attendance' ? 'selected' : '' }}>شهادة حضور تعاونية (مع الشركات)</option>
                    </select>
                </div>

                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 flex-grow-1 fw-bold">
                        <i class="fas fa-filter me-1"></i> تصفية
                    </button>
                    @if(request()->hasAny(['type', 'search']))
                        <a href="{{ route('graduate.certificates.index') }}" class="btn btn-light rounded-pill px-3" title="إعادة تعيين">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Certificates Grid -->
    @if($certificates->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center p-5 bg-white">
            <div class="text-muted mb-3">
                <i class="fas fa-award fa-4x opacity-25"></i>
            </div>
            <h4 class="fw-bold text-dark mb-2">لا توجد شهادات مطابقة</h4>
            <p class="text-muted mb-4 max-w-md mx-auto">
                لم يتم العثور على شهادات معتمدة وفقاً لمعايير البحث الحالية. يتم إصدار الشهادات فور إتمام البرامج التدريبية وورش العمل بنجاح.
            </p>
            <div>
                <a href="{{ route('graduate.trainings') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
                    <i class="fas fa-graduation-cap me-1"></i> استكشف البرامج التدريبية المتاحة
                </a>
            </div>
        </div>
    @else
        <div class="row g-4 mb-4">
            @foreach($certificates as $cert)
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative certificate-card transition-all" style="background: #ffffff;">
                        {{-- Top Accent Bar --}}
                        <div style="height: 6px; background: {{ $cert->type == 'cooperative_attendance' ? 'linear-gradient(90deg, #10b981, #059669)' : ($cert->type == 'workshop_attendance' ? 'linear-gradient(90deg, #0ea5e9, #0284c7)' : 'linear-gradient(90deg, #3b82f6, #1d4ed8)') }};"></div>
                        
                        <div class="card-body p-4 d-flex flex-column">
                            {{-- Header Badge & Code --}}
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="badge {{ $cert->type_badge_class }} rounded-pill px-3 py-1.5 fw-bold mb-1">
                                        @if($cert->type == 'cooperative_attendance')
                                            <i class="fas fa-handshake me-1"></i>
                                        @elseif($cert->type == 'workshop_attendance')
                                            <i class="fas fa-lightbulb me-1"></i>
                                        @else
                                            <i class="fas fa-certificate me-1"></i>
                                        @endif
                                        {{ $cert->type_label }}
                                    </span>
                                </div>
                                <span class="badge bg-light text-muted border font-monospace small px-2 py-1" style="font-size: 0.75rem;">
                                    {{ $cert->certificate_code }}
                                </span>
                            </div>

                            {{-- Certificate Title --}}
                            <h5 class="fw-bold text-dark mb-3 lh-base" style="font-size: 1.1rem;">
                                {{ $cert->title }}
                            </h5>

                            {{-- Cooperative Partner Banner (if cooperative) --}}
                            @if($cert->has_company_collaboration)
                                <div class="p-2.5 rounded-3 mb-3 d-flex align-items-center gap-2" style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.2);">
                                    <div class="bg-white rounded-circle p-1 d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;">
                                        @if($cert->company_logo)
                                            <img src="{{ Storage::url($cert->company_logo) }}" alt="{{ $cert->company_name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                        @else
                                            <i class="fas fa-building text-success" style="font-size: 0.85rem;"></i>
                                        @endif
                                    </div>
                                    <div class="small text-truncate">
                                        <span class="text-muted d-block" style="font-size: 0.72rem;">بالشراكة والتعاون مع:</span>
                                        <strong class="text-success">{{ $cert->company_name }}</strong>
                                    </div>
                                </div>
                            @endif

                            {{-- Details List --}}
                            <div class="mt-auto pt-3 border-top border-light">
                                <div class="row g-2 small text-muted mb-3">
                                    <div class="col-6">
                                        <i class="far fa-calendar-alt text-primary me-1"></i>
                                        <span>تاريخ الإصدار: </span>
                                        <strong class="text-dark d-block">{{ $cert->issue_date->format('Y-m-d') }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <i class="far fa-clock text-primary me-1"></i>
                                        <span>الساعات المعتمدة: </span>
                                        <strong class="text-dark d-block">{{ $cert->hours }} ساعة تدريبية</strong>
                                    </div>
                                    @if($cert->instructor_name)
                                        <div class="col-12 mt-2">
                                            <i class="fas fa-user-tie text-primary me-1"></i>
                                            <span>المدرب / المشرف: </span>
                                            <strong class="text-dark">{{ $cert->instructor_name }}</strong>
                                        </div>
                                    @endif
                                </div>

                                {{-- Action Buttons --}}
                                <div class="d-flex gap-2">
                                    <a href="{{ route('graduate.certificates.show', $cert->id) }}" class="btn btn-primary rounded-pill flex-grow-1 fw-bold shadow-sm">
                                        <i class="fas fa-eye me-1"></i> عرض الشهادة
                                    </a>
                                    <a href="{{ route('certificates.verify', $cert->certificate_code) }}" target="_blank" class="btn btn-outline-secondary rounded-pill px-3" title="التحقق من صحة الشهادة">
                                        <i class="fas fa-qrcode"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center">
            {{ $certificates->withQueryString()->links() }}
        </div>
    @endif
</div>

<style>
.certificate-card {
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease;
}
.certificate-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 32px rgba(0, 0, 0, 0.08) !important;
}
</style>
@endsection
