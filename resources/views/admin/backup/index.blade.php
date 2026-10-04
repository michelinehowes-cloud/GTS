@extends('layouts.app')

@section('title', 'النسخ الاحتياطي وإدارة الطوارئ')
@section('page-title', 'النسخ الاحتياطي وإدارة الطوارئ')

@push('styles')
<style>
    .backup-card {
        border-radius: 14px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid rgba(0,0,0,0.06);
    }
    .backup-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    }
    .action-btn-pill {
        border-radius: 30px;
        padding: 0.35rem 0.85rem;
        font-size: 0.8rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.2s ease;
    }
    .action-btn-pill:hover {
        transform: translateY(-1px);
    }
    .code-pill {
        background: #f1f5f9;
        color: #0f172a;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        font-family: monospace;
        font-size: 0.82rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">

    {{-- رسائل التنبيه والنجاح --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center mb-4" role="alert">
            <i class="fas fa-check-circle fs-5 me-2"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center mb-4" role="alert">
            <i class="fas fa-exclamation-triangle fs-5 me-2"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- بطاقة الترويسة الرئيسية --}}
    <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden bg-primary text-white" style="background: linear-gradient(135deg, #0d6efd 0%, #0a4ebd 100%) !important;">
        <div class="card-body p-4 position-relative">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="badge bg-white text-primary mb-2 px-3 py-1 rounded-pill fw-bold">
                        <i class="fas fa-shield-alt me-1"></i> منظومة حماية البيانات الكبرى
                    </span>
                    <h3 class="fw-bold mb-2">إدارة النسخ الاحتياطي واستعادة الطوارئ (Disaster Recovery)</h3>
                    <p class="text-white-50 mb-0" style="font-size: 0.95rem;">
                        تحكم كامل في أخذ وتنزيل واستعادة النسخ الاحتياطية لقاعدة بيانات المنظومة، مع دعم التنزيل المباشر لجهازك أو الربط مع التخزين السحابي (AWS S3 / Google Drive).
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <form action="{{ route('admin.backup.create') }}" method="POST" class="d-inline" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').innerHTML='<i class=\"fas fa-spinner fa-spin me-2\"></i> جاري الإنشاء...';">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-lg rounded-pill shadow-sm px-4 fw-bold text-dark">
                            <i class="fas fa-plus-circle me-1"></i> أخذ نسخة احتياطية فورية الآن
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- بطاقات المؤشرات السريعة --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card backup-card bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">إجمالي النسخ المتوفرة</div>
                        <div class="fs-4 fw-bold text-dark mt-1">{{ $stats['total_count'] }} نسخة</div>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                        <i class="fas fa-database fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card backup-card bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">المساحة المستخدمة</div>
                        <div class="fs-4 fw-bold text-success mt-1">{{ $stats['total_size'] }}</div>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                        <i class="fas fa-hdd fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card backup-card bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">آخر نسخة منشأة</div>
                        <div class="fs-6 fw-bold text-dark mt-1 text-truncate" style="max-width: 160px;" title="{{ $stats['latest_backup'] }}">
                            {{ $stats['latest_backup_human'] }}
                        </div>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                        <i class="fas fa-clock fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card backup-card bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">الجدولة الآلية اليومية</div>
                        <div class="fs-6 fw-bold text-primary mt-1">الساعة 02:00 فجراً</div>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                        <i class="fas fa-calendar-check fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- جدول النسخ الاحتياطية المتوفرة --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <h5 class="card-title fw-bold mb-0 text-dark">
                <i class="fas fa-list text-primary me-2"></i> سجل ملفات النسخ الاحتياطي المحفوظة
            </h5>
            <span class="badge bg-secondary rounded-pill px-3 py-1">سياسة الحفظ: أحدث 14 نسخة</span>
        </div>
        <div class="card-body p-0">
            @if(empty($backups))
                <div class="text-center py-5">
                    <i class="fas fa-database fa-3x text-muted mb-3"></i>
                    <h5 class="text-secondary fw-bold">لا توجد أي نسخ احتياطية مسجلة حتى الآن</h5>
                    <p class="text-muted small">يمكنك النقر على الزر الأصفر بالأعلى لأخذ أول نسخة احتياطية من قاعدة البيانات.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-center">
                        <thead class="bg-light text-secondary small">
                            <tr>
                                <th class="text-start ps-4">اسم ملف النسخة</th>
                                <th>تاريخ ووقت الإنشاء</th>
                                <th>الحجم</th>
                                <th>الحالة</th>
                                <th class="text-center pe-4">خيارات الطوارئ والتنزيل</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($backups as $index => $backup)
                                <tr>
                                    <td class="text-start ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2 text-primary">
                                                <i class="fas fa-file-code fa-lg"></i>
                                            </div>
                                            <div>
                                                <span class="code-pill fw-bold">{{ $backup['filename'] }}</span>
                                                @if($index === 0)
                                                    <span class="badge bg-success ms-1 small">الأحدث</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark small">{{ $backup['created_at'] }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $backup['created_at_ar'] }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1">{{ $backup['size'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                            <i class="fas fa-check-circle me-1"></i> مكتمل وسليم
                                        </span>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            {{-- تنزيل على الحاسوب الشخصي --}}
                                            <a href="{{ route('admin.backup.download', $backup['filename']) }}" 
                                               class="btn btn-primary action-btn-pill shadow-sm" 
                                               title="تنزيل النسخة على جهازك الشخصي">
                                                <i class="fas fa-download"></i> تنزيل لجهازك
                                            </a>

                                            {{-- استعادة النسخة --}}
                                            <button type="button" 
                                                    class="btn btn-outline-warning action-btn-pill" 
                                                    onclick="confirmRestore('{{ $backup['filename'] }}')"
                                                    title="استعادة قاعدة البيانات من هذه النسخة">
                                                <i class="fas fa-history"></i> استرجاع
                                            </button>

                                            {{-- رفع للسحابة --}}
                                            <form action="{{ route('admin.backup.upload-cloud', $backup['filename']) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-outline-info action-btn-pill" 
                                                        title="رفع هذه النسخة إلى التخزين السحابي (S3)">
                                                    <i class="fas fa-cloud-upload-alt"></i> للسحابة
                                                </button>
                                            </form>

                                            {{-- حذف النسخة --}}
                                            <form action="{{ route('admin.backup.destroy', $backup['filename']) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف ملف هذه النسخة الاحتياطية نهائياً؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-outline-danger action-btn-pill" 
                                                        title="حذف هذا الملف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- بطاقة إرشادية حول ربط التخزين السحابي الخارجي (AWS S3 و Google Drive) --}}
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="fab fa-aws text-warning me-2 fs-4"></i> ربط التخزين السحابي الخارجي (AWS S3 / Cloudflare R2)
                    </h5>
                    <p class="text-muted small mb-3">
                        لحماية بياناتك خارج الخادم الفيزيائي (Off-site Disaster Recovery)، يمكنك تكوين بيانات مفاتيح التخزين السحابي في ملف <span class="code-pill">.env</span> لرفع النسخ تلقائياً:
                    </p>
                    <pre class="bg-dark text-light p-3 rounded-3 small mb-0" style="direction: ltr; text-align: left;"><code>BACKUP_CLOUD_DISK=s3
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your_backup_bucket
AWS_ENDPOINT=   # يُترك فارغاً لـ AWS، أو يوضع رابط Cloudflare R2</code></pre>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="fas fa-laptop-code text-primary me-2 fs-4"></i> نصائح الأمان وتفعيل الـ Cron Job بالسيرفر
                    </h5>
                    <ul class="text-muted small mb-3 ps-3">
                        <li class="mb-2"><strong>التنزيل اليدوي الأسبوعي:</strong> يُنصح المدير بتحميل أحدث ملف <span class="code-pill">.sql</span> أسبوعياً وحفظه على وحدة تخزين خارجية (Flash Drive / Hard Disk).</li>
                        <li class="mb-2"><strong>تفعيل الجدولة على السيرفر:</strong> تأكد من إضافة سطر الـ Cron التالي في لوحة cPanel أو سيرفر Linux:</li>
                    </ul>
                    <pre class="bg-dark text-light p-3 rounded-3 small mb-0" style="direction: ltr; text-align: left;"><code>* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1</code></pre>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- نافذة التأكيد قبل الاسترجاع (Restore Modal) --}}
<div class="modal fade" id="restoreModal" tabindex="-1" aria-labelledby="restoreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-warning bg-opacity-10 border-0">
                <h5 class="modal-title fw-bold text-dark" id="restoreModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i> تأكيد استعادة قاعدة البيانات
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="restoreForm" method="POST" action="">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-danger fw-bold mb-2">⚠️ تحذير شديد الأهمية:</p>
                    <p class="text-muted small mb-3">
                        هذه العملية ستقوم باستبدال كافة البيانات الحالية بقاعدة البيانات واسترجاع البيانات المخزنة في النسخة المحددة:
                    </p>
                    <div class="alert alert-secondary py-2 px-3 rounded-3 mb-3">
                        <span id="restoreFileName" class="code-pill fw-bold"></span>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirmCheckbox" required>
                        <label class="form-check-label small text-dark fw-bold" for="confirmCheckbox">
                            أقر بأنني على دراية بأن البيانات الحالية سيتم استبدالها ببيانات هذه النسخة.
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light p-3 rounded-bottom-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark" id="submitRestoreBtn" disabled>
                        <i class="fas fa-history me-1"></i> استعادة البيانات الآن
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmRestore(filename) {
        document.getElementById('restoreFileName').innerText = filename;
        const form = document.getElementById('restoreForm');
        form.action = `/admin/backup/restore/${encodeURIComponent(filename)}`;
        document.getElementById('confirmCheckbox').checked = false;
        document.getElementById('submitRestoreBtn').disabled = true;

        const modal = new bootstrap.Modal(document.getElementById('restoreModal'));
        modal.show();
    }

    document.getElementById('confirmCheckbox').addEventListener('change', function () {
        document.getElementById('submitRestoreBtn').disabled = !this.checked;
    });

    document.getElementById('restoreForm').addEventListener('submit', function () {
        const btn = document.getElementById('submitRestoreBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري الاستعادة...';
    });
</script>
@endpush
@endsection
