@extends('layouts.app')

@section('title', 'ماسح السير الذاتية - ' . $fair->title)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-qrcode text-primary"></i> ماسح السير الذاتية (Lead Retrieval)
            </h1>
            <p class="text-muted mt-2">معرض: {{ $fair->title }}</p>
        </div>
        <a href="{{ route('company.job-fairs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة للمعارض
        </a>
    </div>

    <div class="row justify-content-center mt-5">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center py-4">
                    <i class="fas fa-expand fa-3x mb-3"></i>
                    <h4 class="mb-0 fw-bold">امسح تذكرة الخريج</h4>
                    <p class="mb-0 text-white-50 small mt-2">استخدم قارئ الباركود أو اكتب رقم التذكرة</p>
                </div>
                <div class="card-body p-5 text-center">
                    
                    <!-- إدخال الرمز -->
                    <div class="mb-4 position-relative">
                        <input type="text" id="qr-input" class="form-control form-control-lg text-center fw-bold" 
                            placeholder="QR Code أو رقم التذكرة" autocomplete="off" style="font-size: 1.5rem; letter-spacing: 2px;">
                        <div class="mt-2 text-muted small">اضغط Enter بعد كتابة الرقم إذا لم تكن تستخدم ماسحاً ضوئياً</div>
                    </div>
                    
                    <!-- النتائج -->
                    <div id="result-container" class="mt-4" style="min-height: 100px;">
                        <!-- النجاح -->
                        <div id="result-success" class="alert alert-success d-none align-items-center flex-column">
                            <i class="fas fa-check-circle fa-4x mb-3 text-success"></i>
                            <h4 class="alert-heading fw-bold">تم استلام بيانات الخريج!</h4>
                            <p class="mb-0 fs-5" id="result-name"></p>
                            <small class="text-muted" id="result-major"></small>
                        </div>

                        <!-- تم المسح مسبقاً -->
                        <div id="result-warning" class="alert alert-warning d-none align-items-center flex-column">
                            <i class="fas fa-exclamation-triangle fa-4x mb-3 text-warning"></i>
                            <h4 class="alert-heading fw-bold">مسجل مسبقاً</h4>
                            <p class="mb-0 fs-5" id="result-warning-msg"></p>
                        </div>

                        <!-- الخطأ -->
                        <div id="result-error" class="alert alert-danger d-none align-items-center flex-column">
                            <i class="fas fa-times-circle fa-4x mb-3 text-danger"></i>
                            <h4 class="alert-heading fw-bold">رمز غير صالح!</h4>
                            <p class="mb-0">تأكد من الرمز أو أن الخريج مسجل في هذا المعرض.</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light text-center py-3">
                    <a href="{{ route('company.job-fairs.leads', $fair->id) }}" class="btn btn-outline-primary fw-bold">
                        <i class="fas fa-users"></i> عرض جميع السير المستلمة
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const input = document.getElementById('qr-input');
const checkInUrl = "{{ route('company.job-fairs.store-visit', $fair->id) }}";
const csrfToken = "{{ csrf_token() }}";

// Auto-focus input
input.focus();

// Re-focus when clicking outside
document.body.addEventListener('click', function(e) {
    if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'BUTTON' && e.target.tagName !== 'A') {
        input.focus();
    }
});

// Listen for QR scan (Enter key)
input.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && this.value.trim()) {
        processQR(this.value.trim());
        this.value = '';
    }
});

function processQR(code) {
    fetch(checkInUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ qr_code: code })
    })
    .then(r => r.json())
    .then(data => {
        hideAllResults();

        if (!data.success) {
            showResult('error');
        } else if (data.already_visited) {
            document.getElementById('result-warning-msg').textContent = data.graduate_name;
            showResult('warning');
        } else {
            document.getElementById('result-name').textContent = data.graduate_name;
            document.getElementById('result-major').textContent = data.major || '';
            showResult('success');
        }

        // Auto-hide after 3 seconds
        setTimeout(hideAllResults, 3000);
        input.focus();
    })
    .catch(() => {
        showResult('error');
        input.focus();
    });
}

function showResult(type) {
    document.getElementById('result-' + type).classList.remove('d-none');
    document.getElementById('result-' + type).classList.add('d-flex');
}
function hideAllResults() {
    ['success', 'warning', 'error'].forEach(t => {
        const el = document.getElementById('result-' + t);
        el.classList.add('d-none');
        el.classList.remove('d-flex');
    });
}
</script>
@endpush
