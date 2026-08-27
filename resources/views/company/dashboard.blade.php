@extends('layouts.app')

@section('title', 'لوحة تحكم الشركة')
@section('page-title', 'لوحة تحكم الشركة')

@section('content')
    <div class="container-fluid py-4">
        <!-- Breadcrumbs -->
        @include('components.breadcrumbs', [
            'items' => [
                ['label' => 'الرئيسية', 'url' => route('home')],
                ['label' => 'لوحة تحكم الشركة', 'active' => true],
            ]
        ])

        <!-- بطاقة الترحيب (Welcome Card) -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="bento-card" style="background: linear-gradient(135deg, rgba(30, 41, 59, 0.8), rgba(15, 23, 42, 0.9)); border-left: 4px solid var(--bento-gold);">
                    <div class="d-flex align-items-center">
                        <div class="avatar-md bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-4" style="width: 70px; height: 70px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
                            <img src="{{ $company && $company->logo_path ? Storage::url($company->logo_path) : asset('images/default-company.png') }}" alt="شعار الشركة" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                        </div>
                        <div>
                            <h2 class="mb-1 fw-bold text-white" style="letter-spacing: -0.5px;">مرحباً بك، {{ auth()->user()->name }} 👋</h2>
                            <p class="mb-0 text-white-50 fs-5"><i class="fas fa-building me-2"></i> {{ $company->name ?? 'شركة غير محددة' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الإحصائيات (Bento Grid Stats) -->
        <div class="bento-grid">
            <!-- فرص العمل الفعالة -->
            <div class="bento-card">
                <div class="bento-card-header">
                    <h3 class="bento-card-title">فرص العمل الفعالة</h3>
                    <div class="bento-card-icon bento-icon-primary">
                        <i class="fas fa-briefcase"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-end mt-1">
                    <div class="bento-stat">{{ $stats['active_jobs'] ?? 0 }}</div>
                    <div class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-2 mb-2">
                        مستمر <i class="fas fa-arrow-trend-up ms-1"></i>
                    </div>
                </div>
                <div class="bento-desc mt-2">الوظائف المفتوحة حالياً للتقديم</div>
            </div>

            <!-- إجمالي الطلبات -->
            <div class="bento-card">
                <div class="bento-card-header">
                    <h3 class="bento-card-title">إجمالي الطلبات</h3>
                    <div class="bento-card-icon bento-icon-success">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-end mt-1">
                    <div class="bento-stat">{{ $stats['total_applications'] ?? 0 }}</div>
                    <div class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2 mb-2">
                        نشط <i class="fas fa-bolt ms-1"></i>
                    </div>
                </div>
                <div class="bento-desc mt-2">إجمالي طلبات التوظيف المستلمة</div>
            </div>

            <!-- طلبات جديدة -->
            <div class="bento-card">
                <div class="bento-card-header">
                    <h3 class="bento-card-title">طلبات جديدة</h3>
                    <div class="bento-card-icon bento-icon-warning">
                        <i class="fas fa-bell"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-end mt-1">
                    <div class="bento-stat">{{ $stats['new_applications'] ?? 0 }}</div>
                    <div class="badge rounded-pill bg-warning bg-opacity-10 text-warning px-3 py-2 mb-2" style="color: #d97706 !important;">
                        جديد <i class="fas fa-fire ms-1"></i>
                    </div>
                </div>
                <div class="bento-desc mt-2">طلبات التوظيف الجديدة قيد المراجعة</div>
            </div>
            
            <!-- مسح الباركود (QR) -->
            <div class="bento-card">
                <div class="bento-card-header">
                    <h3 class="bento-card-title">مسح الباركود (QR)</h3>
                    <div class="bento-card-icon bento-icon-gold">
                        <i class="fas fa-qrcode"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-end mt-1">
                    <div class="bento-stat">{{ $stats['qr_scans'] ?? 0 }}</div>
                    <div class="badge rounded-pill bg-info bg-opacity-10 text-info px-3 py-2 mb-2">
                        متاح <i class="fas fa-check-circle ms-1"></i>
                    </div>
                </div>
                <div class="bento-desc mt-2">عمليات مسح كود التوظيف بالمعرض</div>
                <div class="mt-3">
                    <a href="#" class="btn-bento-outline w-100 text-center text-decoration-none">مسح باركود جديد <i class="fas fa-arrow-left ms-2"></i></a>
                </div>
            </div>
        </div>

        <!-- إضافة وظيفة جديدة (Bento Form) -->
        <div class="row">
            <div class="col-12">
                <div class="bento-card">
                    <div class="bento-card-header border-bottom border-secondary pb-3 mb-4">
                        <h3 class="bento-card-title text-white fs-4">
                            <i class="fas fa-plus-circle me-2 text-primary"></i>إنشاء فرصة عمل جديدة
                        </h3>
                    </div>
                    
                    <form action="{{ route('job-opportunities.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">عنوان الوظيفة</label>
                                <input type="text" class="form-control" id="title" name="title" required placeholder="مثال: مطور ويب، محاسب...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">نوع الوظيفة</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="" disabled selected>اختر نوع الوظيفة...</option>
                                    <option value="job">وظيفة (Job)</option>
                                    <option value="training">تدريب (Training)</option>
                                    <option value="internship">برنامج تدريب عملي (Internship)</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="contract_type" class="form-label">نوع العقد</label>
                                <select class="form-select" id="contract_type" name="contract_type">
                                    <option value="" disabled selected>اختر نوع العقد...</option>
                                    <option value="full_time">دوام كامل</option>
                                    <option value="part_time">دوام جزئي</option>
                                    <option value="contract">عقد محدد المدة</option>
                                    <option value="freelance">مستقل (Freelance)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="salary" class="form-label">الراتب المتوقع (اختياري)</label>
                                <input type="number" class="form-control" id="salary" name="salary" placeholder="أدخل الراتب بالدينار...">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">وصف الوظيفة والمهام</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required placeholder="اكتب تفاصيل الوظيفة والمهام المطلوبة بوضوح..."></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">موقع العمل</label>
                                <input type="text" class="form-control" id="location" name="location" required placeholder="مثال: طرابلس، عن بعد...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="seats" class="form-label">عدد المقاعد الشاغرة</label>
                                <input type="number" class="form-control" id="seats" name="seats" required min="1" value="1">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="start_date" class="form-label">تاريخ البداية</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="end_date" class="form-label">تاريخ النهاية</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="application_deadline" class="form-label">آخر موعد للتقديم</label>
                                <input type="date" class="form-control" id="application_deadline" name="application_deadline" required>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn-bento">
                                <i class="fas fa-paper-plane me-2"></i> إرسال الوظيفة للاعتماد
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection