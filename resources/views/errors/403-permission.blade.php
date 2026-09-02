@extends('layouts.app')

@section('title', 'غير مصرح بالوصول - 403')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden text-center">
                <div class="card-header bg-danger bg-opacity-10 border-0 py-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger text-white shadow-sm" style="width: 80px; height: 80px; font-size: 2.2rem;">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
                <div class="card-body p-4 p-md-5">
                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill fw-bold mb-3" style="font-size: 0.85rem;">
                        <i class="fas fa-lock me-1"></i> حماية أمنية مشددة (403 Forbidden)
                    </span>
                    <h3 class="fw-bold text-dark mb-3">عذراً، هذه الوظيفة تتطلب صلاحيات محددة</h3>
                    <p class="text-muted leading-relaxed mb-4">
                        حسابك لا يمتلك الصلاحية الكافية للوصول إلى هذا القسم أو تنفيذ هذه العملية. تم تصميم النظام لمنح الموظفين وصولاً مخصصاً وفقاً لاختصاصاتهم المحددة.
                    </p>

                    @if(isset($requiredPermission))
                    <div class="p-3 bg-light rounded-3 border text-start mb-4">
                        <small class="text-muted d-block mb-1"><i class="fas fa-key me-1 text-warning"></i> الصلاحية المطلوبة:</small>
                        <code class="text-danger fw-bold fs-6">{{ $requiredPermission }}</code>
                    </div>
                    @endif

                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}" class="btn btn-primary px-4 py-2 rounded-pill fw-bold">
                            <i class="fas fa-arrow-right me-1"></i> العودة للصفحة السابقة
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
                            <i class="fas fa-home me-1"></i> لوحة التحكم الرئيسية
                        </a>
                    </div>
                </div>
                <div class="card-footer bg-light py-3 border-0 text-muted small">
                    إذا كنت تعتقد أنك بحاجة لهذه الصلاحية لأداء مهامك، يرجى التواصل مع <strong>مدير النظام (Super Admin)</strong>.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
