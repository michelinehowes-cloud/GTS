@extends('layouts.app')

@section('title', 'لوحة تحكم الشركة')
@section('page-title', 'لوحة تحكم الشركة')

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumbs -->
        @include('components.breadcrumbs', [
            'items' => [
                ['label' => 'الرئيسية', 'url' => route('home')],
                ['label' => 'لوحة تحكم الشركة', 'active' => true],
            ]
        ])

        <!-- بطاقة الترحيب -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card-modern bg-white">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar-md bg-light-primary text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                <i class="fas fa-building fa-2x"></i>
                            </div>
                            <div>
                                <h3 class="text-primary mb-1">مرحباً بك، {{ auth()->user()->name }}</h3>
                                <p class="text-muted mb-0 lead">شركة: {{ $company->name ?? 'غير محدد' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الإحصائيات -->
        <div class="row mb-4">
            @include('components.stat-card', [
                'title' => 'فرص العمل النشطة',
                'value' => $stats['active_jobs'] ?? 0,
                'icon' => 'fas fa-briefcase',
                'color' => 'primary',
                'col' => 'col-xl-4 col-md-6 mb-4',
                'description' => 'الوظائف المتاحة حالياً للتقديم'
            ])

            @include('components.stat-card', [
                'title' => 'إجمالي الطلبات',
                'value' => $stats['total_applications'] ?? 0,
                'icon' => 'fas fa-file-alt',
                'color' => 'info',
                'col' => 'col-xl-4 col-md-6 mb-4',
                'description' => 'جميع طلبات التوظيف المستلمة'
            ])

            @include('components.stat-card', [
                'title' => 'طلبات جديدة',
                'value' => $stats['new_applications'] ?? 0,
                'icon' => 'fas fa-bell',
                'color' => 'warning',
                'col' => 'col-xl-4 col-md-6 mb-4',
                'description' => 'طلبات بانتظار المراجعة'
            ])
        </div>

        <!-- نموذج إنشاء فرصة عمل -->
        <div class="row">
            <div class="col-12">
                <div class="card-modern">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 text-primary">
                            <i class="fas fa-plus-circle me-2"></i>إنشاء فرصة عمل جديدة
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('company.opportunities.create') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="title" class="form-label-modern">عنوان الوظيفة</label>
                                    <input type="text" class="form-control-modern" id="title" name="title" required placeholder="مثال: مطور ويب، محاسب...">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="type" class="form-label-modern">نوع الوظيفة</label>
                                    <select class="form-select-modern" id="type" name="type" required>
                                        <option value="" disabled selected>اختر نوع الوظيفة...</option>
                                        <option value="full_time">دوام كامل</option>
                                        <option value="part_time">دوام جزئي</option>
                                        <option value="internship">تدريب</option>
                                        <option value="contract">عقد</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label-modern">وصف الوظيفة</label>
                                <textarea class="form-control-modern" id="description" name="description" rows="4" required placeholder="اكتب تفاصيل الوظيفة والمهام المطلوبة..."></textarea>
                            </div>
                            <div class="mb-4">
                                <label for="requirements" class="form-label-modern">المتطلبات (افصل بينها بفاصلة)</label>
                                <input type="text" class="form-control-modern" id="requirements" name="requirements" placeholder="مثال: PHP, Laravel, MySQL">
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary-modern px-5">
                                    <i class="fas fa-paper-plane me-2"></i>نشر الوظيفة
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection