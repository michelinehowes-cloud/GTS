@extends('layouts.app')

@section('title', 'إدارة الاستبيانات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-poll mr-2"></i>
                        إدارة الاستبيانات
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('evaluation-followup.surveys.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> إضافة استبيان جديد
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>عنوان الاستبيان</th>
                                    <th>الجمهور المستهدف</th>
                                    <th>تاريخ البداية</th>
                                    <th>تاريخ النهاية</th>
                                    <th>الحالة</th>
                                    <th>عدد الردود</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($surveys as $survey)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $survey->title }}</strong>
                                            <br>
                                            <small class="text-muted">{{ Str::limit($survey->description, 50) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                @switch($survey->target_audience)
                                                    @case('graduates')
                                                        الخريجين
                                                        @break
                                                    @case('companies')
                                                        الشركات
                                                        @break
                                                    @case('training_coordinators')
                                                        منسقي التدريب
                                                        @break
                                                    @case('all')
                                                        الكل
                                                        @break
                                                @endswitch
                                            </span>
                                        </td>
                                        <td>{{ $survey->start_date->format('Y-m-d') }}</td>
                                        <td>{{ $survey->end_date->format('Y-m-d') }}</td>
                                        <td>
                                            @if($survey->isActive())
                                                <span class="badge badge-success">نشط</span>
                                            @elseif($survey->is_active)
                                                <span class="badge badge-warning">مجدول</span>
                                            @else
                                                <span class="badge badge-secondary">غير نشط</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">{{ $survey->responses_count ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('evaluation-followup.surveys.show', $survey) }}"
                                                   class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> عرض
                                                </a>
                                                <a href="{{ route('evaluation-followup.surveys.edit', $survey) }}"
                                                   class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i> تعديل
                                                </a>
                                                <form action="{{ route('evaluation-followup.surveys.destroy', $survey) }}"
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا الاستبيان؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i> حذف
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-poll fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">لا توجد استبيانات</h5>
                                                <p class="text-muted">ابدأ بإنشاء استبيان جديد</p>
                                                <a href="{{ route('evaluation-followup.surveys.create') }}"
                                                   class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> إضافة أول استبيان
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($surveys->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $surveys->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // إضافة تأكيد الحذف
    $('.btn-danger').on('click', function(e) {
        if (!confirm('هل أنت متأكد من حذف هذا الاستبيان؟ سيتم حذف جميع الردود المرتبطة به.')) {
            e.preventDefault();
        }
    });
});
</script>
@endsection
