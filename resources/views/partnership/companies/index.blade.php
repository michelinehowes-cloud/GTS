@extends('layouts.app')

@section('title', 'إدارة الشركات الشريكة - مسؤول الشراكات')

@section('content')
<div class="container-fluid">
    <!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
    <x-page-hero
        title="دليل الشركات الشريكة"
        subtitle="إدارة ومتابعة المؤسسات والشركات الشريكة وفرص التدريب والتوظيف المطروحة"
        icon="fas fa-building"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة الشراكات والتوظيف', 'url' => route('partnership.dashboard')],
            ['label' => 'دليل الشركات الشريكة']
        ]"
        secondaryBadge="شراكات نشطة: {{ $companies->where('partnership_status', 'active')->count() }}"
        secondaryBadgeIcon="fas fa-check-circle"
    >
        <button type="button" class="btn btn-outline-light text-white fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" onclick="copyCompanyRegisterLink()" style="font-size: 0.88rem; backdrop-filter: blur(4px); transition: all 0.2s ease;">
            <i class="fas fa-link fs-6"></i>
            <span id="copyLinkText">نسخ رابط تسجيل الشركات</span>
        </button>
        <a href="{{ route('partnership.reports') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-chart-bar fs-6"></i>
            <span>التقارير</span>
        </a>
        <a href="{{ route('partnership.companies.create') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-plus-circle fs-6"></i>
            <span>إضافة شركة جديدة</span>
        </a>
    </x-page-hero>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- شريط التبويبات السريعة (الكل، بانتظار الاعتماد، النشطة) -->
    <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
        <a href="{{ route('partnership.companies') }}" 
           class="btn btn-sm rounded-pill px-3 py-2 fw-bold {{ !request('approval_status') && !request('partnership_status') ? 'btn-primary' : 'btn-light bg-white border text-dark' }}">
            <i class="fas fa-building me-1"></i> جميع الشركات ({{ $companies->count() }})
        </a>
        <a href="{{ route('partnership.companies', ['approval_status' => 'pending']) }}" 
           class="btn btn-sm rounded-pill px-3 py-2 fw-bold position-relative {{ request('approval_status') == 'pending' ? 'btn-warning text-dark' : 'btn-light bg-white border text-dark' }}">
            <i class="fas fa-clock me-1 text-warning"></i> طلبات بانتظار الاعتماد
            @if(($pendingCount ?? 0) > 0)
                <span class="badge bg-danger rounded-pill ms-1">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('partnership.companies', ['partnership_status' => 'active']) }}" 
           class="btn btn-sm rounded-pill px-3 py-2 fw-bold {{ request('partnership_status') == 'active' ? 'btn-success text-white' : 'btn-light bg-white border text-dark' }}">
            <i class="fas fa-check-circle me-1 text-success"></i> شراكات نشطة
        </a>
    </div>

    <!-- فلترة وبحث الشركات -->
    <div class="card-modern mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 text-primary fw-bold fs-6">
                <i class="fas fa-filter me-2"></i>تصفية وبحث الشركات
            </h5>
        </div>
        <div class="card-body p-3 p-md-4">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="partnership_status" class="form-label small fw-bold">حالة الشراكة</label>
                    <select name="partnership_status" id="partnership_status" class="form-select">
                        <option value="">جميع الحالات</option>
                        <option value="active" {{ request('partnership_status') == 'active' ? 'selected' : '' }}>نشطة</option>
                        <option value="expired" {{ request('partnership_status') == 'expired' ? 'selected' : '' }}>منتهية</option>
                        <option value="under_review" {{ request('partnership_status') == 'under_review' ? 'selected' : '' }}>قيد المراجعة</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="partnership_type" class="form-label small fw-bold">نوع الشراكة</label>
                    <select name="partnership_type" id="partnership_type" class="form-select">
                        <option value="">جميع الأنواع</option>
                        <option value="employment" {{ request('partnership_type') == 'employment' ? 'selected' : '' }}>توظيف</option>
                        <option value="training" {{ request('partnership_type') == 'training' ? 'selected' : '' }}>تدريب</option>
                        <option value="logistic_support" {{ request('partnership_type') == 'logistic_support' ? 'selected' : '' }}>دعم لوجستي</option>
                        <option value="academic" {{ request('partnership_type') == 'academic' ? 'selected' : '' }}>شراكة أكاديمية</option>
                        <option value="workshops" {{ request('partnership_type') == 'workshops' ? 'selected' : '' }}>ورش عمل</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label small fw-bold">بحث</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="ابحث باسم الشركة أو المجال أو المسؤول..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i>بحث
                    </button>
                    <a href="{{ route('partnership.companies') }}" class="btn btn-outline-secondary" title="إعادة تعيين">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- جدول الشركات -->
    <div class="card-modern mb-4">
        <div class="card-body p-0">
            @if($companies->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center py-3 text-secondary small fw-bold" style="width: 50px;">#</th>
                                <th class="py-3 px-3 text-secondary small fw-bold" style="min-width: 220px;">الشركة</th>
                                <th class="py-3 px-3 text-secondary small fw-bold" style="min-width: 200px;">مسؤول الاتصال</th>
                                <th class="text-center py-3 text-secondary small fw-bold" style="min-width: 160px;">أنواع الشراكة</th>
                                <th class="text-center py-3 text-secondary small fw-bold" style="width: 140px;">حالة الاعتماد</th>
                                <th class="text-center py-3 text-secondary small fw-bold" style="width: 100px;">الفرص</th>
                                <th class="text-center py-3 text-secondary small fw-bold" style="width: 100px;">الوثائق</th>
                                <th class="text-center py-3 text-secondary small fw-bold" style="width: 180px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($companies as $company)
                            <tr class="{{ !$company->is_approved ? 'table-warning bg-opacity-25' : '' }}">
                                <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                                <td class="px-3">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle fw-bold d-flex align-items-center justify-content-center me-3 flex-shrink-0 shadow-sm" style="width: 42px; height: 42px; font-size: 1.15rem; background-color: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd;">
                                            @if($company->logo_path)
                                                <img src="{{ asset('storage/' . $company->logo_path) }}" alt="{{ $company->name }}" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;">
                                            @else
                                                {{ mb_substr($company->name, 0, 1) }}
                                            @endif
                                        </div>
                                        <div>
                                            <a href="{{ route('partnership.companies.show', $company->id) }}" class="fw-bold text-dark text-decoration-none d-block hover-primary" style="font-size: 0.95rem;">
                                                {{ $company->name }}
                                            </a>
                                            <div class="small text-muted">
                                                <i class="fas fa-tag me-1"></i>{{ $company->industry ?? 'غير محدد' }}
                                                @if($company->website)
                                                    &bull; <a href="{{ $company->website }}" target="_blank" class="text-decoration-none text-primary">
                                                        <i class="fas fa-globe me-1"></i>الموقع
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3">
                                    @if($company->contact_person)
                                        <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $company->contact_person }}</div>
                                        <div class="small text-muted">
                                            @if($company->contact_phone)
                                                <span class="me-2"><i class="fas fa-phone me-1 text-success"></i>{{ $company->contact_phone }}</span>
                                            @endif
                                            @if($company->contact_email)
                                                <span><i class="fas fa-envelope me-1 text-primary"></i>{{ $company->contact_email }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small">غير محدد</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-wrap justify-content-center gap-1">
                                        @forelse($company->partnership_types_list as $pType)
                                            @switch($pType)
                                                @case('employment')
                                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">توظيف</span>
                                                    @break
                                                @case('training')
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">تدريب</span>
                                                    @break
                                                @case('logistic_support')
                                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">دعم لوجستي</span>
                                                    @break
                                                @case('academic')
                                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">أكاديمية</span>
                                                    @break
                                                @case('workshops')
                                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">ورش عمل</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">{{ $pType }}</span>
                                            @endswitch
                                        @empty
                                            <span class="badge bg-light text-secondary border rounded-pill px-2 py-1 small">غير محدد</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="text-center" style="white-space: nowrap;">
                                    @if($company->is_approved)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1.5 fw-bold">
                                            <i class="fas fa-check-circle me-1"></i>معتمدة ونشطة
                                        </span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1.5 fw-bold animate__animated animate__pulse animate__infinite">
                                            <i class="fas fa-clock me-1"></i>بانتظار المراجعة
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center" style="white-space: nowrap;">
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-2.5 py-1">
                                        <i class="fas fa-briefcase me-1"></i>{{ $company->job_opportunities_count ?? $company->jobOpportunities->count() }}
                                    </span>
                                </td>
                                <td class="text-center" style="white-space: nowrap;">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border rounded-pill px-2.5 py-1">
                                        <i class="fas fa-file-contract me-1"></i>{{ $company->partnership_documents_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-center" style="white-space: nowrap;">
                                    <div class="d-flex align-items-center justify-content-center gap-1.5">
                                        @if(!$company->is_approved)
                                            {{-- زر الاعتماد المباشر --}}
                                            <form action="{{ route('partnership.companies.approve', $company->id) }}" method="POST" class="d-inline m-0">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-2.5 py-1 shadow-sm d-inline-flex align-items-center gap-1" title="اعتماد وتفعيل حساب الشركة">
                                                    <i class="fas fa-check"></i>
                                                    <span class="small fw-bold">اعتماد</span>
                                                </button>
                                            </form>

                                            {{-- زر الرفض مع الملاحظات --}}
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#rejectCompanyModal{{ $company->id }}" title="رفض الطلب">
                                                <i class="fas fa-times me-1"></i>
                                                <span class="small fw-bold">رفض</span>
                                            </button>
                                        @else
                                            {{-- زر إلغاء الاعتماد --}}
                                            <form action="{{ route('partnership.companies.toggle-approval', $company->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('هل أنت متأكد من إلغاء اعتماد هذه الشركة؟');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-secondary rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;" title="إلغاء الاعتماد">
                                                    <i class="fas fa-ban text-warning"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- زر العرض --}}
                                        <a href="{{ route('partnership.companies.show', $company->id) }}" class="btn btn-sm btn-outline-info rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- زر التعديل --}}
                                        <a href="{{ route('partnership.companies.edit', $company->id) }}" class="btn btn-sm btn-outline-warning rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;" title="تعديل البيانات">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- زر فرص العمل --}}
                                        <a href="{{ route('job-opportunities.index', ['company_id' => $company->id]) }}" class="btn btn-sm btn-outline-primary rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;" title="فرص العمل والتدريب">
                                            <i class="fas fa-briefcase"></i>
                                        </a>
                                    </div>

                                    @if(!$company->is_approved)
                                    <!-- نافذة رفض الشركة -->
                                    <div class="modal fade" id="rejectCompanyModal{{ $company->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content text-start">
                                                <form action="{{ route('partnership.companies.reject', $company->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header bg-danger text-white">
                                                        <h6 class="modal-title fw-bold">
                                                            <i class="fas fa-times-circle me-1"></i> رفض طلب تسجيل شركة: {{ $company->name }}
                                                        </h6>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-end">
                                                        <p class="text-muted small mb-3">سيتم إشعار مسؤول الاتصال بالشركة بسبب عدم قبول الطلب وتظل الشركة غير مفعلة.</p>
                                                        <label class="form-label fw-bold small text-dark">سبب الرفض أو الملاحظات:</label>
                                                        <textarea name="rejection_notes" class="form-control" rows="3" placeholder="اكتب سبب عدم قبول الطلب، مثل: عدم وضوح بيانات السجل التجاري، بيانات الاتصال غير كافية..." required></textarea>
                                                    </div>
                                                    <div class="modal-footer justify-content-between">
                                                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">إلغاء</button>
                                                        <button type="submit" class="btn btn-danger fw-bold">تأكيد الرفض وإشعار الشركة</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-building text-muted display-4 mb-3 opacity-50"></i>
                    <h5 class="text-dark fw-bold">لا توجد شركات شريكة مطابقة للبحث</h5>
                    <p class="text-muted">يمكنك دعوة الشركات للتسجيل عبر إرسال الرابط المخصص لهم أو إضافة شركة يدوياً</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-primary px-3 rounded-pill" onclick="copyCompanyRegisterLink()">
                            <i class="fas fa-copy me-1"></i> نسخ رابط تسجيل الشركات
                        </button>
                        <a href="{{ route('partnership.companies.create') }}" class="btn btn-primary px-4 rounded-pill">
                            <i class="fas fa-plus-circle me-1"></i> إضافة شركة جديدة
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyCompanyRegisterLink() {
        const url = "{{ route('company.register') }}";
        if (navigator.clipboard) {
            navigator.clipboard.writeText(url).then(() => {
                showCopiedFeedback();
            }).catch(() => {
                fallbackCopyText(url);
            });
        } else {
            fallbackCopyText(url);
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
@endpush
@endsection
