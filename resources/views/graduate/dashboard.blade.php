@extends('layouts.app')

@section('title', 'لوحة تحكم الخريج')

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
            <!-- Mobile Header Top Bar (Brand + Menu Trigger + Quick Icons) -->
            <div class="d-flex justify-content-between align-items-center w-100 mb-3 d-md-none">
                <span class="badge rounded-pill px-3 py-1.5 small" style="background: rgba(255, 255, 255, 0.22) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.3) !important; font-size: 0.75rem; backdrop-filter: blur(6px);">
                    <i class="fas fa-graduation-cap me-1 text-warning"></i>منصة الخريجين
                </span>
                <div class="d-flex align-items-center gap-2">
                    <!-- زر الرسائل المنزلق بالموبايل -->
                    <button type="button"
                            class="btn btn-sm text-white rounded-circle d-flex align-items-center justify-content-center border-0 position-relative shadow-none"
                            onclick="openMessagesDrawer()"
                            style="width: 38px; height: 38px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(4px);"
                            title="صندوق الرسائل">
                        <i class="fas fa-envelope"></i>
                        @if($dashUnreadMessagesCount > 0)
                            <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-danger" style="font-size: 0.62rem; min-width: 17px; height: 17px; padding: 2px 4px;">{{ $dashUnreadMessagesCount }}</span>
                        @endif
                    </button>

                    <!-- زر الإشعارات المنزلق بالموبايل -->
                    <button type="button"
                            class="btn btn-sm text-white rounded-circle d-flex align-items-center justify-content-center border-0 position-relative shadow-none"
                            onclick="openNotificationsDrawer()"
                            style="width: 38px; height: 38px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(4px);"
                            title="مركز الإشعارات">
                        <i class="fas fa-bell"></i>
                        @if($dashUnreadNotifsCount > 0)
                            <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-danger" style="font-size: 0.62rem; min-width: 17px; height: 17px; padding: 2px 4px;">{{ $dashUnreadNotifsCount }}</span>
                        @endif
                    </button>

                    <!-- زر القائمة الجانبية -->
                    <button type="button" class="btn btn-sm text-white rounded-circle d-flex align-items-center justify-content-center border-0 shadow-none" style="width: 38px; height: 38px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(4px);" onclick="window.toggleSidebarFunc ? window.toggleSidebarFunc() : null">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>

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

    <!-- التقويم والإشعارات -->
    <div class="row g-3 g-md-4 mb-4">
        <!-- التقويم التفاعلي / الأجندة -->
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm h-100">
                <div class="card-header py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <h5 class="card-title mb-0 text-primary fw-bold fs-6 fs-md-5">
                            <i class="fas fa-calendar-alt me-2"></i>تقويم التدريبات والفعاليات
                        </h5>
                        <!-- Legend Bar for Desktop -->
                        <div class="d-none d-xl-flex align-items-center gap-2">
                            <span class="badge rounded-pill" style="background: rgba(13, 56, 130, 0.08); color: #0d3882; font-size: 0.72rem; font-weight: 700;">
                                <span class="d-inline-block rounded-circle me-1" style="width: 7px; height: 7px; background: #0d3882;"></span> دورات
                            </span>
                            <span class="badge rounded-pill" style="background: rgba(5, 150, 105, 0.08); color: #059669; font-size: 0.72rem; font-weight: 700;">
                                <span class="d-inline-block rounded-circle me-1" style="width: 7px; height: 7px; background: #059669;"></span> ورش
                            </span>
                            <span class="badge rounded-pill" style="background: rgba(29, 78, 216, 0.08); color: #1d4ed8; font-size: 0.72rem; font-weight: 700;">
                                <span class="d-inline-block rounded-circle me-1" style="width: 7px; height: 7px; background: #1d4ed8;"></span> عملي
                            </span>
                            <span class="badge rounded-pill" style="background: rgba(217, 119, 6, 0.08); color: #d97706; font-size: 0.72rem; font-weight: 700;">
                                <span class="d-inline-block rounded-circle me-1" style="width: 7px; height: 7px; background: #d97706;"></span> ندوات
                            </span>
                        </div>
                    </div>
                    <!-- Switcher for Mobile -->
                    <div class="btn-group btn-group-sm d-md-none" role="group">
                        <button type="button" class="btn btn-outline-primary active" id="btnShowAgenda" onclick="toggleCalendarView('agenda')">
                            <i class="fas fa-list me-1"></i>الأجندة
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="btnShowCalendar" onclick="toggleCalendarView('calendar')">
                            <i class="fas fa-calendar me-1"></i>التقويم
                        </button>
                    </div>
                </div>
                <div class="card-body p-3">
                    <!-- Agenda List for Mobile -->
                    <div id="mobile-agenda-view" class="d-md-none">
                        @if(isset($calendarTrainings) && count($calendarTrainings) > 0)
                            <div class="d-flex flex-column gap-2">
                                @foreach($calendarTrainings as $event)
                                    <div class="p-3 rounded-3 border-start border-4 border-primary bg-light d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 fs-6">{{ $event['title'] ?? 'برنامج تدريبي' }}</h6>
                                            <div class="text-muted small d-flex align-items-center gap-2">
                                                <span><i class="fas fa-calendar-day text-primary me-1"></i> {{ \Carbon\Carbon::parse($event['start'])->format('Y-m-d') }}</span>
                                            </div>
                                        </div>
                                        @if(!empty($event['url']))
                                            <a href="{{ $event['url'] }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                                عرض التفاصيل
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-calendar-check fa-2x mb-2 opacity-50"></i>
                                <p class="small mb-0">لا توجد تدريبات مجدولة حالياً</p>
                            </div>
                        @endif
                    </div>

                    <!-- Full Calendar Container -->
                    <div id="calendar-container">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الجانب الأيسر: رسائل الشركات والتنبيهات -->
        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm h-100 d-flex flex-column">
                <div class="card-header py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="card-title mb-0 text-primary fw-bold fs-6">
                            <i class="fas fa-envelope-open-text me-1 text-primary"></i>رسائل وتنبيهات الشركات
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

    <!-- الصف السفلي: التدريبات الموصى بها وطلباتي الأخيرة -->
    <div class="row g-4 mb-4">
        <!-- التدريبات الموصى بها -->
        <div class="col-lg-6">
            <div class="card card-modern border-0 rounded-4 shadow-sm h-100">
                <div class="card-header py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-star me-2 text-warning"></i>التدريبات الموصى بها
                    </h5>
                    <a href="{{ route('graduate.trainings') }}" class="btn btn-sm btn-outline-primary-modern">
                        عرض الكل <i class="fas fa-arrow-left ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-3">
                    @if($recommendedTrainings->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recommendedTrainings as $training)
                                <a href="{{ route('graduate.trainings.show', $training->id) }}" class="list-group-item list-group-item-action border-bottom px-2 py-3">
                                    <div class="d-flex align-items-start">
                                        <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center p-3 me-3 flex-shrink-0" style="width: 44px; height: 44px;">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold text-dark">{{ $training->title }}</h6>
                                            <p class="text-muted small mb-2">{{ Str::limit($training->description, 70) }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    {{ $training->start_date->format('Y-m-d') }}
                                                </small>
                                                <span class="badge bg-light text-primary border border-primary rounded-pill px-2 py-1 small">
                                                    {{ $training->type_arabic ?? 'تدريب' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-graduation-cap fa-3x text-muted mb-3 opacity-50"></i>
                            <p class="text-muted mb-0">لا توجد تدريبات موصى بها حالياً</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- طلباتي الأخيرة -->
        <div class="col-lg-6">
            <div class="card card-modern border-0 rounded-4 shadow-sm h-100">
                <div class="card-header py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-history me-2 text-info"></i>طلباتي الأخيرة
                    </h5>
                    <span class="badge bg-light text-primary border border-primary rounded-pill px-3 py-1">{{ $myApplications }}</span>
                </div>
                <div class="card-body p-3">
                    @if($myRecentApplications->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($myRecentApplications as $application)
                                <div class="list-group-item border-bottom px-2 py-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark">{{ $application->training->title }}</h6>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $application->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <div>
                                            @if($application->status == 'approved')
                                                <span class="badge bg-light text-success border border-success rounded-pill px-3 py-1">مقبول</span>
                                            @elseif($application->status == 'rejected')
                                                <span class="badge bg-light text-danger border border-danger rounded-pill px-3 py-1">مرفوض</span>
                                            @else
                                                <span class="badge bg-light text-warning border border-warning rounded-pill px-3 py-1">قيد المراجعة</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3 opacity-50"></i>
                            <p class="text-muted mb-3">لا توجد طلبات سابقة</p>
                            <a href="{{ route('graduate.trainings') }}" class="btn btn-sm btn-primary-modern">
                                <i class="fas fa-paper-plane me-1"></i> تقديم طلب جديد
                            </a>
                        </div>
                    @endif
                </div>
            </div>
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

@push('styles')
<style>
    /* تصميم التقويم المعتمد وفق هوية المنظومة */
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
    .fc .fc-button-primary:hover, .fc .fc-button-primary:focus, .fc .fc-button-primary.fc-button-active {
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

    /* خلايا الأيام */
    .fc-theme-standard td, .fc-theme-standard th {
        border-color: #edf2f7 !important;
    }
    html[data-theme='dark'] .fc-theme-standard td,
    html[data-theme='dark'] .fc-theme-standard th,
    html[data-theme='dark'] .fc-daygrid-day {
        background-color: #111827 !important;
        border-color: #1e293b !important;
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
    html[data-theme='dark'] .fc-daygrid-day-number {
        color: #94a3b8 !important;
    }

    .fc-day-other .fc-daygrid-day-number {
        color: #94a3b8 !important;
        opacity: 0.55;
    }
    html[data-theme='dark'] .fc-day-other {
        background-color: #0b1120 !important;
        opacity: 0.65;
    }
    html[data-theme='dark'] .fc-day-other .fc-daygrid-day-number {
        color: #64748b !important;
    }

    .fc-day-today {
        background-color: #eff6ff !important;
    }
    html[data-theme='dark'] .fc-day-today {
        background-color: rgba(59, 130, 246, 0.12) !important;
    }

    .fc-day-today .fc-daygrid-day-number {
        background-color: #0d3882 !important;
        color: #ffffff !important;
        font-weight: 800 !important;
        box-shadow: 0 2px 6px rgba(13, 56, 130, 0.3);
    }
    html[data-theme='dark'] .fc-day-today .fc-daygrid-day-number {
        background-color: #2563eb !important;
        color: #ffffff !important;
    }

    /* أزرار وعنوان التقويم بالوضع الليلي */
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

    /* نافذة المزيد للمناسبات المتعددة */
    .fc .fc-more-popover {
        border-radius: 12px !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
        border: 1px solid #e2e8f0 !important;
        overflow: hidden;
    }
    .fc .fc-more-popover .fc-popover-header {
        background: #f8fafc !important;
        font-weight: 700 !important;
        padding: 8px 12px !important;
        border-bottom: 1px solid #e2e8f0 !important;
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

    /* تثبيت ومنع أي ارتعاش أو اهتزاز في القوائم المنسدلة */
    .custom-header-dropdown {
        position: absolute !important;
        top: calc(100% + 8px) !important;
        transform: none !important;
        margin: 0 !important;
        background: #ffffff;
        box-shadow: 0 18px 45px rgba(0, 0, 0, 0.22) !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        border-radius: 16px !important;
        overflow: hidden !important;
        z-index: 1060 !important;
        animation: none !important;
    }

    /* جسر وهمي غير مرئي يربط الزر بالقائمة المنسدلة لمنع أي فجوة ماوس تسبب اهتزاز */
    .custom-header-dropdown::before {
        content: '';
        position: absolute;
        top: -12px;
        left: 0;
        right: 0;
        height: 12px;
        background: transparent;
        pointer-events: auto;
    }

    /* منع أي تحول أو اهتزاز نهائياً عند وضع الماوس على أزرار البانر أو القوائم */
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

    /* شارات الإشعارات لا تتداخل مع مؤشر الفأرة لمنع الارتجاج */
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

    /* شريط التمرير الداخلي السلس ومنع تسريب التمرير للنافذة */
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

    /* الوضع المظلم للقوائم المنسدلة */
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

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // HTML5 QR Code Scanner
            let html5QrcodeScanner;
            const qrModal = document.getElementById('qrScannerModal');

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

            function onScanSuccess(decodedText, decodedResult) {
                if (decodedText.startsWith('http')) {
                    html5QrcodeScanner.clear();
                    window.location.href = decodedText;
                } else {
                    alert('هذا الباركود غير صالح للاستخدام هنا.');
                }
            }

            function onScanFailure(error) {
                // Ignore scanning cycle ticks
            }

            // FullCalendar
            var calendarEl = document.getElementById('calendar');
            var calendar;
            if (calendarEl) {
                var isSmallScreen = window.innerWidth < 480;
                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'ar',
                    direction: 'rtl',
                    height: 'auto',
                    dayMaxEvents: 2, // يحول التراكم إلى كبسولة أنيقة (+X المزيد)
                    dayHeaderFormat: isSmallScreen ? { weekday: 'narrow' } : { weekday: 'short' },
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,listMonth'
                    },
                    buttonText: {
                        today: 'اليوم',
                        month: 'شهر',
                        week: 'أسبوع',
                        list: 'قائمة'
                    },
                    events: @json($calendarTrainings),
                    eventContent: function(arg) {
                        let type = (arg.event.extendedProps && arg.event.extendedProps.type) ? arg.event.extendedProps.type : 'course';
                        let iconClass = 'fa-graduation-cap';
                        if (type === 'workshop') iconClass = 'fa-tools';
                        else if (type === 'internship') iconClass = 'fa-laptop-code';
                        else if (type === 'seminar') iconClass = 'fa-bullhorn';

                        let customHtml = `
                            <div class="d-flex align-items-center gap-1.5 overflow-hidden w-100 text-white" style="line-height: 1.25;">
                                <i class="fas ${iconClass} me-1" style="font-size: 0.68rem; opacity: 0.9; flex-shrink: 0;"></i>
                                <span class="text-truncate fw-bold" style="font-size: 0.74rem;">${arg.event.title}</span>
                            </div>
                        `;
                        return { html: customHtml };
                    },
                    eventClick: function (info) {
                        if (info.event.url) {
                            window.location.href = info.event.url;
                            info.jsEvent.preventDefault();
                        }
                    }
                });

                setTimeout(function () {
                    calendar.render();
                    calendar.updateSize();
                }, 100);

                window.addEventListener('resize', function() {
                    if (calendar) {
                        calendar.setOption('dayHeaderFormat', window.innerWidth < 480 ? { weekday: 'narrow' } : { weekday: 'short' });
                        calendar.updateSize();
                    }
                });
            }

            // Mobile Calendar / Agenda View Toggle
            window.toggleCalendarView = function(view) {
                const agendaView = document.getElementById('mobile-agenda-view');
                const calendarContainer = document.getElementById('calendar-container');
                const btnAgenda = document.getElementById('btnShowAgenda');
                const btnCalendar = document.getElementById('btnShowCalendar');

                if (view === 'agenda') {
                    if (agendaView) agendaView.classList.remove('d-none');
                    if (calendarContainer) calendarContainer.classList.add('d-none');
                    btnAgenda?.classList.add('active');
                    btnCalendar?.classList.remove('active');
                } else {
                    if (agendaView) agendaView.classList.add('d-none');
                    if (calendarContainer) calendarContainer.classList.remove('d-none');
                    btnCalendar?.classList.add('active');
                    btnAgenda?.classList.remove('active');
                    setTimeout(() => {
                        if (calendar) {
                            calendar.render();
                            calendar.updateSize();
                        }
                    }, 50);
                }
            };

            // Set initial state for mobile
            if (window.innerWidth < 768) {
                const calendarContainer = document.getElementById('calendar-container');
                if (calendarContainer) {
                    calendarContainer.classList.add('d-none');
                }
            }
        });


    </script>
@endpush
@endsection