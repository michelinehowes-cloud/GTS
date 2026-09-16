@extends('layouts.guest')

@section('title', 'تم استلام طلب التسجيل بنجاح - جامعة طرابلس')

@push('styles')
<style>
    body {
        background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%);
        min-height: 100vh;
    }
    .success-card {
        max-width: 650px;
        margin: 3.5rem auto;
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
        border: 1px solid rgba(226, 232, 240, 0.9);
        overflow: hidden;
        text-align: center;
        padding: 3rem 2rem;
    }
    .check-icon-box {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #dcfce7;
        color: #16a34a;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 20px rgba(22, 163, 74, 0.15);
    }
    .step-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        text-align: right;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }
    .step-num {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #0284c7;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 2px;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="success-card">
        <div class="check-icon-box animate__animated animate__bounceIn">
            <i class="fas fa-check"></i>
        </div>

        <h3 class="fw-bold text-dark mb-2">تم استلام طلب تسجيل شركتكم بنجاح!</h3>
        <p class="text-muted mb-4" style="font-size: 1rem; line-height: 1.6;">
            شكراً لاهتمامكم بالشراكة مع مكتب تدريب وتأهيل الخريجين بجامعة طرابلس. تم تسجيل بيانات شركتكم وحسابكم في المنظومة وهو قيد المراجعة حالياً.
        </p>

        @if(session('company_name'))
        <div class="alert alert-light border text-dark fw-bold mb-4 py-2 px-3 rounded-3 d-inline-block">
            <i class="fas fa-building me-1 text-primary"></i>
            {{ session('company_name') }}
        </div>
        @endif

        <div class="mb-4">
            <div class="step-box">
                <div class="step-num">1</div>
                <div>
                    <strong class="d-block text-dark" style="font-size: 0.92rem;">مراجعة الطلب</strong>
                    <small class="text-muted">يقوم فريق وحدة الشراكات والاتصال بمراجعة بيانات المؤسسة والتأكد من استيفاء المتطلبات.</small>
                </div>
            </div>

            <div class="step-box">
                <div class="step-num">2</div>
                <div>
                    <strong class="d-block text-dark" style="font-size: 0.92rem;">الاعتماد والتفعيل</strong>
                    <small class="text-muted">عند اعتماد الحساب، سيصلكم إشعار بالبريد الإلكتروني يفيد بتفعيل حسابكم وإمكانية تسجيل الدخول.</small>
                </div>
            </div>

            <div class="step-box">
                <div class="step-num">3</div>
                <div>
                    <strong class="d-block text-dark" style="font-size: 0.92rem;">إضافة ونشر الفرص</strong>
                    <small class="text-muted">ستتمكنون من نشر الشواغر الوظيفية وبرامج التدريب واستقبال ترشيحات أفضل خريجي جامعة طرابلس.</small>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="{{ route('home') }}" class="btn btn-primary px-4 py-2 rounded-3 fw-bold">
                <i class="fas fa-home me-1"></i> العودة للرئيسية
            </a>
            <a href="{{ url('/?open_login=1') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">
                <i class="fas fa-sign-in-alt me-1"></i> صفحة تسجيل الدخول
            </a>
        </div>
    </div>
</div>
@endsection
