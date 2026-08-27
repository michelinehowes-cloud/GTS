@extends('layouts.app')

@section('title', 'معارض التوظيف')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                <div class="card-body p-4 text-white position-relative">
                    <h1 class="h3 fw-bold mb-2 position-relative z-index-1">
                        <i class="fas fa-store me-2"></i> معارض التوظيف المشارك بها
                    </h1>
                    <p class="mb-0 text-white-50 position-relative z-index-1">
                        إدارة تواجد شركتك في معارض التوظيف ومتابعة المتقدمين بفعالية
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if($fairs->isEmpty())
        <div class="alert bg-white border-0 shadow-sm rounded-4 text-center p-5">
            <div class="text-primary mb-3">
                <i class="fas fa-info-circle fa-4x opacity-50"></i>
            </div>
            <h4 class="fw-bold text-dark">لا توجد معارض توظيف تشارك فيها شركتكم حالياً</h4>
            <p class="text-muted mb-0">تواصل مع إدارة الشراكات في الجامعة للانضمام للمعارض القادمة والوصول إلى أفضل الخريجين.</p>
        </div>
    @else
        <div class="row g-4">
            @foreach($fairs as $fair)
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative transition-all hover-shadow" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
                        <div class="card-header bg-white border-bottom pb-0 pt-4 px-4 border-0">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title fw-bold text-dark mb-2">{{ $fair->title }}</h5>
                                    @if($fair->status == 'published')
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 fw-semibold border border-success border-opacity-25">
                                            <i class="fas fa-door-open me-1"></i> مفتوح
                                        </span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1 fw-semibold border border-warning border-opacity-25">
                                            <i class="fas fa-lock me-1"></i> مسودة/مغلق
                                        </span>
                                    @endif
                                </div>
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex justify-content-center align-items-center" style="width: 48px; height: 48px;">
                                    <i class="fas fa-calendar-check fs-5"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body px-4 py-3 bg-white">
                            <div class="d-flex align-items-center mb-3 p-3 bg-light rounded-3">
                                <div class="text-primary me-3">
                                    <i class="fas fa-map-marker-alt fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">الموقع</div>
                                    <strong class="text-dark">{{ $fair->location }}</strong>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4 p-3 bg-light rounded-3">
                                <div class="text-primary me-3">
                                    <i class="fas fa-clock fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">تاريخ المعرض</div>
                                    <strong class="text-dark">{{ $fair->event_date->format('Y-m-d') }}</strong>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-white border-top-0 px-4 pb-4 pt-0">
                            <a href="{{ route('company.job-fairs.qr-booth', $fair->id) }}" target="_blank" class="btn btn-dark w-100 mb-2 rounded-pill py-2 fw-semibold shadow-sm">
                                <i class="fas fa-print me-1"></i> طباعة باركود الجناح
                            </a>
                            <div class="row g-2">
                                <div class="col-6">
                                    <a href="{{ route('company.job-fairs.scanner', $fair->id) }}" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold shadow-sm">
                                        <i class="fas fa-qrcode me-1"></i> ماسح السير
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('company.job-fairs.leads', $fair->id) }}" class="btn btn-outline-primary w-100 rounded-pill py-2 fw-semibold bg-white">
                                        <i class="fas fa-users me-1"></i> الخريجين
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <style>
            .hover-shadow:hover {
                transform: translateY(-5px);
                box-shadow: 0 1rem 3rem rgba(0,0,0,.15)!important;
            }
        </style>
    @endif
</div>
@endsection
