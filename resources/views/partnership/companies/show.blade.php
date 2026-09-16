@extends('layouts.app')

@section('title', 'ملف الشركة: ' . $company->name . ' - مسؤول الشراكات')

@section('page-title', 'ملف الشركة')

@section('content')
<div class="container-fluid">
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('partnership.dashboard')],
            ['label' => 'إدارة الشركات', 'url' => route('partnership.companies')],
            ['label' => $company->name, 'active' => true],
        ]
    ])

    @php
        $isApproved = $company->is_approved && $company->partnership_status === 'active';
        $typesLabels = [
            'employment'      => ['label' => 'توظيف الخريجين',       'icon' => 'fa-user-tie',          'color' => '#0284c7', 'bg' => '#e0f2fe'],
            'training'        => ['label' => 'تدريب ميداني وتأهيل',  'icon' => 'fa-graduation-cap',     'color' => '#059669', 'bg' => '#d1fae5'],
            'logistic_support'=> ['label' => 'دعم لوجستي ورعاية',   'icon' => 'fa-award',             'color' => '#d97706', 'bg' => '#fef3c7'],
            'academic'        => ['label' => 'تعاون أكاديمي وبحثي', 'icon' => 'fa-flask',             'color' => '#7c3aed', 'bg' => '#ede9fe'],
            'workshops'       => ['label' => 'ورش عمل وندوات',       'icon' => 'fa-chalkboard-teacher', 'color' => '#db2777', 'bg' => '#fce7f3'],
        ];
        $companyTypes = $company->partnership_types ?? ($company->partnership_type ? [$company->partnership_type] : []);
        $totalJobs   = $company->jobOpportunities()->count();
        $openJobs    = $company->jobOpportunities()->where('status', 'open')->count();
    @endphp

    {{-- =================== رأس الصفحة - بطاقة الهوية =================== --}}
    {{-- =================== رأس الصفحة - بطاقة الهوية =================== --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="p-4" style="background:linear-gradient(135deg,#0284c7 0%,#1e3a8a 100%);">
            <div class="d-flex align-items-center gap-4 flex-wrap">
                {{-- شعار الشركة --}}
                <div class="flex-shrink-0">
                    @if($company->logo_path)
                        <img src="{{ Storage::url($company->logo_path) }}" alt="{{ $company->name }}"
                             style="width:84px;height:84px;border-radius:16px;object-fit:contain;background:#fff;padding:6px;border:3px solid rgba(255,255,255,.5);box-shadow:0 4px 12px rgba(0,0,0,0.08);">
                    @else
                        <div style="width:84px;height:84px;border-radius:16px;background:rgba(255,255,255,.2);border:3px solid rgba(255,255,255,.5);display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,0.08);">
                            <span style="font-size:2.2rem;font-weight:800;color:#fff;">{{ mb_substr($company->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <div class="text-white">
                    <h2 class="fw-bold mb-1 fs-3">{{ $company->name }}</h2>
                    <div class="d-flex align-items-center gap-3 flex-wrap" style="opacity:.95;font-size:.9rem;">
                        @if($company->industry)
                            <span><i class="fas fa-industry me-1"></i>{{ $company->industry }}</span>
                        @endif
                        @if($company->address)
                            <span><i class="fas fa-map-marker-alt me-1"></i>{{ $company->city ? $company->city . ' - ' : '' }}{{ $company->address }}</span>
                        @endif
                    </div>
                </div>
                <div class="ms-auto d-flex gap-2 flex-wrap">
                    <a href="{{ route('partnership.companies.edit', $company->id) }}" class="btn btn-sm btn-light text-primary fw-bold rounded-pill shadow-sm px-3">
                        <i class="fas fa-edit me-1"></i>تعديل البيانات
                    </a>
                    <a href="{{ route('job-opportunities.create', ['company_id' => $company->id]) }}" class="btn btn-sm btn-light text-success fw-bold rounded-pill shadow-sm px-3">
                        <i class="fas fa-plus me-1"></i>إضافة فرصة
                    </a>
                    @if(!$company->is_approved)
                        <form action="{{ route('partnership.companies.approve', $company->id) }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success rounded-pill fw-bold shadow-sm px-3">
                                <i class="fas fa-check me-1"></i>اعتماد الحساب
                            </button>
                        </form>
                    @else
                        <form action="{{ route('partnership.companies.toggle-approval', $company->id) }}" method="POST" class="m-0" onsubmit="return confirm('هل أنت متأكد من إلغاء اعتماد هذه الشركة؟');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning rounded-pill fw-bold shadow-sm px-3">
                                <i class="fas fa-pause me-1"></i>تعليق الاعتماد
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        {{-- شريط حالة الشراكة والأنواع --}}
        <div class="bg-white px-4 py-3 border-top">
            <div class="d-flex gap-2 align-items-center flex-wrap">
                @if($isApproved)
                    <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background:#dcfce7;color:#16a34a;font-size:.85rem;">
                        <i class="fas fa-check-circle me-1"></i>معتمدة ونشطة
                    </span>
                @elseif($company->partnership_status === 'expired')
                    <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background:#f3f4f6;color:#6b7280;font-size:.85rem;">
                        <i class="fas fa-history me-1"></i>منتهية الشراكة
                    </span>
                @else
                    <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background:#fef9c3;color:#ca8a04;font-size:.85rem;">
                        <i class="fas fa-clock me-1"></i>قيد المراجعة
                    </span>
                @endif

                @foreach((array)$companyTypes as $type)
                    @if(isset($typesLabels[$type]))
                    <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background:{{ $typesLabels[$type]['bg'] }};color:{{ $typesLabels[$type]['color'] }};font-size:.82rem;">
                        <i class="fas {{ $typesLabels[$type]['icon'] }} me-1"></i>{{ $typesLabels[$type]['label'] }}
                    </span>
                    @endif
                @endforeach

                <div class="ms-auto d-flex align-items-center gap-4 text-muted small">
                    <span><i class="fas fa-briefcase text-primary me-1"></i><strong>{{ $totalJobs }}</strong> فرصة عمل</span>
                    <span><i class="fas fa-file-contract text-success me-1"></i><strong>{{ $company->partnershipDocuments ? $company->partnershipDocuments->count() : 0 }}</strong> وثيقة شراكة</span>
                </div>
            </div>
        </div>
    </div>

    {{-- =================== تفاصيل الشركة في شبكة Bento =================== --}}
    <div class="row g-4 mb-4">
        {{-- بيانات الاتصال والموقع --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <span class="badge rounded-circle p-2" style="background:#e0f2fe;color:#0284c7;"><i class="fas fa-address-book"></i></span>
                    معلومات الاتصال بالشركة
                </h6>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-center gap-3 p-2 rounded-3 bg-light">
                        <i class="fas fa-envelope text-primary fs-5 flex-shrink-0" style="width:24px;"></i>
                        <div>
                            <div class="text-muted small">البريد الإلكتروني الرسمي</div>
                            <a href="mailto:{{ $company->email }}" class="fw-bold text-dark text-decoration-none small">{{ $company->email }}</a>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 p-2 rounded-3 bg-light">
                        <i class="fas fa-phone text-success fs-5 flex-shrink-0" style="width:24px;"></i>
                        <div>
                            <div class="text-muted small">رقم الهاتف</div>
                            <a href="tel:{{ $company->phone }}" class="fw-bold text-dark text-decoration-none small">{{ $company->phone }}</a>
                        </div>
                    </div>
                    @if($company->website)
                    <div class="d-flex align-items-center gap-3 p-2 rounded-3 bg-light">
                        <i class="fas fa-globe text-info fs-5 flex-shrink-0" style="width:24px;"></i>
                        <div>
                            <div class="text-muted small">الموقع الإلكتروني</div>
                            <a href="{{ $company->website }}" target="_blank" class="fw-bold text-primary text-decoration-none small">{{ $company->website }}</a>
                        </div>
                    </div>
                    @endif
                    <div class="d-flex align-items-center gap-3 p-2 rounded-3 bg-light">
                        <i class="fas fa-map-pin text-danger fs-5 flex-shrink-0" style="width:24px;"></i>
                        <div>
                            <div class="text-muted small">العنوان</div>
                            <div class="fw-bold text-dark small">{{ $company->city ? $company->city . '، ' : '' }}{{ $company->address ?? 'غير محدد' }}</div>
                        </div>
                    </div>
                </div>

                {{-- مسؤول الاتصال --}}
                @if($company->contact_person)
                <h6 class="fw-bold text-dark mt-4 mb-3 d-flex align-items-center gap-2">
                    <span class="badge rounded-circle p-2" style="background:#d1fae5;color:#059669;"><i class="fas fa-user-tie"></i></span>
                    ممثل / مسؤول الاتصال
                </h6>
                <div class="p-3 rounded-3" style="background:#f8fafc;border:1px dashed #cbd5e1;">
                    <div class="fw-bold text-dark mb-1">{{ $company->contact_person }}</div>
                    @if($company->contact_position)
                        <div class="text-muted small mb-2"><i class="fas fa-briefcase me-1"></i>{{ $company->contact_position }}</div>
                    @endif
                    @if($company->contact_phone)
                        <div class="small mb-1"><i class="fas fa-phone text-success me-1"></i><a href="tel:{{ $company->contact_phone }}" class="text-decoration-none text-dark">{{ $company->contact_phone }}</a></div>
                    @endif
                    @if($company->contact_email)
                        <div class="small"><i class="fas fa-envelope text-primary me-1"></i><a href="mailto:{{ $company->contact_email }}" class="text-decoration-none text-dark">{{ $company->contact_email }}</a></div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- تفاصيل الشراكة والنبذة --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <span class="badge rounded-circle p-2" style="background:#fef3c7;color:#d97706;"><i class="fas fa-info-circle"></i></span>
                    نبذة عن الشركة
                </h6>
                <p class="text-secondary leading-relaxed mb-0" style="line-height:1.8;">
                    {{ $company->description ?: 'لا توجد نبذة تعريفية مسجلة لهذه الشركة بعد.' }}
                </p>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <span class="badge rounded-circle p-2" style="background:#ede9fe;color:#7c3aed;"><i class="fas fa-handshake"></i></span>
                    بيانات ومدى الشراكة
                </h6>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded-3">
                            <div class="text-muted small mb-1">تاريخ بداية الشراكة</div>
                            <div class="fw-bold text-dark">
                                {{ $company->partnership_start_date ? \Carbon\Carbon::parse($company->partnership_start_date)->format('Y/m/d') : 'غير محدد' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded-3">
                            <div class="text-muted small mb-1">تاريخ نهاية الشراكة</div>
                            <div class="fw-bold text-dark">
                                {{ $company->partnership_end_date ? \Carbon\Carbon::parse($company->partnership_end_date)->format('Y/m/d') : 'مفتوحة / غير محدد' }}
                            </div>
                        </div>
                    </div>
                    @if($company->partnership_notes)
                    <div class="col-12">
                        <div class="p-3 bg-light rounded-3">
                            <div class="text-muted small mb-1">ملاحظات الشراكة</div>
                            <div class="text-dark small">{{ $company->partnership_notes }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- فرص العمل التي طرحتها الشركة --}}
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <span class="badge rounded-circle p-2" style="background:#dcfce7;color:#16a34a;"><i class="fas fa-briefcase"></i></span>
                        فرص العمل والتدريب المطروحة ({{ $company->jobOpportunities->count() }})
                    </h6>
                    <a href="{{ route('job-opportunities.create', ['company_id' => $company->id]) }}" class="btn btn-sm btn-primary rounded-pill px-3">
                        <i class="fas fa-plus me-1"></i>طرح فرصة
                    </a>
                </div>

                @if($company->jobOpportunities && $company->jobOpportunities->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="bg-light">
                                <tr>
                                    <th>المسمى الوظيفي</th>
                                    <th>النوع</th>
                                    <th>الموقع</th>
                                    <th>الحالة</th>
                                    <th>تاريخ النشر</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($company->jobOpportunities as $job)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $job->title }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $job->type ?? 'وظيفة' }}</span>
                                    </td>
                                    <td>{{ $job->location ?? 'طرابلس' }}</td>
                                    <td>
                                        @if($job->status === 'open')
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill">مفتوحة</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">مغلقة</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">{{ $job->created_at ? $job->created_at->format('Y/m/d') : '-' }}</td>
                                    <td>
                                        <a href="{{ route('job-opportunities.show', $job->id) }}" class="btn btn-sm btn-outline-primary rounded-circle" style="width:28px;height:28px;padding:0;line-height:26px;" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-briefcase fs-3 mb-2 d-block opacity-50"></i>
                        لم تقم هذه الشركة بطرح أي فرص وظيفية حتى الآن.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
