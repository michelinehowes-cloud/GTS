@extends('layouts.app')

@section('title', 'لوحة تحكم الشركة')
@section('page-title', 'لوحة تحكم الشركة')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <h3 class="text-primary">مرحباً بك، {{ auth()->user()->name }}</h3>
                <p class="lead">شركة: {{ $company->name ?? 'غير محدد' }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="text-primary text-uppercase small fw-bold">
                            فرص العمل النشطة
                        </div>
                        <div class="h4 mb-0 fw-bold text-dark">
                            {{ $stats['active_jobs'] ?? 0 }}
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="text-primary text-uppercase small fw-bold">
                            إجمالي الطلبات
                        </div>
                        <div class="h4 mb-0 fw-bold text-dark">
                            {{ $stats['total_applications'] ?? 0 }}
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="text-primary text-uppercase small fw-bold">
                            طلبات جديدة
                        </div>
                        <div class="h4 mb-0 fw-bold text-dark">
                            {{ $stats['new_applications'] ?? 0 }}
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">إنشاء فرصة عمل جديدة</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('company.opportunities.create') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label">عنوان الوظيفة</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">نوع الوظيفة</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="full_time">دوام كامل</option>
                                <option value="part_time">دوام جزئي</option>
                                <option value="internship">تدريب</option>
                                <option value="contract">عقد</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">وصف الوظيفة</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="requirements" class="form-label">المتطلبات (افصل بينها بفاصلة)</label>
                        <input type="text" class="form-control" id="requirements" name="requirements" placeholder="مثال: PHP, Laravel, MySQL">
                    </div>
                    <button type="submit" class="btn btn-primary">نشر الوظيفة</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection