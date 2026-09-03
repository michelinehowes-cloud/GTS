@extends('layouts.app')

@section('title', 'إدارة وثائق الشراكة')

@section('content')
<div class="container-fluid">

    <!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
    <x-page-hero
        title="وثائق واتفاقيات الشراكة"
        subtitle="إدارة وتوثيق العقود، مذكرات التفاهم (MOU)، والاتفاقيات الرسمية المبرمة مع المؤسسات والشركات الشريكة"
        icon="fas fa-file-contract"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة الشراكات والتوظيف', 'url' => route('partnership.dashboard')],
            ['label' => 'وثائق الشراكة']
        ]"
        badge="إدارة العقود والتوثيق"
    >
        <a href="{{ route('partnership.documents.create') }}" class="btn btn-warning text-dark fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
            <i class="fas fa-plus-circle"></i>
            <span>إضافة وثيقة جديدة</span>
        </a>
        <a href="{{ route('partnership.companies') }}" class="btn btn-light bg-white text-primary fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
            <i class="fas fa-building"></i>
            <span>دليل الشركات</span>
        </a>
    </x-page-hero>

    <!-- بطاقات الإحصائيات -->
    <div class="row g-2 g-md-3 mb-4">
        @include('components.stat-card', [
            'col' => 'col-6 col-md-3',
            'title' => 'إجمالي الوثائق',
            'value' => $stats['total'] ?? (method_exists($documents, 'total') ? $documents->total() : $documents->count()),
            'icon' => 'fas fa-folder-open',
            'color' => 'primary'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-3',
            'title' => 'وثائق سارية ونشطة',
            'value' => $stats['active'] ?? $documents->where('document_status', 'active')->count(),
            'icon' => 'fas fa-check-circle',
            'color' => 'success'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-3',
            'title' => 'منتهية الصلاحية',
            'value' => $stats['expired'] ?? $documents->where('document_status', 'expired')->count(),
            'icon' => 'fas fa-exclamation-triangle',
            'color' => 'danger'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-3',
            'title' => 'الشركات الموقعة',
            'value' => $stats['companies_count'] ?? $documents->pluck('company_id')->unique()->count(),
            'icon' => 'fas fa-handshake',
            'color' => 'info'
        ])
    </div>

    <!-- التنبيهات -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center" role="alert">
            <i class="fas fa-check-circle fs-5 me-2"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- جدول البيانات الحديث -->
    <div class="card-modern shadow-sm border-0 rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="fas fa-list"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-dark fs-6">سجل الاتفاقيات والوثائق المعتمدة</h6>
                    <small class="text-muted">عرض تفصيلي لجميع مذكرات التفاهم والعقود الرسمية</small>
                </div>
            </div>
            <a href="{{ route('partnership.documents.create') }}" class="btn btn-sm btn-primary-modern d-flex align-items-center gap-1">
                <i class="fas fa-file-upload"></i>
                <span>رفع وثيقة جديدة</span>
            </a>
        </div>

        <div class="card-body p-0">
            @if($documents->isEmpty())
                <div class="text-center py-5">
                    <div class="rounded-circle bg-light text-muted d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="fas fa-folder-open fa-2x opacity-50"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">لا توجد وثائق شراكة مسجلة حالياً</h5>
                    <p class="text-muted small mb-3">يمكنك البدء برفع وتوثيق أول اتفاقية أو مذكرة تفاهم مع الشركات الشريكة</p>
                    <a href="{{ route('partnership.documents.create') }}" class="btn btn-primary-modern btn-sm">
                        <i class="fas fa-plus-circle me-1"></i>إضافة أول وثيقة
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="min-width: 980px;">
                        <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                            <tr class="text-secondary fw-bold" style="font-size: 0.83rem;">
                                <th class="py-3 px-3 text-center text-nowrap" style="width: 50px;">#</th>
                                <th class="py-3 text-nowrap" style="min-width: 250px;">اسم الوثيقة والملف</th>
                                <th class="py-3 text-nowrap" style="min-width: 200px;">الشركة الشريكة</th>
                                <th class="py-3 text-center text-nowrap" style="width: 120px;">النوع</th>
                                <th class="py-3 text-nowrap" style="width: 130px;">تاريخ الرفع</th>
                                <th class="py-3 text-nowrap" style="width: 130px;">تاريخ الانتهاء</th>
                                <th class="py-3 text-center text-nowrap" style="width: 100px;">الحالة</th>
                                <th class="py-3 text-center text-nowrap" style="width: 130px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $document)
                            <tr>
                                <td class="px-3 text-center text-muted fw-bold" style="font-size: 0.85rem;">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="rounded-3 bg-light border d-flex align-items-center justify-content-center text-danger flex-shrink-0" style="width: 38px; height: 38px; font-size: 1.1rem;">
                                            @if(str_contains(strtolower($document->file_name ?? $document->file_path), '.pdf'))
                                                <i class="fas fa-file-pdf text-danger"></i>
                                            @elseif(str_contains(strtolower($document->file_name ?? $document->file_path), '.doc'))
                                                <i class="fas fa-file-word text-primary"></i>
                                            @else
                                                <i class="fas fa-file-alt text-secondary"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 0.88rem; line-height: 1.3;">
                                                {{ $document->document_name }}
                                            </div>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">
                                                <i class="fas fa-paperclip me-1"></i>{{ $document->file_name ?? basename($document->file_path) }}
                                                @if(!empty($document->file_size))
                                                    <span class="ms-1 text-muted opacity-75">({{ $document->file_size }})</span>
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary flex-shrink-0" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark" style="font-size: 0.86rem;">
                                                {{ $document->company->name ?? 'غير محدد' }}
                                            </span>
                                            @if(!empty($document->company->industry))
                                                <small class="text-muted d-block" style="font-size: 0.74rem;">{{ $document->company->industry }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center text-nowrap">
                                    @php
                                        $typeBadgeStyle = match($document->document_type) {
                                            'mou' => 'background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe;',
                                            'contract' => 'background:#ecfdf5; color:#059669; border:1px solid #a7f3d0;',
                                            'agreement' => 'background:#f5f3ff; color:#7c3aed; border:1px solid #ddd6fe;',
                                            default => 'background:#f1f5f9; color:#475569; border:1px solid #cbd5e1;'
                                        };
                                    @endphp
                                    <span class="badge rounded-pill px-2.5 py-1.5 fw-bold" style="font-size: 0.75rem; {{ $typeBadgeStyle }}">
                                        {{ $document->document_type_text ?? $document->document_type }}
                                    </span>
                                </td>
                                <td class="text-nowrap">
                                    <span class="text-secondary font-monospace small d-inline-flex align-items-center gap-1">
                                        <i class="fas fa-calendar-upload text-muted" style="font-size: 0.75rem;"></i>
                                        <span>{{ $document->created_at ? $document->created_at->format('Y-m-d') : '--' }}</span>
                                    </span>
                                </td>
                                <td class="text-nowrap">
                                    @if($document->expiry_date)
                                        <span class="text-dark font-monospace small d-inline-flex align-items-center gap-1">
                                            <i class="fas fa-clock text-danger" style="font-size: 0.75rem;"></i>
                                            <span>{{ $document->expiry_date->format('Y-m-d') }}</span>
                                        </span>
                                    @else
                                        <span class="text-muted small">مفتوح / غير محدد</span>
                                    @endif
                                </td>
                                <td class="text-center text-nowrap">
                                    <span class="badge rounded-pill px-2.5 py-1.5 fw-bold" style="font-size: 0.75rem;
                                        @if($document->document_status === 'active') background:#dcfce7; color:#15803d; border:1px solid #86efac;
                                        @elseif($document->document_status === 'expired') background:#fee2e2; color:#b91c1c; border:1px solid #fca5a5;
                                        @else background:#fef3c7; color:#b45309; border:1px solid #fde68a;
                                        @endif">
                                        <i class="fas fa-circle me-1" style="font-size: 0.45rem;"></i>{{ $document->document_status_text ?? $document->document_status }}
                                    </span>
                                </td>
                                <td class="text-center text-nowrap">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="btn btn-sm btn-light border text-primary rounded-2 px-2 py-1 shadow-none" data-bs-toggle="tooltip" title="معاينة وتحميل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('partnership.documents.edit', $document->id) }}" class="btn btn-sm btn-light border text-warning rounded-2 px-2 py-1 shadow-none" data-bs-toggle="tooltip" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('partnership.documents.delete', $document->id) }}" method="POST" class="d-inline m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger rounded-2 px-2 py-1 shadow-none" onclick="return confirm('هل أنت متأكد من حذف هذه الوثيقة نهائياً؟')" data-bs-toggle="tooltip" title="حذف">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- الترقيم والتصفح -->
                @if(method_exists($documents, 'hasPages') && $documents->hasPages())
                <div class="p-3 border-top d-flex justify-content-center">
                    {{ $documents->links() }}
                </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
