@extends('layouts.app')

@section('title', 'لوحة تحكم الخريج')

@push('styles')
<style>
    /* تصميم التقويم المعتمد وفق هوية المنظومة FullCalendar v6 */
    .fc {
        font-family: '29LT Bukra', 'Cairo', 'Tajawal', sans-serif !important;
    }
    .fc .fc-toolbar {
        flex-wrap: wrap !important;
        gap: 10px !important;
        justify-content: space-between !important;
        align-items: center !important;
        padding-bottom: 0.85rem !important;
        border-bottom: 1px solid #f1f5f9 !important;
        margin-bottom: 1rem !important;
    }
    .fc .fc-toolbar-title {
        font-size: 1.35rem !important;
        font-weight: 800 !important;
        color: #0d3882 !important;
        letter-spacing: -0.3px;
    }
    .fc .fc-button {
        font-size: 0.84rem !important;
        font-weight: 700 !important;
        padding: 0.42rem 0.9rem !important;
        border-radius: 8px !important;
        box-shadow: 0 2px 4px rgba(13, 56, 130, 0.12) !important;
        transition: all 0.2s ease !important;
    }
    .fc .fc-button-primary {
        background-color: #0d3882 !important;
        border-color: #0d3882 !important;
        color: #ffffff !important;
    }
    .fc .fc-button-primary:hover, 
    .fc .fc-button-primary:focus, 
    .fc .fc-button-primary.fc-button-active {
        background-color: #1e40af !important;
        border-color: #1e40af !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(13, 56, 130, 0.25) !important;
    }
    .fc .fc-button-group {
        border-radius: 8px !important;
        overflow: hidden;
    }
    .fc .fc-button-group > .fc-button {
        border-radius: 0 !important;
    }
    .fc .fc-button-group > .fc-button:first-child {
        border-top-right-radius: 8px !important;
        border-bottom-right-radius: 8px !important;
    }
    .fc .fc-button-group > .fc-button:last-child {
        border-top-left-radius: 8px !important;
        border-bottom-left-radius: 8px !important;
    }

    /* عناوين الأيام */
    .fc .fc-col-header-cell {
        background: #f8fafc !important;
        padding: 10px 0 !important;
        border-color: #e2e8f0 !important;
    }
    .fc-col-header-cell-cushion {
        font-size: 0.86rem !important;
        font-weight: 800 !important;
        color: #334155 !important;
        text-decoration: none !important;
    }

    /* خلايا الأيام */
    .fc-theme-standard td, .fc-theme-standard th {
        border-color: #edf2f7 !important;
    }
    .fc-daygrid-day-top {
        padding: 4px 6px !important;
    }
    .fc-daygrid-day-number {
        font-size: 0.88rem !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        padding: 2px 7px !important;
        text-decoration: none !important;
        border-radius: 6px;
    }
    .fc-day-other .fc-daygrid-day-number {
        color: #94a3b8 !important;
        opacity: 0.55;
    }
    .fc-day-today {
        background-color: #eff6ff !important;
    }
    .fc-day-today .fc-daygrid-day-number {
        background-color: #0d3882 !important;
        color: #ffffff !important;
        font-weight: 800 !important;
        box-shadow: 0 2px 6px rgba(13, 56, 130, 0.3);
    }

    /* كبسولات الفعاليات والتدريبات الحديثة */
    .fc .fc-daygrid-event {
        border-radius: 6px !important;
        padding: 3px 6px !important;
        margin: 2px 2px !important;
        font-size: 0.74rem !important;
        font-weight: 700 !important;
        border: none !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08) !important;
        transition: transform 0.15s ease, box-shadow 0.15s ease !important;
        cursor: pointer;
    }
    .fc .fc-daygrid-event:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.18) !important;
        filter: brightness(1.08);
    }
    .fc-h-event {
        border: none !important;
    }
    .fc-event-title {
        font-weight: 700 !important;
        font-size: 0.75rem !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        display: block !important;
    }

    /* التدريبات المسجل بها الخريج (تمييز خاص وبارز) */
    .fc .fc-event-my-training {
        border: 1.5px solid #f59e0b !important;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.35) !important;
        background: linear-gradient(135deg, #0d3882 0%, #1e40af 100%) !important;
        position: relative;
        z-index: 5 !important;
    }

    /* أحداث الانطلاق والختام */
    .fc .fc-event-start {
        border-right: 3px solid #f59e0b !important;
    }
    .fc .fc-event-end {
        border-right: 3px solid #ef4444 !important;
    }

    /* زر +X أخرى الراقي والمنسق */
    .fc .fc-daygrid-more-link {
        font-size: 0.72rem !important;
        font-weight: 800 !important;
        color: #0d3882 !important;
        background: #e0e7ff !important;
        border: 1px solid #c7d2fe !important;
        padding: 2px 7px !important;
        border-radius: 6px !important;
        margin-top: 2px !important;
        display: inline-block !important;
        text-decoration: none !important;
        transition: all 0.2s ease !important;
    }
    .fc .fc-daygrid-more-link:hover {
        background: #c7d2fe !important;
        color: #1e1b4b !important;
        transform: scale(1.04);
    }

    /* نافذة المزيد المنبثقة لليوم */
    .fc .fc-more-popover {
        border-radius: 14px !important;
        box-shadow: 0 16px 36px rgba(13, 56, 130, 0.22) !important;
        border: 1px solid #cbd5e1 !important;
        overflow: hidden;
        z-index: 1060 !important;
        background: #ffffff !important;
        max-width: 330px !important;
    }
    .fc .fc-more-popover .fc-popover-header {
        background: #0d3882 !important;
        color: #ffffff !important;
        font-weight: 800 !important;
        padding: 10px 14px !important;
        font-size: 0.85rem !important;
        border-bottom: 1px solid #1e40af !important;
    }
    .fc .fc-more-popover .fc-popover-header .fc-popover-close {
        color: #ffffff !important;
        opacity: 0.85;
    }
    .fc .fc-more-popover .fc-popover-header .fc-popover-close:hover {
        opacity: 1;
    }
    .fc .fc-more-popover .fc-popover-body {
        padding: 10px !important;
        max-height: 280px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    /* أزرار الفلترة السريعة أعلى التقويم */
    .cal-filter-btn {
        transition: all 0.2s ease !important;
        font-size: 0.78rem !important;
        font-weight: 700 !important;
        border-radius: 20px !important;
    }
    .cal-filter-btn.active {
        background-color: #0d3882 !important;
        border-color: #0d3882 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(13, 56, 130, 0.25) !important;
    }

    /* طريقة عرض القائمة */
    .fc .fc-list-empty {
        background: #f8fafc !important;
        padding: 2rem !important;
        color: #64748b !important;
        font-weight: 600 !important;
    }
    .fc .fc-list-day-cushion {
        background: #f1f5f9 !important;
        color: #0d3882 !important;
        font-weight: 800 !important;
        padding: 8px 16px !important;
    }
    .fc .fc-list-event:hover td {
        background-color: #f8fafc !important;
    }

    @media (max-width: 576px) {
        .fc .fc-toolbar-title {
            font-size: 1.15rem !important;
            width: 100%;
            text-align: center;
            order: -1;
            margin-bottom: 0.25rem !important;
        }
        .fc-col-header-cell-cushion {
            font-size: 0.72rem !important;
            padding: 3px 0 !important;
        }
        .fc-daygrid-day-number {
            font-size: 0.75rem !important;
            padding: 2px 4px !important;
        }
        .fc-daygrid-event {
            font-size: 0.70rem !important;
            padding: 2px 4px !important;
        }
    }

    .location-pill {
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        padding: 3px 8px;
        font-weight: 600;
        font-size: 0.76rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* أنماط القوائم المنسدلة للرسائل والإشعارات */
    .dropdown-bridge {
        position: absolute;
        top: -12px;
        left: 0;
        right: 0;
        height: 12px;
        background: transparent;
        pointer-events: auto;
    }

    .graduate-hero-banner .btn,
    .graduate-hero-banner .btn:hover,
    .graduate-hero-banner .btn:focus,
    .graduate-hero-banner .btn:active,
    #desktopMsgDropdownBtn,
    #desktopNotifDropdownBtn,
    #mobileMsgDropdownBtn,
    #mobileNotifDropdownBtn,
    .custom-header-dropdown,
    .custom-header-dropdown * {
        transform: none !important;
    }

    .dash-msg-badge,
    .dash-notif-badge {
        pointer-events: none !important;
        transform: translate(-50%, -50%) !important;
    }

    [dir="rtl"] .dropdown-menu-end.custom-header-dropdown {
        left: 0 !important;
        right: auto !important;
    }

    @media (max-width: 576px) {
        .custom-header-dropdown {
            position: fixed !important;
            top: 64px !important;
            left: 12px !important;
            right: 12px !important;
            width: auto !important;
            max-width: calc(100vw - 24px) !important;
            margin: 0 auto !important;
        }
    }

    .custom-dropdown-list {
        max-height: 380px;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        overscroll-behavior: contain !important;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: rgba(148, 163, 184, 0.4) transparent;
    }
    .custom-dropdown-list::-webkit-scrollbar {
        width: 6px;
    }
    .custom-dropdown-list::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-dropdown-list::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.4);
        border-radius: 4px;
    }
    .custom-dropdown-list::-webkit-scrollbar-thumb:hover {
        background: rgba(148, 163, 184, 0.7);
    }

    .custom-dropdown-item {
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        gap: 12px;
        padding: 12px 16px;
        align-items: flex-start;
        text-decoration: none !important;
        transition: background-color 0.15s ease;
        transform: none !important;
    }
    .custom-dropdown-item:hover {
        background-color: #f8fafc;
    }
    .custom-dropdown-item.is-unread {
        background-color: #eff6ff;
    }
    .custom-dropdown-item.is-unread-msg {
        background-color: #f0fdf4;
    }
    .custom-dropdown-footer {
        background-color: #f8fafc;
        border-top: 1px solid #f1f5f9;
    }

    /* كبسولة +X إضافية للمزيد */
    .fc .fc-more-link {
        font-size: 0.72rem !important;
        font-weight: 700 !important;
        color: #0d3882 !important;
        background: rgba(13, 56, 130, 0.08) !important;
        padding: 2px 7px !important;
        border-radius: 6px !important;
        text-decoration: none !important;
        display: inline-block !important;
        margin-top: 2px !important;
        transition: all 0.2s ease !important;
    }
    .fc .fc-more-link:hover {
        background: #0d3882 !important;
        color: #ffffff !important;
    }

    /* Dark Mode */
    html[data-theme='dark'] .fc .fc-col-header-cell,
    html[data-bs-theme='dark'] .fc .fc-col-header-cell {
        background: #162032 !important;
        background-color: #162032 !important;
        border-color: #27354a !important;
    }
    html[data-theme='dark'] .fc-col-header-cell-cushion,
    html[data-bs-theme='dark'] .fc-col-header-cell-cushion {
        color: #f1f5f9 !important;
    }
    html[data-theme='dark'] .fc-theme-standard td,
    html[data-theme='dark'] .fc-theme-standard th,
    html[data-theme='dark'] .fc-daygrid-day {
        background-color: #111827 !important;
        border-color: #1e293b !important;
    }
    html[data-theme='dark'] .fc-daygrid-day-number {
        color: #94a3b8 !important;
    }
    html[data-theme='dark'] .fc-day-other {
        background-color: #0b1120 !important;
        opacity: 0.65;
    }
    html[data-theme='dark'] .fc-day-other .fc-daygrid-day-number {
        color: #64748b !important;
    }
    html[data-theme='dark'] .fc-day-today {
        background-color: rgba(59, 130, 246, 0.12) !important;
    }
    html[data-theme='dark'] .fc-day-today .fc-daygrid-day-number {
        background-color: #2563eb !important;
        color: #ffffff !important;
    }
    html[data-theme='dark'] .fc .fc-toolbar-title {
        color: #f8fafc !important;
    }
    html[data-theme='dark'] .fc .fc-toolbar {
        border-bottom-color: #1e293b !important;
    }
    html[data-theme='dark'] .fc .fc-button-primary {
        background-color: #1e293b !important;
        border-color: #334155 !important;
        color: #f1f5f9 !important;
    }
    html[data-theme='dark'] .fc .fc-button-primary:hover,
    html[data-theme='dark'] .fc .fc-button-primary.fc-button-active {
        background-color: #2563eb !important;
        border-color: #3b82f6 !important;
        color: #ffffff !important;
    }
    html[data-theme='dark'] .fc .fc-more-popover {
        background: #111827 !important;
        border-color: #27354a !important;
    }
    html[data-theme='dark'] .fc .fc-more-popover .fc-popover-header {
        background: #162032 !important;
        border-bottom-color: #27354a !important;
        color: #f1f5f9 !important;
    }
    html[data-theme='dark'] .fc .fc-list-empty {
        background: #111827 !important;
        color: #94a3b8 !important;
    }
    html[data-theme='dark'] .fc .fc-list-day-cushion {
        background: #162032 !important;
        color: #60a5fa !important;
    }
    html[data-theme='dark'] .fc .fc-list-event:hover td {
        background-color: #1e293b !important;
    }
    html[data-theme='dark'] .fc .fc-more-link {
        color: #60a5fa !important;
        background: rgba(96, 165, 250, 0.15) !important;
    }
    html[data-theme='dark'] .fc .fc-more-link:hover {
        background: #2563eb !important;
        color: #ffffff !important;
    }
    html[data-theme='dark'] .custom-header-dropdown,
    [data-bs-theme='dark'] .custom-header-dropdown {
        background: #0f172a !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6) !important;
    }
    html[data-theme='dark'] .custom-dropdown-item,
    [data-bs-theme='dark'] .custom-dropdown-item {
        color: #f1f5f9 !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
        background: #0f172a;
    }
    html[data-theme='dark'] .custom-dropdown-item:hover,
    [data-bs-theme='dark'] .custom-dropdown-item:hover {
        background: #1e293b !important;
    }
    html[data-theme='dark'] .custom-dropdown-item.is-unread,
    [data-bs-theme='dark'] .custom-dropdown-item.is-unread {
        background: rgba(37, 99, 235, 0.16) !important;
    }
    html[data-theme='dark'] .custom-dropdown-item.is-unread-msg,
    [data-bs-theme='dark'] .custom-dropdown-item.is-unread-msg {
        background: rgba(16, 185, 129, 0.16) !important;
    }
    html[data-theme='dark'] .dropdown-item-title,
    [data-bs-theme='dark'] .dropdown-item-title {
        color: #f8fafc !important;
    }
    html[data-theme='dark'] .dropdown-item-desc,
    [data-bs-theme='dark'] .dropdown-item-desc {
        color: #94a3b8 !important;
    }
    html[data-theme='dark'] .custom-dropdown-footer,
    [data-bs-theme='dark'] .custom-dropdown-footer {
        background: #090d16 !important;
        border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
    }
    html[data-theme='dark'] .custom-dropdown-list::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-2 px-md-3">
    @php
        $myFairRegistration = \App\Models\JobFairRegistration::where('user_id', auth()->id())->latest()->first();
        $dashUnreadMessagesCount = \App\Models\Message::where('receiver_id', auth()->id())->whereNull('read_at')->count();
        $dashUnreadNotifsCount = \App\Models\Notification::forUser(auth()->id())->unread()->count();
    @endphp

    <!-- Unified Single Graduate Hero Banner (Rich & Spacious) -->
    <div class="card graduate-hero-banner border-0 rounded-4 shadow mb-4 position-relative" style="background: linear-gradient(135deg, #0d3882 0%, #1565c0 50%, #1e88e5 100%); color: white; box-shadow: 0 10px 30px rgba(13, 71, 161, 0.18) !important; border-radius: 1rem; overflow: visible;">
        <!-- Ambient decorative glow -->
        <div class="position-absolute end-0 top-0 w-100 h-100 opacity-25 pointer-events-none" style="background: radial-gradient(circle at 90% 10%, rgba(255,255,255,0.3) 0%, transparent 60%); border-radius: inherit; overflow: hidden;"></div>

        <div class="p-4 p-lg-5 position-relative" style="z-index: 2;">


            <div class="row align-items-center g-4">
                <!-- Graduate User Info -->
                <div class="col-12 col-xl-6 col-lg-6">
                    <div class="d-flex align-items-center gap-3 gap-md-4">
                        <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center shadow flex-shrink-0" style="width: 64px; height: 64px; font-size: 1.75rem; font-weight: 700; border: 3px solid rgba(255,255,255,0.4);">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div>
                            <h2 class="fw-bold mb-1 text-white fs-4 fs-md-3">مرحباً بك، {{ auth()->user()->name }}</h2>
                            <p class="text-white text-opacity-75 small mb-2" style="font-size: 0.85rem;">
                                <i class="fas fa-university me-1 text-white-50"></i>منصة تدريب وتأهيل الخريجين — جامعة طرابلس
                            </p>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge rounded-pill px-3 py-1.5 small fw-bold" style="background: rgba(255, 255, 255, 0.22) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.35) !important; font-size: 0.78rem; backdrop-filter: blur(6px);">
                                    <i class="fas fa-check-circle me-1 text-warning"></i>خريج مسجل
                                </span>
                                @if($myFairRegistration)
                                    <a href="{{ route('job-fair.my-ticket', $myFairRegistration->id) }}" class="badge rounded-pill px-2.5 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 text-decoration-none" style="background: #fbbf24; color: #1e293b; border: 1px solid #f59e0b; box-shadow: none !important; font-size: 0.76rem;">
                                        <i class="fas fa-ticket-alt text-dark"></i>
                                        <span>تذكرة المعرض</span>
                                        <span class="d-none d-sm-inline">(#{{ $myFairRegistration->registration_number }})</span>
                                        <i class="fas fa-arrow-left ms-0.5" style="font-size: 0.65rem;"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Action Buttons -->
                <div class="col-12 col-xl-6 col-lg-6 text-lg-start">
                    <div class="d-flex gap-2 justify-content-start justify-content-lg-end flex-wrap align-items-center">
                        <!-- منسدلة الرسائل (ديسكتوب) -->
                        <!-- زر الرسائل المنزلق (ديسكتوب) -->
                        <button type="button"
                           class="btn btn-light bg-white text-primary rounded-circle shadow-sm d-flex align-items-center justify-content-center position-relative flex-shrink-0 d-none d-md-flex"
                           onclick="openMessagesDrawer()"
                           style="width: 44px; height: 44px;"
                           title="صندوق الرسائل">
                            <i class="fas fa-envelope" style="font-size: 1.15rem;"></i>
                            @if($dashUnreadMessagesCount > 0)
                                <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-danger dash-msg-badge" style="font-size: 0.65rem; min-width: 18px; height: 18px; padding: 2px 4px;">{{ $dashUnreadMessagesCount }}</span>
                            @endif
                        </button>

                        <!-- زر الإشعارات المنزلق (ديسكتوب) -->
                        <button type="button"
                           class="btn btn-light bg-white text-warning rounded-circle shadow-sm d-flex align-items-center justify-content-center position-relative flex-shrink-0 d-none d-md-flex"
                           onclick="openNotificationsDrawer()"
                           style="width: 44px; height: 44px;"
                           title="مركز الإشعارات">
                            <i class="fas fa-bell text-warning" style="font-size: 1.15rem;"></i>
                            @if($dashUnreadNotifsCount > 0)
                                <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-danger dash-notif-badge" style="font-size: 0.65rem; min-width: 18px; height: 18px; padding: 2px 4px;">{{ $dashUnreadNotifsCount }}</span>
                            @endif
                        </button>

                        <a href="{{ route('graduate.id-card') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 text-nowrap" style="font-size: 0.88rem;">
                            <i class="fas fa-id-card"></i>
                            <span>بطاقتي الرقمية</span>
                        </a>
                        <a href="{{ route('graduate.trainings') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 text-nowrap" style="font-size: 0.88rem;">
                            <i class="fas fa-graduation-cap"></i>
                            <span>التدريبات</span>
                        </a>
                        <a href="{{ route('graduate.certificates.index') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 text-nowrap" style="font-size: 0.88rem;">
                            <i class="fas fa-award text-warning"></i>
                            <span>شهاداتي</span>
                        </a>
                        <a href="{{ route('graduate.job-opportunities.index') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 text-nowrap" style="font-size: 0.88rem;">
                            <i class="fas fa-briefcase"></i>
                            <span>فرص العمل</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- تنبيه الرسائل الجديدة غير المقروءة من الشركات --}}
    @if($dashUnreadMessagesCount > 0)
        @php
            $latestUnreadMsg = \App\Models\Message::where('receiver_id', auth()->id())
                ->whereNull('read_at')
                ->with(['sender.company'])
                ->latest()
                ->first();
            $senderLabel = $latestUnreadMsg && $latestUnreadMsg->sender && $latestUnreadMsg->sender->company
                ? $latestUnreadMsg->sender->company->name
                : ($latestUnreadMsg?->sender?->name ?? 'شركة شريكة');
        @endphp
        <div class="alert unread-messages-alert border-0 rounded-4 shadow-sm mb-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 p-3.5" 
             style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-right: 5px solid #2563eb !important;">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" 
                     style="width: 48px; height: 48px; background: #2563eb; color: #ffffff; font-size: 1.3rem;">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                <div>
                    <h6 class="mb-1 fw-bold text-dark" style="font-size: 0.98rem;">
                        لديك {{ $dashUnreadMessagesCount }} {{ $dashUnreadMessagesCount == 1 ? 'رسالة جديدة غير مقروءة' : 'رسائل جديدة غير مقروءة' }} من الشركات الشريكة!
                    </h6>
                    <div class="small text-muted">
                        أحدث رسالة من: <strong class="text-primary">{{ $senderLabel }}</strong>
                        @if($latestUnreadMsg)
                            <span class="d-none d-sm-inline">— "{{ \Illuminate\Support\Str::limit($latestUnreadMsg->content, 65) }}"</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                <a href="javascript:void(0)" onclick="window.openMessagesDrawer ? window.openMessagesDrawer() : null; return false;" class="btn btn-primary rounded-pill px-4 py-2 fw-bold text-nowrap shadow-sm" style="font-size: 0.88rem;">
                    <i class="fas fa-comments me-1"></i> فتح صندوق الرسائل
                </a>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 shadow-sm mb-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm mb-3" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- بطاقات الإحصائيات (2x2 على الموبايل و 4 أعمدة على الديسكتوب) -->
    <div class="row g-2 g-md-3 mb-4">
        <!-- البرامج التدريبية -->
        <div class="col-6 col-xl-3">
            <a href="{{ route('graduate.trainings') }}" class="text-decoration-none">
                <div class="card stat-card-widget border-0 rounded-4 shadow-sm p-3 h-100 transition-hover">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-bold" style="font-size: 0.78rem;">البرامج المتاحة</span>
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(37, 99, 235, 0.1); color: #2563eb;">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                    </div>
                    <div class="fs-3 fw-bolder text-primary mb-1">{{ $totalTrainings ?? 0 }}</div>
                    <div class="text-muted small d-flex align-items-center justify-content-between" style="font-size: 0.7rem;">
                        <span>تصفح التدريبات</span>
                        <i class="fas fa-arrow-left text-primary" style="font-size: 0.65rem;"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- فرص العمل -->
        <div class="col-6 col-xl-3">
            <a href="{{ route('graduate.job-opportunities.index') }}" class="text-decoration-none">
                <div class="card stat-card-widget border-0 rounded-4 shadow-sm p-3 h-100 transition-hover">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-bold" style="font-size: 0.78rem;">فرص العمل</span>
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(16, 185, 129, 0.1); color: #10b981;">
                            <i class="fas fa-briefcase"></i>
                        </div>
                    </div>
                    <div class="fs-3 fw-bolder text-success mb-1">{{ \App\Models\JobOpportunity::where('status', 'open')->count() ?? 0 }}</div>
                    <div class="text-muted small d-flex align-items-center justify-content-between" style="font-size: 0.7rem;">
                        <span>الوظائف النشطة</span>
                        <i class="fas fa-arrow-left text-success" style="font-size: 0.65rem;"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- طلباتي وترشيحاتي -->
        <div class="col-6 col-xl-3">
            <a href="{{ route('graduate.my-applications') }}" class="text-decoration-none">
                <div class="card stat-card-widget border-0 rounded-4 shadow-sm p-3 h-100 transition-hover">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-bold" style="font-size: 0.78rem;">طلباتي</span>
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                    <div class="fs-3 fw-bolder text-warning mb-1">{{ $myApplications ?? 0 }}</div>
                    <div class="text-muted small d-flex align-items-center justify-content-between" style="font-size: 0.7rem;">
                        <span>متابعة الطلبات</span>
                        <i class="fas fa-arrow-left text-warning" style="font-size: 0.65rem;"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- تسجيل الحضور الذكي -->
        <div class="col-6 col-xl-3">
            <div class="card stat-card-widget border-0 rounded-4 shadow-sm p-3 h-100 transition-hover" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#qrScannerModal">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-bold" style="font-size: 0.78rem;">تسجيل الحضور</span>
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(14, 165, 233, 0.1); color: #0ea5e9;">
                        <i class="fas fa-qrcode"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bolder text-info mb-1"><i class="fas fa-camera"></i></div>
                <div class="text-muted small d-flex align-items-center justify-content-between" style="font-size: 0.7rem;">
                    <span>مسح الباركود</span>
                    <i class="fas fa-expand text-info" style="font-size: 0.65rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- التقويم الشهري المعتمد للبرامج والفعاليات (كامل العرض لضمان راحة ووضوح العرض الشهري) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 rounded-4 shadow-sm overflow-hidden">
                <div class="card-header bg-white py-3 px-3 px-md-4 border-bottom">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-2 gap-md-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; background: rgba(13, 56, 130, 0.08); color: #0d3882; font-size: 1.25rem;">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <h5 class="card-title mb-0 text-primary fw-bold fs-6 fs-md-5">
                                        تقويم التدريبات والفعاليات
                                    </h5>
                                    <span class="badge rounded-pill bg-light text-primary border border-primary-subtle px-2 py-0.5 small fw-bold" style="font-size: 0.72rem;">
                                        عرض شهري منظم
                                    </span>
                                </div>
                                <small class="text-muted" style="font-size: 0.78rem;">استعراض شهري دقيق لمواعيد انطلاق البرامج وجلساتك التدريبية مع فلترة سريعة</small>
                            </div>
                        </div>

                        <!-- أزرار الفلترة السريعة (Filter Chips) -->
                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold cal-filter-btn active" data-filter="all" onclick="setCalendarTypeFilter('all', this)">
                                الكل
                            </button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold cal-filter-btn" data-filter="my_trainings" onclick="setCalendarTypeFilter('my_trainings', this)" style="color: #0d3882;">
                                <i class="fas fa-star text-warning me-1"></i>تدريباتي المسجل بها
                                @if(isset($myCalendarCount) && $myCalendarCount > 0)
                                    <span class="badge bg-warning text-dark rounded-pill ms-1" style="font-size: 0.68rem;">{{ $myCalendarCount }}</span>
                                @endif
                            </button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 fw-bold cal-filter-btn" data-filter="course" onclick="setCalendarTypeFilter('course', this)" style="color: #0d3882;">
                                <span class="d-inline-block rounded-circle me-1" style="width: 7px; height: 7px; background: #0d3882;"></span>دورات
                            </button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 fw-bold cal-filter-btn" data-filter="workshop" onclick="setCalendarTypeFilter('workshop', this)" style="color: #059669;">
                                <span class="d-inline-block rounded-circle me-1" style="width: 7px; height: 7px; background: #059669;"></span>ورش عمل
                            </button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 fw-bold cal-filter-btn" data-filter="internship" onclick="setCalendarTypeFilter('internship', this)" style="color: #1d4ed8;">
                                <span class="d-inline-block rounded-circle me-1" style="width: 7px; height: 7px; background: #1d4ed8;"></span>تدريب عملي
                            </button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 fw-bold cal-filter-btn" data-filter="seminar" onclick="setCalendarTypeFilter('seminar', this)" style="color: #d97706;">
                                <span class="d-inline-block rounded-circle me-1" style="width: 7px; height: 7px; background: #d97706;"></span>ندوات
                            </button>
                        </div>

                        <!-- شريط الفلترة حسب الموقع -->
                        <div class="d-flex align-items-center gap-2 ms-md-auto">
                            <span class="small fw-bold text-muted d-none d-sm-inline"><i class="fas fa-map-marker-alt text-danger me-1"></i>الموقع:</span>
                            <select id="locationFilterSelect" class="form-select form-select-sm rounded-pill" style="min-width: 140px; max-width: 190px;" onchange="filterCalendarByLocation(this.value)">
                                <option value="all">جميع القاعات والأماكن</option>
                                @foreach($calendarLocations ?? [] as $loc)
                                    <option value="{{ $loc }}">{{ $loc }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- رسائل الشركات وتنبيهات التواصل + طلباتي الأخيرة -->
    <div class="row g-3 g-md-4 mb-4">
        <!-- الجانب الأيمن: رسائل الشركات والتنبيهات -->
        <div class="col-lg-6">
            <div class="card border-0 rounded-4 shadow-sm h-100 d-flex flex-column">
                <div class="card-header py-3 border-bottom d-flex justify-content-between align-items-center bg-white">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: rgba(13, 56, 130, 0.08); color: #0d3882;">
                            <i class="fas fa-envelope-open-text"></i>
                        </div>
                        <h5 class="card-title mb-0 text-primary fw-bold fs-6">
                            رسائل وتنبيهات الشركات
                        </h5>
                        @if($dashUnreadMessagesCount > 0)
                            <span class="badge bg-danger rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">{{ $dashUnreadMessagesCount }} جديدة</span>
                        @endif
                    </div>
                    <a href="javascript:void(0)" onclick="window.openMessagesDrawer ? window.openMessagesDrawer() : null; return false;" class="small text-decoration-none fw-bold text-primary">
                        صندوق الرسائل <i class="fas fa-arrow-left ms-0.5" style="font-size: 0.7rem;"></i>
                    </a>
                </div>
                <div class="card-body p-3 d-flex flex-column flex-grow-1">
                    @php
                        $recentDashboardConversations = \App\Models\Message::where('receiver_id', auth()->id())
                            ->orWhere('sender_id', auth()->id())
                            ->with(['sender.company', 'receiver.company'])
                            ->latest()
                            ->get()
                            ->groupBy(function($msg) {
                                return $msg->sender_id == auth()->id() ? $msg->receiver_id : $msg->sender_id;
                            })
                            ->take(3);
                    @endphp

                    @if($recentDashboardConversations->count() > 0)
                        <div class="d-flex flex-column gap-2.5 flex-grow-1">
                            @foreach($recentDashboardConversations as $otherPartyId => $msgs)
                                @php
                                    $lastMsg = $msgs->first();
                                    $otherUser = $lastMsg->sender_id == auth()->id() ? $lastMsg->receiver : $lastMsg->sender;
                                    $displayName = $otherUser->company ? $otherUser->company->name : $otherUser->name;
                                    $isUnread = $lastMsg->receiver_id == auth()->id() && is_null($lastMsg->read_at);
                                @endphp
                                <a href="javascript:void(0)" onclick="window.openMessagesDrawer ? window.openMessagesDrawer({{ $otherPartyId }}) : null; return false;" 
                                   class="text-decoration-none p-3 rounded-3 border d-flex align-items-center justify-content-between transition-hover dashboard-msg-item {{ $isUnread ? 'is-unread' : '' }}"
                                   style="cursor: pointer;">
                                    <div class="d-flex align-items-center gap-2.5 min-w-0 flex-grow-1">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                                             style="width: 38px; height: 38px; background: {{ $isUnread ? '#16a34a' : '#0d3882' }}; color: #ffffff; font-size: 0.95rem; font-weight: bold;">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <div class="min-w-0 flex-grow-1">
                                            <div class="d-flex align-items-center gap-1.5 mb-0.5">
                                                <h6 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 0.88rem;">{{ $displayName }}</h6>
                                                @if($isUnread)
                                                    <span class="badge bg-success rounded-pill px-1.5 py-0.5" style="font-size: 0.65rem;">جديدة</span>
                                                @endif
                                            </div>
                                            <p class="mb-0 text-muted small text-truncate" style="font-size: 0.78rem;">
                                                @if($lastMsg->sender_id == auth()->id())
                                                    <span class="text-secondary">أنت: </span>
                                                @endif
                                                {{ $lastMsg->content }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-end flex-shrink-0 ms-2">
                                        <span class="text-muted font-monospace" style="font-size: 0.7rem;">{{ $lastMsg->created_at->diffForHumans(null, true, true) }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <div class="mt-3 pt-2 border-top text-center">
                            <a href="javascript:void(0)" onclick="window.openMessagesDrawer ? window.openMessagesDrawer() : null; return false;" class="btn btn-outline-primary btn-sm rounded-pill w-100 fw-bold py-1.5" style="font-size: 0.82rem;">
                                <i class="fas fa-comments me-1"></i> فتح المحادثات بالشريط الجانبي
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4 my-auto">
                            <div class="rounded-circle bg-light text-muted d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-inbox fa-lg opacity-50 text-primary"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1 fs-6">لا توجد رسائل جديدة</h6>
                            <p class="text-muted small mb-3" style="font-size: 0.78rem;">عندما ترغب أي شركة شريكة في التواصل معك بخصوص فرصة عمل أو مقابلة، ستظهر رسائلها هنا فوراً.</p>
                            <a href="javascript:void(0)" onclick="window.openMessagesDrawer ? window.openMessagesDrawer() : null; return false;" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" style="font-size: 0.8rem;">
                                <i class="fas fa-envelope me-1"></i> فتح صندوق الرسائل
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- الجانب الأيسر: طلباتي الأخيرة ومتابعة الطلبات -->
        <div class="col-lg-6">
            <div class="card border-0 rounded-4 shadow-sm h-100 d-flex flex-column">
                <div class="card-header py-3 border-bottom d-flex align-items-center justify-content-between bg-white">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                            <i class="fas fa-history"></i>
                        </div>
                        <h5 class="card-title mb-0 text-primary fw-bold fs-6">
                            طلباتي الأخيرة
                        </h5>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-primary border border-primary rounded-pill px-2.5 py-1 small fw-bold">{{ $myApplications }} طلبات</span>
                        <a href="{{ route('graduate.my-applications') }}" class="small text-decoration-none fw-bold text-primary">
                            عرض الكل <i class="fas fa-arrow-left ms-0.5" style="font-size: 0.7rem;"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-3 d-flex flex-column flex-grow-1">
                    @if($myRecentApplications->count() > 0)
                        <div class="list-group list-group-flush flex-grow-1">
                            @foreach($myRecentApplications as $application)
                                <div class="list-group-item border-bottom px-2 py-2.5">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="min-w-0 me-2">
                                            <h6 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 0.86rem;">{{ $application->training->title }}</h6>
                                            <small class="text-muted" style="font-size: 0.74rem;">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $application->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <div>
                                            @if($application->status == 'approved')
                                                <span class="badge bg-light text-success border border-success rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">مقبول</span>
                                            @elseif($application->status == 'rejected')
                                                <span class="badge bg-light text-danger border border-danger rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">مرفوض</span>
                                            @else
                                                <span class="badge bg-light text-warning border border-warning rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">قيد المراجعة</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 my-auto">
                            <i class="fas fa-inbox fa-3x text-muted mb-2 opacity-50"></i>
                            <p class="text-muted small mb-2">لا توجد طلبات تدريب سابقة</p>
                            <a href="{{ route('graduate.trainings') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size: 0.8rem;">
                                <i class="fas fa-paper-plane me-1"></i> تقديم طلب تدريب
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- تتبع التقدم في التدريبات الحالية -->
    @if($activeTrainings->count() > 0)
        <div class="card card-modern border-0 rounded-4 shadow-sm mb-4">
            <div class="card-header py-3 border-bottom">
                <h5 class="card-title mb-0 text-primary fw-bold">
                    <i class="fas fa-tasks me-2"></i>التدريبات الحالية والتقدم
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    @foreach($activeTrainings as $app)
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-light training-progress-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold text-dark">{{ $app->training->title }}</span>
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                        @if($app->attended_days_count > 0)
                                            <span class="badge rounded-pill px-2 py-0.5 small fw-bold bg-success-subtle text-success border border-success-subtle" style="font-size: 0.72rem;">
                                                <i class="fas fa-user-check me-1"></i>حضور {{ $app->attendance_percentage }}%
                                            </span>
                                        @endif
                                        <span class="badge rounded-pill px-2.5 py-1 small fw-bold" style="background: #eff6ff; color: #2563eb; border: 1.5px solid #3b82f6; box-shadow: none !important; font-size: 0.75rem;">{{ $app->progress }}% زمنياً</span>
                                    </div>
                                </div>
                                <div class="progress rounded-pill" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: {{ $app->progress }}%" aria-valuenow="{{ $app->progress }}"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="mt-2 d-flex justify-content-between text-muted small">
                                    <span><i class="fas fa-calendar-alt me-1"></i> البداية: {{ $app->training->start_date->format('Y-m-d') }}</span>
                                    <span><i class="fas fa-calendar-check me-1"></i> النهاية: {{ $app->training->end_date ? $app->training->end_date->format('Y-m-d') : 'غير محدد' }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- حالة الترشيحات الوظيفية (Bento Cards Layout) -->
    @if($nominations->count() > 0)
        <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden">
            <div class="card-header py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; background: rgba(37, 99, 235, 0.1); color: #2563eb; font-size: 1.2rem;">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="card-title mb-0 text-dark fw-bold">حالة الترشيحات الوظيفية</h5>
                            <span class="badge rounded-pill px-2.5 py-1 small fw-bold" style="background: #eff6ff; color: #2563eb; border: 1.5px solid #93c5fd; box-shadow: none !important; font-size: 0.72rem;">{{ $nominations->count() }} ترشيح</span>
                        </div>
                        <small class="text-muted" style="font-size: 0.78rem;">متابعة طلبات التوظيف، مواعيد المقابلات، والردود الرسمية للشركات</small>
                    </div>
                </div>
                <a href="{{ route('graduate.my-applications') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                    عرض جميع الترشيحات <i class="fas fa-arrow-left ms-1"></i>
                </a>
            </div>
            <div class="card-body p-3 p-md-4">
                <div class="row g-3">
                    @foreach($nominations as $nomination)
                        @php
                            $company = $nomination->jobOpportunity->company ?? null;
                            $companyName = $company->name ?? 'شركة معتمدة';
                            $companyLogo = $company->logo_path ?? null;
                            
                            // Stepper stages: 1: submitted, 2: review, 3: interview, 4: final
                            $step = 1;
                            if ($nomination->status == 'pending') $step = 2;
                            elseif ($nomination->status == 'interview_scheduled') $step = 3;
                            elseif (in_array($nomination->status, ['accepted', 'rejected', 'withdrawn'])) $step = 4;

                            $progressPercent = ($step - 1) * 33.33;
                            $progressColor = '#2563eb';
                            if ($step == 2) $progressColor = '#f59e0b';
                            elseif ($step == 3) $progressColor = '#0ea5e9';
                            elseif ($step == 4) $progressColor = ($nomination->status == 'accepted' ? '#10b981' : '#ef4444');
                        @endphp
                        <div class="col-12">
                            <div class="p-3 p-md-4 rounded-4 border transition-hover position-relative nomination-card-item">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                                    <!-- Company & Opportunity Details -->
                                    <div class="d-flex align-items-center gap-3">
                                        @if($companyLogo)
                                            <img src="{{ asset('storage/' . $companyLogo) }}" alt="{{ $companyName }}" class="rounded-3 shadow-sm object-fit-cover flex-shrink-0" style="width: 52px; height: 52px; border: 1px solid #e2e8f0;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="rounded-3 bg-white text-primary fw-bold shadow-sm align-items-center justify-content-center flex-shrink-0 border" style="width: 52px; height: 52px; font-size: 1.4rem; display: none;">
                                                {{ mb_substr($companyName, 0, 1) }}
                                            </div>
                                        @else
                                            <div class="rounded-3 bg-white text-primary fw-bold shadow-sm d-flex align-items-center justify-content-center flex-shrink-0 border" style="width: 52px; height: 52px; font-size: 1.4rem;">
                                                {{ mb_substr($companyName, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                                <h6 class="fw-bold text-dark mb-0 fs-6">{{ $nomination->jobOpportunity->title ?? 'فرصة عمل' }}</h6>
                                                @if($nomination->nomination_type == 'self')
                                                    <span class="badge rounded-pill px-2.5 py-1 small fw-bold" style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; box-shadow: none !important; font-size: 0.7rem;">
                                                        <i class="fas fa-user me-1"></i>تقديم مباشر
                                                    </span>
                                                @else
                                                    <span class="badge rounded-pill px-2.5 py-1 small fw-bold" style="background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; box-shadow: none !important; font-size: 0.7rem;">
                                                        <i class="fas fa-star me-1"></i>ترشيح من المكتب
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center gap-2 text-muted small flex-wrap" style="font-size: 0.78rem;">
                                                <span class="d-flex align-items-center gap-1"><i class="fas fa-building text-primary"></i> {{ $companyName }}</span>
                                                <span class="text-muted">•</span>
                                                <span class="d-flex align-items-center gap-1"><i class="fas fa-map-marker-alt text-danger"></i> {{ $nomination->jobOpportunity->location ?? 'ليبيا' }}</span>
                                                <span class="text-muted">•</span>
                                                <span class="d-flex align-items-center gap-1"><i class="far fa-calendar-alt text-secondary"></i> {{ $nomination->created_at->format('Y/m/d') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status & Action (Clean Outline Style like Details Button) -->
                                    <div class="d-flex align-items-center gap-2 w-100 w-md-auto justify-content-between justify-content-md-end">
                                        @if($nomination->status == 'accepted')
                                            <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5" style="background: #f0fdf4; color: #15803d; border: 1.5px solid #22c55e; box-shadow: none !important; font-size: 0.78rem;">
                                                <i class="fas fa-check-circle"></i> تم القبول بنجاح
                                            </span>
                                        @elseif($nomination->status == 'interview_scheduled')
                                            <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5" style="background: #eff6ff; color: #1d4ed8; border: 1.5px solid #3b82f6; box-shadow: none !important; font-size: 0.78rem;">
                                                <i class="fas fa-calendar-check"></i> مقابلة مجدولة
                                            </span>
                                        @elseif($nomination->status == 'rejected' || $nomination->status == 'withdrawn')
                                            <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5" style="background: #fef2f2; color: #b91c1c; border: 1.5px solid #ef4444; box-shadow: none !important; font-size: 0.78rem;">
                                                <i class="fas fa-times-circle"></i> غير مكتمل
                                            </span>
                                        @else
                                            <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5" style="background: #fffbeb; color: #b45309; border: 1.5px solid #f59e0b; box-shadow: none !important; font-size: 0.78rem;">
                                                <i class="fas fa-hourglass-half"></i> قيد المراجعة
                                            </span>
                                        @endif

                                        <a href="{{ route('graduate.job-opportunities.show', $nomination->job_opportunity_id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 text-nowrap fw-bold" style="box-shadow: none !important;">
                                            التفاصيل <i class="fas fa-arrow-left ms-1"></i>
                                        </a>
                                    </div>
                                </div>

                                <!-- Stepper Progress Tracker with Connecting Line -->
                                <div class="mt-3 pt-3 border-top">
                                    <div class="mx-auto" style="max-width: 580px;">
                                        <div class="position-relative py-1">
                                            <!-- Progress Line Background -->
                                            <div class="position-absolute stepper-line-bg" style="top: 15px; right: 12.5%; left: 12.5%; height: 3px; z-index: 1;">
                                                <!-- Active Progress Fill (RTL) -->
                                                <div class="h-100" style="width: {{ $progressPercent }}%; background: {{ $progressColor }}; transition: width 0.4s ease;"></div>
                                            </div>

                                            <div class="row text-center g-0 position-relative" style="z-index: 2;">
                                                <div class="col-3">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <div class="rounded-circle stepper-node-circle d-flex align-items-center justify-content-center mb-1 text-white shadow-sm" style="width: 28px; height: 28px; font-size: 0.72rem; background: #2563eb;">
                                                            <i class="fas fa-check"></i>
                                                        </div>
                                                        <span class="small fw-bold text-primary" style="font-size: 0.72rem;">تم التقديم</span>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <div class="rounded-circle stepper-node-circle d-flex align-items-center justify-content-center mb-1 text-white shadow-sm" style="width: 28px; height: 28px; font-size: 0.72rem; background: {{ $step >= 2 ? ($nomination->status == 'pending' ? '#f59e0b' : '#2563eb') : '#cbd5e1' }};">
                                                            <i class="fas {{ $step > 2 ? 'fa-check' : 'fa-hourglass-half' }}"></i>
                                                        </div>
                                                        <span class="small fw-bold {{ $step >= 2 ? ($nomination->status == 'pending' ? 'text-warning' : 'text-primary') : 'text-muted' }}" style="font-size: 0.72rem;">قيد الفرز</span>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <div class="rounded-circle stepper-node-circle d-flex align-items-center justify-content-center mb-1 text-white shadow-sm" style="width: 28px; height: 28px; font-size: 0.72rem; background: {{ $step >= 3 ? ($nomination->status == 'interview_scheduled' ? '#0ea5e9' : '#2563eb') : '#cbd5e1' }};">
                                                            <i class="fas {{ $step > 3 ? 'fa-check' : 'fa-calendar-alt' }}"></i>
                                                        </div>
                                                        <span class="small fw-bold {{ $step >= 3 ? ($nomination->status == 'interview_scheduled' ? 'text-info' : 'text-primary') : 'text-muted' }}" style="font-size: 0.72rem;">المقابلة</span>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <div class="rounded-circle stepper-node-circle d-flex align-items-center justify-content-center mb-1 text-white shadow-sm" style="width: 28px; height: 28px; font-size: 0.72rem; background: {{ $step >= 4 ? ($nomination->status == 'accepted' ? '#10b981' : '#ef4444') : '#cbd5e1' }};">
                                                            <i class="fas {{ $nomination->status == 'accepted' ? 'fa-check' : ($nomination->status == 'rejected' ? 'fa-times' : 'fa-flag') }}"></i>
                                                        </div>
                                                        <span class="small fw-bold {{ $step >= 4 ? ($nomination->status == 'accepted' ? 'text-success' : 'text-danger') : 'text-muted' }}" style="font-size: 0.72rem;">القرار النهائي</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Interview Highlight Alert (if scheduled) -->
                                @if($nomination->interview_date)
                                    <div class="mt-3 p-2.5 px-3 rounded-3 bg-info bg-opacity-10 border border-info border-opacity-25 d-flex align-items-center justify-content-between flex-wrap gap-2 text-dark small" style="font-size: 0.78rem;">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-calendar-check text-info fs-6"></i>
                                            <span><strong>موعد المقابلة:</strong> {{ $nomination->interview_date->format('Y-m-d') }} @if($nomination->interview_time) الساعة {{ $nomination->interview_time }} @endif</span>
                                        </div>
                                        @if($nomination->interview_location)
                                            <div>
                                                <i class="fas fa-map-marker-alt text-danger me-1"></i><strong>المكان:</strong> {{ $nomination->interview_location }}
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @if($nomination->notes)
                                    <div class="mt-2 text-muted small" style="font-size: 0.75rem;">
                                        <i class="fas fa-info-circle text-primary me-1"></i>{{ $nomination->notes }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- الصف السفلي: برامج تدريبية موصى بها -->
    <div class="card card-modern border-0 rounded-4 shadow-sm mb-4 overflow-hidden">
        <div class="card-header py-3 px-3 px-md-4 border-bottom d-flex align-items-center justify-content-between bg-white">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i class="fas fa-star"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0 text-primary fw-bold fs-6">
                        برامج تدريبية موصى بها لك
                    </h5>
                    <small class="text-muted" style="font-size: 0.76rem;">اكتشف أحدث البرامج التدريبية المعتمدة لتعزيز مسارك المهني</small>
                </div>
            </div>
            <a href="{{ route('graduate.trainings') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" style="font-size: 0.8rem;">
                تصفح كافة التدريبات <i class="fas fa-arrow-left ms-1"></i>
            </a>
        </div>
        <div class="card-body p-3 p-md-4">
            @if($recommendedTrainings->count() > 0)
                <div class="row g-3">
                    @foreach($recommendedTrainings as $training)
                        <div class="col-md-4">
                            <div class="p-3 rounded-4 border bg-light h-100 d-flex flex-column justify-content-between transition-hover">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge bg-white text-primary border rounded-pill px-2.5 py-1 small fw-bold">
                                            {{ $training->type_arabic ?? 'تدريب' }}
                                        </span>
                                        <small class="text-muted font-monospace" style="font-size: 0.74rem;">
                                            <i class="fas fa-calendar-alt text-secondary me-1"></i>{{ $training->start_date ? $training->start_date->format('Y/m/d') : 'مفتوح' }}
                                        </small>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">{{ $training->title }}</h6>
                                    <p class="text-muted small mb-3" style="font-size: 0.8rem; line-height: 1.45;">{{ Str::limit($training->description, 80) }}</p>
                                </div>
                                <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                                    <small class="text-muted" style="font-size: 0.75rem;"><i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $training->location ?? 'جامعة طرابلس' }}</small>
                                    <a href="{{ route('graduate.trainings.show', $training->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-bold" style="font-size: 0.78rem;">
                                        التفاصيل <i class="fas fa-chevron-left ms-0.5" style="font-size: 0.65rem;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4 my-auto">
                    <i class="fas fa-graduation-cap fa-3x text-muted mb-2 opacity-50"></i>
                    <p class="text-muted small mb-0">لا توجد تدريبات موصى بها حالياً</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- QR Scanner Modal -->
<div class="modal fade" id="qrScannerModal" tabindex="-1" aria-labelledby="qrScannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-modern p-0 overflow-hidden border-0">
            <div class="modal-header bg-white py-3 border-bottom">
                <h5 class="modal-title text-primary fw-bold" id="qrScannerModalLabel">
                    <i class="fas fa-qrcode me-2"></i>ماسح الباركود (QR)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <p class="text-muted mb-3">قم بتوجيه الكاميرا نحو باركود جناح الشركة أو الفعالية لتسجيل حضورك أو تسليم سيرتك الذاتية</p>
                <div id="reader" style="width: 100%; max-width: 350px; margin: 0 auto; border-radius: 12px; overflow: hidden;"></div>
            </div>
            <div class="modal-footer bg-light border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal تفاصيل التدريب التفاعلي عند النقر على أي فعالية في التقويم -->
<div class="modal fade" id="calEventModal" tabindex="-1" aria-hidden="true" onclick="if(event.target === this) closeCalendarModal()">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header text-white py-3 px-4" style="background: #0d3882;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-calendar-check fs-5 text-warning"></i>
                    <h6 class="modal-title fw-bold mb-0" id="mEventTitle">تفاصيل البرنامج التدريبي</h6>
                </div>
                <button type="button" class="btn-close btn-close-white" onclick="closeCalendarModal()" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="mEventBadgeContainer" class="d-flex flex-wrap gap-1.5 mb-2.5"></div>
                <h5 id="mEventHeading" class="fw-bold text-dark mb-3"></h5>

                <div class="p-3 bg-light rounded-3 mb-3 border d-flex flex-column gap-2.5 small">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-map-marker-alt text-danger fs-6" style="width: 22px;"></i>
                        <span class="text-muted">مكان التدريب / القاعة:</span>
                        <strong id="mEventLocation" class="text-primary fw-bold fs-6"></strong>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-calendar-alt text-primary" style="width: 22px;"></i>
                        <span class="text-muted">الفترة الزمنية:</span>
                        <strong id="mEventDates" class="text-dark font-monospace"></strong>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="far fa-clock text-warning" style="width: 22px;"></i>
                        <span class="text-muted">توقيت الجلسة:</span>
                        <strong id="mEventTiming" class="text-dark font-monospace"></strong>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-hourglass-half text-warning" style="width: 22px;"></i>
                        <span class="text-muted">المدة المقدرة:</span>
                        <strong id="mEventDuration" class="text-dark"></strong>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-chalkboard-teacher text-info" style="width: 22px;"></i>
                        <span class="text-muted">المدرب المشرف:</span>
                        <strong id="mEventInstructor" class="text-dark"></strong>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-users text-success" style="width: 22px;"></i>
                        <span class="text-muted">المقاعد المتاحة:</span>
                        <strong id="mEventSeats" class="text-dark"></strong>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-2">
                    <button type="button" class="btn btn-light border rounded-3 px-4 fw-semibold" onclick="closeCalendarModal()" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> إغلاق
                    </button>
                    <a id="mEventUrl" href="#" class="btn btn-primary rounded-3 px-4 fw-bold d-flex align-items-center gap-1.5" style="background: #0d3882; border-color: #0d3882;">
                        <i class="fas fa-eye"></i>
                        <span>عرض تفاصيل التدريب والتقديم</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        let calendar = null;
        const allCalendarEvents = @json($calendarTrainings ?? []);

        document.addEventListener('DOMContentLoaded', function () {
            // HTML5 QR Code Scanner
            let html5QrcodeScanner;
            const qrModal = document.getElementById('qrScannerModal');

            if (qrModal) {
                qrModal.addEventListener('shown.bs.modal', function () {
                    html5QrcodeScanner = new Html5QrcodeScanner(
                        "reader",
                        { fps: 10, qrbox: {width: 250, height: 250}, aspectRatio: 1.0 },
                        /* verbose= */ false
                    );
                    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                });

                qrModal.addEventListener('hidden.bs.modal', function () {
                    if (html5QrcodeScanner) {
                        html5QrcodeScanner.clear().catch(error => {
                            console.error("Failed to clear html5QrcodeScanner. ", error);
                        });
                    }
                });
            }

            function onScanSuccess(decodedText, decodedResult) {
                if (decodedText.startsWith('http')) {
                    if (html5QrcodeScanner) html5QrcodeScanner.clear();
                    window.location.href = decodedText;
                } else {
                    alert('هذا الباركود غير صالح للاستخدام هنا.');
                }
            }

            function onScanFailure(error) {
                // Ignore scanning cycle ticks
            }

            // FullCalendar
            const calendarEl = document.getElementById('calendar');
            if (calendarEl) {
                const isSmallScreen = window.innerWidth < 576;
                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'ar',
                    direction: 'rtl',
                    height: 'auto',
                    dayMaxEvents: 3, // يحدد 3 فعاليات لكل يوم لتفادي تراكم وتشويه الخلايا
                    dayMaxEventRows: 4,
                    moreLinkClick: 'popover', // نافذة منبثقة أنيقة ومريحة لليوم
                    moreLinkText: function(num) {
                        return '+' + num + ' أخرى';
                    },
                    dayHeaderFormat: isSmallScreen ? { weekday: 'narrow' } : { weekday: 'short' },
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,listMonth'
                    },
                    buttonText: {
                        today: 'اليوم',
                        month: 'شهري',
                        week: 'أسبوعي',
                        list: 'قائمة'
                    },
                    events: allCalendarEvents,
                    eventContent: function(arg) {
                        let props = arg.event.extendedProps || {};
                        let type = props.type || 'course';
                        let role = props.eventRole || 'event';
                        let isMy = props.isMyTraining || false;

                        let iconClass = 'fa-graduation-cap';
                        if (type === 'workshop') iconClass = 'fa-tools';
                        else if (type === 'internship') iconClass = 'fa-laptop-code';
                        else if (type === 'seminar') iconClass = 'fa-bullhorn';

                        if (role === 'start') iconClass = 'fa-flag-checkered text-warning';
                        else if (role === 'end') iconClass = 'fa-award text-light';
                        else if (role === 'session') iconClass = 'fa-calendar-check text-info';

                        let starHtml = isMy ? '<i class="fas fa-star text-warning me-1 flex-shrink-0" title="برنامج مسجل به" style="font-size: 0.62rem;"></i>' : '';

                        let timing = props.compactTiming || props.timing || '';
                        if (timing === 'غير محدد') timing = '';

                        let timingHtml = timing ? `
                            <div class="d-flex align-items-center gap-1 text-truncate mt-0.5" style="font-size: 0.65rem; opacity: 0.95; padding-right: 2px;">
                                <i class="far fa-clock" style="font-size: 0.6rem; flex-shrink: 0;"></i>
                                <span class="font-monospace text-truncate">${timing}</span>
                            </div>
                        ` : '';

                        let customHtml = `
                            <div class="d-flex flex-column overflow-hidden w-100 text-white" style="line-height: 1.2; padding: 1px 2px;">
                                <div class="d-flex align-items-center gap-1 overflow-hidden w-100">
                                    ${starHtml}
                                    <i class="fas ${iconClass} me-1" style="font-size: 0.68rem; opacity: 0.95; flex-shrink: 0;"></i>
                                    <span class="text-truncate fw-bold flex-grow-1" style="font-size: 0.74rem;">${arg.event.title}</span>
                                </div>
                                ${timingHtml}
                            </div>
                        `;
                        return { html: customHtml };
                    },
                    eventClick: function (info) {
                        info.jsEvent.preventDefault();
                        const props = info.event.extendedProps || {};
                        openCalendarModal(
                            props.rawTitle || info.event.title,
                            props.location || 'غير محدد',
                            props.startDate || '',
                            props.endDate || '',
                            props.duration || 'غير محدد',
                            props.instructor || 'غير محدد',
                            props.seats || '0',
                            props.status || 'active',
                            props.showUrl || info.event.url || '#',
                            props
                        );
                    }
                });

                setTimeout(function () {
                    calendar.render();
                    calendar.updateSize();
                }, 100);

                window.addEventListener('resize', function() {
                    if (calendar) {
                        calendar.setOption('dayHeaderFormat', window.innerWidth < 576 ? { weekday: 'narrow' } : { weekday: 'short' });
                        calendar.updateSize();
                    }
                });
            }
        });

        let activeCalTypeFilter = 'all';
        let activeCalLocFilter = 'all';

        function applyCalendarFilters() {
            if (!calendar) return;
            calendar.removeAllEvents();

            const filtered = allCalendarEvents.filter(function(e) {
                const props = e.extendedProps || {};
                
                // فلترة الموقع
                if (activeCalLocFilter !== 'all' && props.location !== activeCalLocFilter) {
                    return false;
                }

                // فلترة التصنيف / تدريباتي
                if (activeCalTypeFilter === 'all') {
                    return true;
                } else if (activeCalTypeFilter === 'my_trainings') {
                    return props.isMyTraining === true;
                } else {
                    return props.type === activeCalTypeFilter;
                }
            });

            calendar.addEventSource(filtered);
        }

        function setCalendarTypeFilter(type, btnEl) {
            activeCalTypeFilter = type;
            document.querySelectorAll('.cal-filter-btn').forEach(function(b) {
                b.classList.remove('btn-primary', 'active');
                b.classList.add('btn-light');
            });
            if (btnEl) {
                btnEl.classList.remove('btn-light');
                btnEl.classList.add('btn-primary', 'active');
            }
            applyCalendarFilters();
        }

        function filterCalendarByLocation(loc) {
            activeCalLocFilter = loc;
            applyCalendarFilters();
        }

        function openCalendarModal(title, location, start, end, duration, instructor, seats, status, url, props) {
            props = props || {};
            document.getElementById('mEventHeading').innerText = title;
            document.getElementById('mEventLocation').innerText = location || 'غير محدد';
            document.getElementById('mEventDates').innerText = (start || '') + (end ? ' إلى ' + end : '');
            document.getElementById('mEventTiming').innerText = props.timing || 'غير محدد';
            document.getElementById('mEventDuration').innerText = duration || 'غير محدد';
            document.getElementById('mEventInstructor').innerText = instructor || 'غير محدد';
            document.getElementById('mEventSeats').innerText = (seats || '0') + ' مقعد';
            document.getElementById('mEventUrl').href = url || '#';

            const badgeBox = document.getElementById('mEventBadgeContainer');
            if (badgeBox) {
                let badges = '';
                if (props.isMyTraining) {
                    badges += '<span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-bold"><i class="fas fa-star me-1"></i>أنت مسجل في هذا البرنامج التدريبي</span> ';
                }
                if (props.timing && props.timing !== 'غير محدد') {
                    badges += '<span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: rgba(245, 158, 11, 0.15); color: #b45309; border: 1px solid rgba(245, 158, 11, 0.3);"><i class="far fa-clock text-warning me-1"></i>' + props.timing + '</span> ';
                }
                if (props.eventRole === 'start') {
                    badges += '<span class="badge bg-primary px-3 py-1.5 rounded-pill fw-bold"><i class="fas fa-flag-checkered me-1"></i>موعد انطلاق البرنامج</span> ';
                } else if (props.eventRole === 'end') {
                    badges += '<span class="badge bg-danger px-3 py-1.5 rounded-pill fw-bold"><i class="fas fa-award me-1"></i>موعد ختام البرنامج</span> ';
                } else if (props.eventRole === 'session') {
                    badges += '<span class="badge bg-info px-3 py-1.5 rounded-pill fw-bold"><i class="fas fa-calendar-day me-1"></i>جلسة تدريبية مجدولة</span> ';
                }
                badgeBox.innerHTML = badges;
            }

            const modalEl = document.getElementById('calEventModal');
            if (!modalEl) return;

            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                let m = bootstrap.Modal.getInstance(modalEl);
                if (!m) m = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true });
                m.show();
            } else {
                modalEl.classList.add('show');
                modalEl.style.display = 'block';
                modalEl.setAttribute('aria-modal', 'true');
                document.body.classList.add('modal-open');
            }
        }

        function closeCalendarModal() {
            const modalEl = document.getElementById('calEventModal');
            if (!modalEl) return;

            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const m = bootstrap.Modal.getInstance(modalEl);
                if (m) {
                    try { m.hide(); } catch(e) {}
                }
            }

            modalEl.classList.remove('show');
            modalEl.style.display = 'none';
            modalEl.setAttribute('aria-modal', 'false');
            modalEl.removeAttribute('role');
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('padding-right');
            document.body.style.removeProperty('overflow');

            document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeCalendarModal();
            }
        });
    </script>
@endpush