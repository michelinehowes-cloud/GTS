@extends('layouts.app')

@section('title', 'إدارة الشركات')
@section('page-title', 'إدارة الشركات')

@push('styles')
<style>
    .company-mobile-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid rgba(0, 0, 0, 0.06);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        padding: 1rem;
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
    }
    .company-mobile-card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
    }
    .action-circle-btn {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    .action-circle-btn:hover {
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    @php
        $approvedCount = $companies->filter(fn($c) => $c->is_approved && $c->partnership_status === 'active')->count();
        $pendingCount = $companies->filter(fn($c) => !$c->is_approved || $c->partnership_status !== 'active')->count();
    @endphp

    <!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
    <x-page-hero
        title="إدارة الشركات الشريكة"
        subtitle="متابعة واعتماد المؤسسات والشركات الشريكة وتوثيق العقود وفرص التوظيف"
        icon="fas fa-building"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('admin.dashboard')],
            ['label' => 'الشركات الشريكة']
        ]"
        secondaryBadge="معتمدة: {{ $approvedCount }}"
        secondaryBadgeIcon="fas fa-check-circle"
    >
        <button type="button" class="btn btn-outline-light text-white fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" onclick="copyCompanyRegisterLink()" style="font-size: 0.88rem; backdrop-filter: blur(4px); transition: all 0.2s ease;">
            <i class="fas fa-link fs-6"></i>
            <span id="copyLinkText">نسخ رابط تسجيل الشركات</span>
        </button>
        <a href="{{ route('job-fair.admin.index') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-calendar-alt fs-6"></i>
            <span>فعاليات</span>
        </a>
        <a href="{{ route('admin.companies.create') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-plus-circle fs-6"></i>
            <span>إضافة شركة جديدة</span>
        </a>
    </x-page-hero>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif


    <!-- إحصائيات سريعة للشركات (2x2 على الموبايل و4 على الديسكتوب) -->
    <div class="row g-2 g-md-3 mb-4">
        @include('components.stat-card', [
            'col' => 'col-6 col-md-3',
            'title' => 'إجمالي الشركات',
            'value' => $companies->count(),
            'icon' => 'fas fa-building',
            'color' => 'primary',
            'description' => 'المسجلة بالنظام'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-3',
            'title' => 'شركات معتمدة',
            'value' => $approvedCount,
            'icon' => 'fas fa-check-circle',
            'color' => 'success',
            'description' => 'جاهزة للتوظيف'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-3',
            'title' => 'قيد المراجعة',
            'value' => $pendingCount,
            'icon' => 'fas fa-clock',
            'color' => 'warning',
            'description' => 'بانتظار الاعتماد'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-3',
            'title' => 'أحدث إضافة',
            'value' => $companies->count() > 0 ? $companies->sortByDesc('created_at')->first()->created_at->format('d/m/Y') : '--',
            'icon' => 'fas fa-calendar-plus',
            'color' => 'info',
            'description' => 'تاريخ آخر تسجيل'
        ])
    </div>

    <!-- جدول وقائمة الشركات -->
    <div class="card-modern">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="card-title mb-0 text-primary fw-bold fs-6">
                    <i class="fas fa-building me-2"></i>قائمة الشركات المسجلة
                </h5>
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'partnership_officer')
                    <a href="{{ route('admin.companies.create') }}" class="btn btn-primary-modern btn-sm">
                        <i class="fas fa-plus me-1"></i>إضافة شركة جديدة
                    </a>
                @endif
            </div>
        </div>
        
        <div class="card-body p-0">
            @if($companies->count() > 0)
                {{-- 🖥️ عرض سطح المكتب: جدول متجاوب --}}
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-4 border-0 text-secondary small text-uppercase fw-bold" width="50">#</th>
                                <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">اسم الشركة</th>
                                <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">البريد الإلكتروني</th>
                                <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">المجال الصناعي</th>
                                <th class="py-3 border-0 text-secondary small text-uppercase fw-bold">العنوان</th>
                                <th class="py-3 border-0 text-secondary small text-uppercase fw-bold" width="140">حالة الاعتماد</th>
                                <th class="py-3 border-0 text-secondary small text-uppercase fw-bold" width="120">تاريخ الإضافة</th>
                                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'partnership_officer')
                                    <th class="py-3 px-4 border-0 text-secondary small text-uppercase fw-bold text-end" width="140">الإجراءات</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @foreach($companies as $company)
                            @php
                                $isCompanyApproved = $company->is_approved && $company->partnership_status === 'active';
                            @endphp
                            <tr>
                                <td class="px-4 text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light text-primary rounded-circle d-flex align-items-center justify-content-center me-2 flex-shrink-0" style="width: 36px; height: 36px;">
                                            <span class="fw-bold">{{ mb_substr($company->name, 0, 1) }}</span>
                                        </div>
                                        <div class="fw-bold text-dark">{{ $company->name }}</div>
                                    </div>
                                </td>
                                <td><a href="mailto:{{ $company->email }}" class="text-decoration-none text-primary">{{ $company->email }}</a></td>
                                <td>
                                    @if($company->industry)
                                        <span class="badge bg-light text-info fw-normal px-2 py-1">{{ $company->industry }}</span>
                                    @else
                                        <span class="text-muted small">--</span>
                                    @endif
                                </td>
                                <td><span class="text-muted small">{{ Str::limit($company->address ?? '--', 30) }}</span></td>
                                <td>
                                    @if($isCompanyApproved)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.75rem;">
                                            <i class="fas fa-check-circle me-1"></i>معتمدة
                                        </span>
                                    @elseif($company->partnership_status === 'expired')
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.75rem;">
                                            <i class="fas fa-history me-1"></i>منتهية
                                        </span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.75rem;">
                                            <i class="fas fa-clock me-1"></i>قيد المراجعة
                                        </span>
                                    @endif
                                </td>
                                <td><span class="text-muted small">{{ $company->created_at->format('Y-m-d') }}</span></td>
                                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'partnership_officer')
                                    <td class="px-4 text-end">
                                        <div class="d-flex align-items-center justify-content-end gap-1">
                                            {{-- زر عرض الملف --}}
                                            <a href="{{ route('admin.companies.show', $company->id) }}" class="btn btn-sm btn-outline-info action-circle-btn" data-bs-toggle="tooltip" title="عرض ملف الشركة">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            {{-- زر مراسلة الشركة --}}
                                            @if($company->user_id)
                                                <a href="{{ route('messages.show', $company->user_id) }}" class="btn btn-sm btn-outline-success action-circle-btn" data-bs-toggle="tooltip" title="مراسلة مسؤولي الشركة">
                                                    <i class="fas fa-comment-dots"></i>
                                                </a>
                                            @endif

                                            {{-- زر الاعتماد / التعليق --}}
                                            <form action="{{ route('admin.companies.toggle-approval', $company->id) }}" method="POST" class="m-0">
                                                @csrf
                                                @if($isCompanyApproved)
                                                    <button type="submit" class="btn btn-sm btn-outline-warning action-circle-btn" data-bs-toggle="tooltip" title="إلغاء الاعتماد">
                                                        <i class="fas fa-pause"></i>
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-sm btn-success action-circle-btn" data-bs-toggle="tooltip" title="اعتماد وتفعيل الشركة">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                            </form>

                                            {{-- زر التعديل --}}
                                            <a href="{{ route('admin.companies.edit', $company->id) }}" class="btn btn-sm btn-outline-primary action-circle-btn" data-bs-toggle="tooltip" title="تعديل بيانات الشركة">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            {{-- زر الحذف --}}
                                            <form action="{{ route('admin.companies.destroy', $company->id) }}" method="POST" class="m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger action-circle-btn" onclick="return confirm('هل أنت متأكد من حذف هذه الشركة؟')" data-bs-toggle="tooltip" title="حذف الشركة">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- 📱 عرض الموبايل: بطاقات لمسية أنيقة --}}
                <div class="d-md-none p-3">
                    @foreach($companies as $company)
                    @php
                        $isCompanyApproved = $company->is_approved && $company->partnership_status === 'active';
                    @endphp
                    <div class="company-mobile-card">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 40px; height: 40px; font-size: 1.1rem;">
                                    {{ mb_substr($company->name, 0, 1) }}
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0 fs-6">{{ $company->name }}</h6>
                                    <span class="badge bg-light text-info fw-normal" style="font-size: 0.72rem;">
                                        {{ $company->industry ?? 'غير محدد' }}
                                    </span>
                                </div>
                            </div>
                            <span class="badge rounded-pill {{ $isCompanyApproved ? 'bg-success text-white' : 'bg-warning text-dark' }}" style="font-size: 0.7rem;">
                                {{ $isCompanyApproved ? 'معتمدة' : 'قيد المراجعة' }}
                            </span>
                        </div>

                        <div class="d-flex flex-column gap-1 my-2 py-2 border-top border-bottom border-light small text-muted">
                            <div>
                                <i class="fas fa-envelope me-1 text-primary"></i>
                                <a href="mailto:{{ $company->email }}" class="text-decoration-none text-muted">{{ $company->email }}</a>
                            </div>
                            @if($company->address)
                            <div>
                                <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                                <span>{{ $company->address }}</span>
                            </div>
                            @endif
                            <div>
                                <i class="fas fa-calendar-alt me-1 text-secondary"></i>
                                <span>أضيفت في: {{ $company->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'partnership_officer')
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                            <form action="{{ route('admin.companies.toggle-approval', $company->id) }}" method="POST" class="d-inline">
                                @csrf
                                @if($isCompanyApproved)
                                    <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-3" style="font-size: 0.75rem;">
                                        <i class="fas fa-pause me-1"></i>تعليق الاعتماد
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3" style="font-size: 0.75rem;">
                                        <i class="fas fa-check-circle me-1"></i>اعتماد الشركة
                                    </button>
                                @endif
                            </form>

                            <div class="d-flex gap-1">
                                @if($company->user_id)
                                    <a href="{{ route('messages.show', $company->user_id) }}" class="btn btn-sm btn-outline-success rounded-pill px-2.5" style="font-size: 0.75rem;" title="مراسلة مسؤولي الشركة">
                                        <i class="fas fa-comment-dots"></i>
                                    </a>
                                @endif
                                <a href="{{ route('admin.companies.show', $company->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3" style="font-size: 0.75rem;">
                                    <i class="fas fa-eye me-1"></i>عرض
                                </a>
                                <a href="{{ route('admin.companies.edit', $company->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size: 0.75rem;">
                                    <i class="fas fa-edit me-1"></i>تعديل
                                </a>
                                <form action="{{ route('admin.companies.destroy', $company->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" onclick="return confirm('هل أنت متأكد من حذف هذه الشركة؟')" style="font-size: 0.75rem;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-building fa-3x text-muted opacity-50"></i>
                        </div>
                    </div>
                    <h5 class="text-muted mb-3">لا توجد شركات مسجلة</h5>
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'partnership_officer')
                        <a href="{{ route('admin.companies.create') }}" class="btn btn-primary-modern">
                            <i class="fas fa-plus me-2"></i>إضافة أول شركة
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });

    function copyCompanyRegisterLink() {
        const link = "{{ route('company.register') }}";
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(link).then(() => {
                showCopiedFeedback();
            }).catch(err => {
                fallbackCopyText(link);
            });
        } else {
            fallbackCopyText(link);
        }
    }

    function fallbackCopyText(text) {
        const input = document.createElement('input');
        input.value = text;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        showCopiedFeedback();
    }

    function showCopiedFeedback() {
        const btnText = document.getElementById('copyLinkText');
        if (btnText) {
            const original = btnText.innerText;
            btnText.innerText = 'تم نسخ رابط التسجيل! ✔️';
            btnText.parentElement.classList.add('bg-success', 'border-success');
            setTimeout(() => {
                btnText.innerText = original;
                btnText.parentElement.classList.remove('bg-success', 'border-success');
            }, 3000);
        }
        alert('تم نسخ رابط تسجيل الشركات بنجاح:\n' + "{{ route('company.register') }}\n\nيمكنك إرساله الآن لمسؤولي الشركات للتسجيل الذاتي.");
    }
</script>
@endsection