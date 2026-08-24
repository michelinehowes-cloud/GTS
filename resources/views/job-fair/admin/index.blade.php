@extends('layouts.app')

@section('title', 'إدارة معارض التوظيف')

@section('content')
<div class="container-fluid py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color: #0A1628">
                <i class="fas fa-store me-2" style="color: #F59E0B"></i>
                معارض التوظيف
            </h2>
            <p class="text-muted mb-0">إدارة وإنشاء معارض التوظيف</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('job-fair.public') }}" class="btn btn-outline-primary rounded-pill" target="_blank">
                <i class="fas fa-eye me-2"></i>الصفحة العامة
            </a>
            <a href="{{ route('job-fair.admin.create') }}" class="btn btn-warning rounded-pill text-dark fw-bold">
                <i class="fas fa-plus me-2"></i>معرض جديد
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success rounded-3 border-0 shadow-sm">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    </div>
    @endif

    @if($fairs->isEmpty())
    <div class="text-center py-5">
        <div style="font-size: 4rem">🎪</div>
        <h4 class="text-muted mt-3">لا يوجد معارض بعد</h4>
        <a href="{{ route('job-fair.admin.create') }}" class="btn btn-warning mt-3 rounded-pill px-4 fw-bold">
            <i class="fas fa-plus me-2"></i>إنشاء أول معرض
        </a>
    </div>
    @else
    <div class="row g-4">
        @foreach($fairs as $fair)
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="border-right: 4px solid
                @if($fair->status === 'published') #3B82F6
                @elseif($fair->status === 'ongoing') #10B981
                @elseif($fair->status === 'completed') #6B7280
                @else #F59E0B
                @endif !important">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">{{ $fair->title }}</h5>
                            <div class="text-muted small">
                                <i class="fas fa-calendar me-1"></i>{{ $fair->event_date->format('d/m/Y') }}
                                <span class="ms-3"><i class="fas fa-map-marker-alt me-1"></i>{{ $fair->location }}</span>
                            </div>
                        </div>
                        <span class="badge rounded-pill px-3 py-2 @if($fair->status === 'published') bg-primary @elseif($fair->status === 'ongoing') bg-success @elseif($fair->status === 'completed') bg-secondary @else bg-warning text-dark @endif">
                            @php
                                $statusLabels = ['draft'=>'مسودة','published'=>'منشور','ongoing'=>'جارٍ الآن','completed'=>'منتهي','cancelled'=>'ملغي'];
                            @endphp
                            {{ $statusLabels[$fair->status] ?? $fair->status }}
                        </span>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-4 text-center p-2 rounded-3" style="background: #f0f9ff">
                            <div class="fw-bold" style="font-size: 1.4rem; color: #2563EB">{{ $fair->registrations_count }}</div>
                            <small class="text-muted">خريج مسجل</small>
                        </div>
                        <div class="col-4 text-center p-2 rounded-3" style="background: #f0fdf4">
                            <div class="fw-bold" style="font-size: 1.4rem; color: #059669">{{ $fair->companies_count }}</div>
                            <small class="text-muted">شركة</small>
                        </div>
                        <div class="col-4 text-center p-2 rounded-3" style="background: #fef3c7">
                            <div class="fw-bold" style="font-size: 1.4rem; color: #D97706">{{ $fair->days_remaining }}</div>
                            <small class="text-muted">يوم متبقي</small>
                        </div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('job-fair.admin.show', $fair->id) }}" class="btn btn-sm btn-primary rounded-pill">
                            <i class="fas fa-eye me-1"></i>التفاصيل
                        </a>
                        <a href="{{ route('job-fair.admin.attendance', $fair->id) }}" class="btn btn-sm btn-success rounded-pill">
                            <i class="fas fa-qrcode me-1"></i>الحضور
                        </a>
                        <a href="{{ route('job-fair.admin.edit', $fair->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                            <i class="fas fa-edit me-1"></i>تعديل
                        </a>
                        <a href="{{ route('job-fair.admin.export', $fair->id) }}" class="btn btn-sm btn-outline-info rounded-pill">
                            <i class="fas fa-download me-1"></i>تصدير
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
