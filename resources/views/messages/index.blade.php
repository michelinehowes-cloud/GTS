@extends('layouts.app')

@section('title', 'صندوق الرسائل والمحادثات')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ auth()->user()->dashboard_route }}" class="text-decoration-none">لوحة التحكم</a></li>
                    <li class="breadcrumb-item active" aria-current="page">صندوق الرسائل</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="fas fa-envelope text-primary"></i>
                <span>صندوق الرسائل والمحادثات</span>
            </h1>
        </div>
        <div>
            <a href="{{ auth()->user()->dashboard_route }}" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 fw-bold text-nowrap shadow-sm">
                <i class="fas fa-arrow-right me-1"></i> العودة للوحة التحكم
            </a>
        </div>
    </div>

    <!-- Conversations Container -->
    <div class="card border-0 rounded-4 shadow-sm overflow-hidden" style="background: #ffffff;">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold text-dark fs-6">
                <i class="fas fa-comments text-primary me-2"></i>جميع المحادثات النشطة
            </h5>
            <span class="badge bg-light text-muted border px-2.5 py-1.5 rounded-pill font-monospace">
                {{ count($conversations) }} محادثة
            </span>
        </div>

        @if(empty($conversations))
            <div class="empty-state py-5 text-center my-4">
                <div class="rounded-circle bg-light text-muted d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px;">
                    <i class="fas fa-inbox fa-2x text-primary opacity-50"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">صندوق الوارد فارغ</h4>
                <p class="text-muted mb-4 mx-auto" style="max-width: 420px; font-size: 0.92rem;">
                    @if(auth()->user()->role === 'graduate')
                        لم تستلم أي رسائل بعد. عندما تبدي الشركات الشريكة اهتماماً بملفك الشخصي أو ترشيحك لإحدى الفرص، ستصلك رسائلهم هنا مباشرة.
                    @else
                        لا توجد محادثات نشطة حالياً. يمكنك بدء التواصل مع الخريجين من خلال تصفح مرشحي معرض التوظيف أو السير الذاتية.
                    @endif
                </p>
                <a href="{{ auth()->user()->dashboard_route }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
                    <i class="fas fa-home me-1"></i> العودة للرئيسية
                </a>
            </div>
        @else
            <div class="list-group list-group-flush">
                @foreach($conversations as $userId => $conv)
                    @php
                        $other = $conv['user'];
                        $isCompany = $other->role === 'company';
                        $displayName = ($isCompany && $other->company) ? $other->company->name : $other->name;
                        $hasUnread = $conv['unread_count'] > 0;
                        $initial = mb_substr($displayName, 0, 1);
                    @endphp
                    <a href="{{ route('messages.show', $userId) }}" 
                       class="list-group-item list-group-item-action p-3.5 p-md-4 border-bottom text-decoration-none transition-hover {{ $hasUnread ? 'bg-light' : '' }}"
                       style="transition: all 0.2s ease; border-right: 4px solid {{ $hasUnread ? '#2563eb' : 'transparent' }} !important;">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3 min-w-0 flex-grow-1">
                                <!-- Avatar -->
                                <div class="position-relative flex-shrink-0">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm" 
                                         style="width: 52px; height: 52px; font-size: 1.25rem; font-weight: bold; background: {{ $isCompany ? 'linear-gradient(135deg, #0d3882 0%, #1e40af 100%)' : 'linear-gradient(135deg, #059669 0%, #10b981 100%)' }}; color: #ffffff;">
                                        @if($isCompany)
                                            <i class="fas fa-building" style="font-size: 1.15rem;"></i>
                                        @else
                                            {{ $initial }}
                                        @endif
                                    </div>
                                    @if($hasUnread)
                                        <span class="position-absolute top-0 start-0 translate-middle p-1.5 bg-danger border border-white rounded-circle"></span>
                                    @endif
                                </div>

                                <!-- User Details & Message Preview -->
                                <div class="min-w-0 flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <h5 class="mb-0 fw-bold {{ $hasUnread ? 'text-dark' : 'text-secondary' }}" style="font-size: 1.02rem;">
                                            {{ $displayName }}
                                        </h5>
                                        @if($isCompany)
                                            <span class="badge rounded-pill px-2 py-0.5" style="background: rgba(13, 56, 130, 0.08); color: #0d3882; font-size: 0.72rem; border: 1px solid rgba(13, 56, 130, 0.2);">
                                                <i class="fas fa-building me-1"></i>شركة شريكة
                                            </span>
                                        @else
                                            <span class="badge rounded-pill px-2 py-0.5" style="background: rgba(5, 150, 105, 0.08); color: #059669; font-size: 0.72rem; border: 1px solid rgba(5, 150, 105, 0.2);">
                                                <i class="fas fa-user-graduate me-1"></i>خريج
                                            </span>
                                        @endif
                                    </div>
                                    <p class="mb-0 text-truncate text-muted" style="max-width: 550px; font-size: 0.88rem; color: {{ $hasUnread ? '#1e293b' : '#64748b' }} !important; font-weight: {{ $hasUnread ? '600' : '400' }};">
                                        @if($conv['last_message']->sender_id == auth()->id())
                                            <i class="fas fa-reply text-muted ms-1" style="font-size: 0.75rem;"></i>
                                            <span class="text-secondary fw-semibold">أنت: </span>
                                        @endif
                                        {{ $conv['last_message']->content }}
                                    </p>
                                </div>
                            </div>

                            <!-- Meta / Time / Badge -->
                            <div class="text-end flex-shrink-0 d-flex flex-column align-items-end gap-1.5">
                                <div class="small text-muted font-monospace" style="font-size: 0.76rem;">
                                    {{ $conv['last_message']->created_at->diffForHumans() }}
                                </div>
                                @if($hasUnread)
                                    <span class="badge rounded-pill bg-danger px-2.5 py-1 shadow-sm" style="font-size: 0.72rem;">
                                        {{ $conv['unread_count'] }} {{ $conv['unread_count'] == 1 ? 'جديدة' : 'رسائل جديدة' }}
                                    </span>
                                @else
                                    <i class="fas fa-chevron-left text-muted opacity-50" style="font-size: 0.8rem;"></i>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
