@extends('layouts.app')

@section('title', 'معارض التوظيف')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-store text-primary"></i> معارض التوظيف المشارك بها
        </h1>
    </div>

    @if($fairs->isEmpty())
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle fa-3x mb-3"></i>
            <h4>لا توجد معارض توظيف تشارك فيها شركتكم حالياً</h4>
            <p>تواصل مع إدارة الشراكات للانضمام للمعارض القادمة.</p>
        </div>
    @else
        <div class="row">
            @foreach($fairs as $fair)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h4 class="card-title fw-bold mb-1">{{ $fair->title }}</h4>
                                    <span class="badge {{ $fair->status == 'published' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ $fair->status == 'published' ? 'مفتوح' : 'مسودة/مغلق' }}
                                    </span>
                                </div>
                                <div class="text-primary fs-3">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="text-muted small mb-1">
                                    <i class="fas fa-map-marker-alt"></i> الموقع:
                                </div>
                                <strong>{{ $fair->location }}</strong>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small mb-1">
                                    <i class="fas fa-clock"></i> تاريخ المعرض:
                                </div>
                                <strong>{{ $fair->event_date->format('Y-m-d') }}</strong>
                            </div>

                            <hr>
                            
                            <div class="d-flex gap-2">
                                <a href="{{ route('company.job-fairs.scanner', $fair->id) }}" class="btn btn-primary w-50">
                                    <i class="fas fa-qrcode"></i> ماسح السير
                                </a>
                                <a href="{{ route('company.job-fairs.leads', $fair->id) }}" class="btn btn-outline-primary w-50">
                                    <i class="fas fa-users"></i> الخريجين
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
