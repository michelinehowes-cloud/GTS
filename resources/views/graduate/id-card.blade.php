@extends('layouts.app')

@section('title', 'بطاقتي الرقمية')

@section('content')
<div class="container-fluid px-2 px-md-3">
    <!-- Unified Page Hero Banner -->
    <x-page-hero
        title="بطاقتي الرقمية الذكية"
        subtitle="استخدم هذه البطاقة لتسجيل حضورك في ورش العمل، البرامج التدريبية، وأجنحة معارض التوظيف"
        icon="fas fa-id-badge"
        :breadcrumbs="[
            ['label' => 'منصة الخريجين', 'url' => route('graduate.dashboard')],
            ['label' => 'بطاقتي الرقمية']
        ]"
        badge="هوية معتمدة"
        badgeIcon="fas fa-check-circle"
    >
        <button onclick="window.print()" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-print fs-6"></i>
            <span>طباعة البطاقة</span>
        </button>
        <a href="{{ route('graduate.dashboard') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-home fs-6"></i>
            <span>لوحة التحكم</span>
        </a>
    </x-page-hero>

    <!-- البطاقة -->
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8">
            <div class="card-modern overflow-hidden p-0" id="id-card-container">
                <!-- الجزء العلوي من البطاقة -->
                <div class="p-4 text-center text-white position-relative" style="background: linear-gradient(135deg, #045db0 0%, #1e3a8a 100%);">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="rounded-circle mb-3 border border-3 border-white shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                    <h4 class="fw-bold mb-1">{{ $graduate->name }}</h4>
                    <p class="mb-0 text-white-50 small">خريج - جامعة طرابلس</p>
                </div>
                
                <!-- الجزء الأوسط - المعلومات -->
                <div class="card-body p-4 bg-white">
                    <div class="row mb-4 g-3">
                        <div class="col-12">
                            <div class="d-flex flex-column bg-light rounded-3 p-3 text-center border">
                                <span class="text-muted small fw-bold mb-1">الرقم الجامعي / الوطني</span>
                                <span class="text-dark fw-bold fs-5">{{ $graduate->graduateData->university_id ?? $graduate->graduateData->national_id ?? '---' }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex flex-column bg-light rounded-3 p-3 text-center border h-100">
                                <span class="text-muted small fw-bold mb-1">الكلية</span>
                                <span class="text-dark fw-bold" style="font-size: 0.9rem;">{{ $graduate->graduateData->faculty ?? '---' }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex flex-column bg-light rounded-3 p-3 text-center border h-100">
                                <span class="text-muted small fw-bold mb-1">القسم</span>
                                <span class="text-dark fw-bold" style="font-size: 0.9rem;">{{ $graduate->graduateData->department ?? '---' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- الـ QR Code -->
                    <div class="text-center mt-4">
                        <div class="d-inline-block p-3 bg-white border rounded-4 shadow-sm position-relative">
                            <div class="position-absolute top-0 start-0 w-25 h-25 border-top border-start border-primary border-3 rounded-top-4 rounded-start-4" style="margin: -2px;"></div>
                            <div class="position-absolute top-0 end-0 w-25 h-25 border-top border-end border-primary border-3 rounded-top-4 rounded-end-4" style="margin: -2px;"></div>
                            <div class="position-absolute bottom-0 start-0 w-25 h-25 border-bottom border-start border-primary border-3 rounded-bottom-4 rounded-start-4" style="margin: -2px;"></div>
                            <div class="position-absolute bottom-0 end-0 w-25 h-25 border-bottom border-end border-primary border-3 rounded-bottom-4 rounded-end-4" style="margin: -2px;"></div>
                            
                            <div id="qr-code" class="d-flex justify-content-center p-2"></div>
                        </div>
                        <p class="text-muted small mt-3 fw-bold mb-0">
                            <i class="fas fa-qrcode me-1 text-primary"></i>
                            امسح الرمز لتسجيل الحضور
                        </p>
                    </div>
                </div>
                
                <!-- الجزء السفلي -->
                <div class="card-footer bg-light border-0 text-center py-3">
                    <small class="text-muted fw-bold">مكتب تدريب الخريجين - جامعة طرابلس</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #id-card-container, #id-card-container * {
            visibility: visible;
        }
        #id-card-container {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            max-width: 400px;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // إنشاء الـ QR Code
        // يحتوي على المعرف الفريد الخاص بالخريج لمسحه في الدورات والمعارض
        new QRCode(document.getElementById('qr-code'), {
            text: "{{ route('graduate.profile.public', $graduate->id) }}",
            width: 200,
            height: 200,
            colorDark : "#1e3a8a",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    });
</script>
@endsection
