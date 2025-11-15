@extends('layouts.app')

@section('title', 'إدارة الإعلانات')

@section('page-title', 'إدارة الإعلانات')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bullhorn me-2"></i>الإعلانات
                </h5>
                <a href="{{ route('media.announcements.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>إعلان جديد
                </a>
            </div>

            <div class="card-body">
                <!-- نموذج البحث والتصفية -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control"
                               placeholder="البحث في العنوان أو المحتوى..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="is_active" class="form-select">
                            <option value="">جميع الحالات</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="period" class="form-select">
                            <option value="">جميع الفترات</option>
                            <option value="current" {{ request('period') === 'current' ? 'selected' : '' }}>حالي</option>
                            <option value="upcoming" {{ request('period') === 'upcoming' ? 'selected' : '' }}>قادم</option>
                            <option value="expired" {{ request('period') === 'expired' ? 'selected' : '' }}>منتهي</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="fas fa-search me-1"></i>بحث
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('media.announcements.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times me-1"></i>مسح
                        </a>
                    </div>
                </form>

                <!-- جدول الإعلانات -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>العنوان</th>
                                <th>تاريخ البدء</th>
                                <th>تاريخ الانتهاء</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($announcements as $announcement)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $announcement->title }}</strong>
                                        <br>
                                        <small class="text-muted">{{ Str::limit(strip_tags($announcement->content), 50) }}</small>
                                    </div>
                                </td>
                                <td>{{ $announcement->start_date->format('d/m/Y') }}</td>
                                <td>{{ $announcement->end_date->format('d/m/Y') }}</td>
                                <td>
                                    @if($announcement->is_active)
                                        @if($announcement->isCurrent())
                                            <span class="badge bg-success">نشط</span>
                                        @elseif($announcement->isExpired())
                                            <span class="badge bg-secondary">منتهي</span>
                                        @else
                                            <span class="badge bg-warning">قادم</span>
                                        @endif
                                    @else
                                        <span class="badge bg-danger">معطل</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('media.announcements.show', $announcement) }}"
                                           class="btn btn-sm btn-outline-info"
                                           title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('media.announcements.edit', $announcement) }}"
                                           class="btn btn-sm btn-outline-warning"
                                           title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('media.announcements.toggle-status', $announcement) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="btn btn-sm {{ $announcement->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                    title="{{ $announcement->is_active ? 'تعطيل' : 'تفعيل' }}">
                                                <i class="fas {{ $announcement->is_active ? 'fa-times' : 'fa-check' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('media.announcements.destroy', $announcement) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الإعلان؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">لا توجد إعلانات</p>
                                    <a href="{{ route('media.announcements.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i>إضافة إعلان جديد
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- التنقل بين الصفحات -->
                @if($announcements->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $announcements->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.table th {
    border-top: none;
    font-weight: 600;
}

.btn-group .btn {
    margin-right: 2px;
}

.badge {
    font-size: 0.75em;
}
</style>
@endpush
