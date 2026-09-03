@extends('layouts.app')

@section('title', 'لوحة تحكم مسؤول الشراكات والتوظيف')

@section('content')
<div class="container-fluid">
    <!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
    <x-page-hero
        title="لوحة تحكم مسؤول الشراكات والتوظيف"
        subtitle="إدارة الشراكات الاستراتيجية ومتابعة المؤسسات الشريكة وفرص التوظيف والتدريب للخريجين"
        icon="fas fa-handshake"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة الشراكات والتوظيف']
        ]"
        secondaryBadge="مسؤول الشراكات والتوظيف"
        secondaryBadgeIcon="fas fa-handshake"
    >
        <a href="{{ route('job-opportunities.create') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-briefcase fs-6"></i>
            <span>فرصة جديدة</span>
        </a>
        <a href="{{ route('partnership.companies.create') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-building fs-6"></i>
            <span>إضافة شركة</span>
        </a>
    </x-page-hero>

    <!-- Quick Stats Cards (4 Columns) -->
    <div class="row g-3 mb-4">
        <!-- إجمالي الشركات -->
        <div class="col-xl-3 col-sm-6">
            <div class="card-modern p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small mb-1">الشركات الشريكة</div>
                        <div class="fs-4 fw-bold text-primary mb-0">{{ $stats['totalCompanies'] ?? 0 }}</div>
                        <div class="text-muted small" style="font-size: 0.75rem;">
                            <span class="text-success fw-bold">{{ $stats['activePartnerships'] ?? 0 }}</span> شراكة نشطة
                        </div>
                    </div>
                    <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-building fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- فرص العمل -->
        <div class="col-xl-3 col-sm-6">
            <div class="card-modern p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small mb-1">فرص العمل والتدريب</div>
                        <div class="fs-4 fw-bold text-success mb-0">{{ $stats['totalOpportunities'] ?? 0 }}</div>
                        <div class="text-muted small" style="font-size: 0.75rem;">
                            <span class="text-success fw-bold">{{ $stats['openOpportunities'] ?? 0 }}</span> فرصة مفتوحة
                        </div>
                    </div>
                    <div class="rounded-circle bg-light text-success d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-briefcase fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- الخريجون المسجلون -->
        <div class="col-xl-3 col-sm-6">
            <div class="card-modern p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small mb-1">الخريجون المسجلون</div>
                        <div class="fs-4 fw-bold text-info mb-0">{{ $stats['totalGraduates'] ?? 0 }}</div>
                        <div class="text-muted small" style="font-size: 0.75rem;">
                            <span class="text-info fw-bold">{{ $stats['activeNominations'] ?? 0 }}</span> ترشيح نشط
                        </div>
                    </div>
                    <div class="rounded-circle bg-light text-info d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-user-graduate fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- وثائق الشراكة -->
        <div class="col-xl-3 col-sm-6">
            <div class="card-modern p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small mb-1">وثائق الشراكة</div>
                        <div class="fs-4 fw-bold text-warning mb-0">{{ $stats['totalDocuments'] ?? 0 }}</div>
                        <div class="text-muted small" style="font-size: 0.75rem;">
                            <span class="{{ ($stats['expiringDocuments'] ?? 0) > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                                {{ $stats['expiringDocuments'] ?? 0 }} تنتهي قريباً
                            </span>
                        </div>
                    </div>
                    <div class="rounded-circle bg-light text-warning d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-file-contract fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- المحتوى الرئيسي: الفرص الحديثة ووثائق الشراكة -->
    <div class="row g-4 mb-4">
        <!-- أحدث الفرص -->
        <div class="col-lg-6">
            <div class="card-modern h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-primary fw-bold fs-6">
                        <i class="fas fa-clock me-2"></i>أحدث الفرص المطروحة
                    </h5>
                    <a href="{{ route('job-opportunities.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        عرض الكل <i class="fas fa-arrow-left ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($recentOpportunities->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentOpportunities as $opp)
                            <div class="list-group-item p-3 d-flex justify-content-between align-items-center hover-bg-light">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 40px; height: 40px;">
                                        <i class="fas {{ $opp->type == 'job' ? 'fa-briefcase' : 'fa-chalkboard-teacher' }}"></i>
                                    </div>
                                    <div>
                                        <a href="{{ route('job-opportunities.show', $opp->id) }}" class="fw-bold text-dark text-decoration-none d-block">
                                            {{ $opp->title }}
                                        </a>
                                        <div class="small text-muted">
                                            <i class="fas fa-building me-1"></i>{{ $opp->company->name ?? 'غير محدد' }} &bull;
                                            <i class="fas fa-users me-1"></i>{{ $opp->seats }} مقاعد
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-{{ $opp->status == 'open' ? 'success' : 'secondary' }} bg-opacity-10 text-{{ $opp->status == 'open' ? 'success' : 'secondary' }} border border-{{ $opp->status == 'open' ? 'success' : 'secondary' }} border-opacity-25 rounded-pill px-2.5 py-1">
                                        {{ $opp->status == 'open' ? 'مفتوحة' : 'مغلقة' }}
                                    </span>
                                    <a href="{{ route('job-opportunities.show', $opp->id) }}" class="btn btn-sm btn-outline-info rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-briefcase fa-2x mb-2 opacity-50"></i>
                            <p class="mb-0 small">لا توجد فرص مطروحة حالياً</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- أحدث وثائق الشراكة -->
        <div class="col-lg-6">
            <div class="card-modern h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-primary fw-bold fs-6">
                        <i class="fas fa-file-signature me-2"></i>أحدث وثائق واتفاقيات الشراكة
                    </h5>
                    <a href="{{ route('partnership.documents') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        عرض الكل <i class="fas fa-arrow-left ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($recentDocuments->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentDocuments as $doc)
                            <div class="list-group-item p-3 d-flex justify-content-between align-items-center hover-bg-light">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light text-warning d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 40px; height: 40px;">
                                        <i class="fas fa-file-contract"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $doc->title }}</div>
                                        <div class="small text-muted">
                                            <i class="fas fa-building me-1"></i>{{ $doc->company->name ?? 'غير محدد' }} &bull;
                                            <i class="far fa-calendar-alt me-1"></i>{{ $doc->expiry_date ? $doc->expiry_date->format('Y-m-d') : 'سارية' }}
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('partnership.documents.show', $doc->id) }}" class="btn btn-sm btn-outline-info rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-file-contract fa-2x mb-2 opacity-50"></i>
                            <p class="mb-0 small">لا توجد وثائق شراكة مسجلة حديثاً</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- روابط سريعة للوصول -->
    <div class="card-modern mb-4 p-3 p-md-4">
        <h6 class="fw-bold text-dark mb-3"><i class="fas fa-bolt text-warning me-2"></i>إجراءات سريعة</h6>
        <div class="row g-3">
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('partnership.companies') }}" class="card-modern p-3 d-flex align-items-center text-decoration-none text-dark hover-shadow">
                    <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 40px; height: 40px;">
                        <i class="fas fa-building"></i>
                    </div>
                    <div>
                        <div class="fw-bold small">دليل الشركات</div>
                        <div class="text-muted" style="font-size: 0.75rem;">استعراض ومتابعة الشركاء</div>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('job-opportunities.index') }}" class="card-modern p-3 d-flex align-items-center text-decoration-none text-dark hover-shadow">
                    <div class="rounded-circle bg-light text-success d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 40px; height: 40px;">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div>
                        <div class="fw-bold small">فرص العمل والتدريب</div>
                        <div class="text-muted" style="font-size: 0.75rem;">إدارة الإعلانات والفرص</div>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('partnership.documents') }}" class="card-modern p-3 d-flex align-items-center text-decoration-none text-dark hover-shadow">
                    <div class="rounded-circle bg-light text-warning d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 40px; height: 40px;">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div>
                        <div class="fw-bold small">اتفاقيات الشراكة</div>
                        <div class="text-muted" style="font-size: 0.75rem;">أرشفة ومتابعة الوثائق</div>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('partnership.reports') }}" class="card-modern p-3 d-flex align-items-center text-decoration-none text-dark hover-shadow">
                    <div class="rounded-circle bg-light text-info d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 40px; height: 40px;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <div class="fw-bold small">التقارير والإحصائيات</div>
                        <div class="text-muted" style="font-size: 0.75rem;">مؤشرات الشراكات والتوظيف</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection