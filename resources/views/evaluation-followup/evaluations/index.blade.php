@extends('layouts.app')

@section('title', 'إدارة التقييمات - نظام إدارة الخريجين')
@section('page-title', 'إدارة التقييمات')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-star me-2"></i>
                            قائمة التقييمات
                        </h5>
                        <a href="{{ route('evaluation-followup.evaluations.create') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-plus me-1"></i>
                            إضافة تقييم جديد
                        </a>
                    </div>

                    <div class="card-body">
                        <!-- فلاتر البحث -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <select class="form-select" id="typeFilter">
                                    <option value="">جميع الأنواع</option>
                                    <option value="performance">تقييم الأداء</option>
                                    <option value="training">تقييم التدريب</option>
                                    <option value="company">تقييم الشركة</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="statusFilter">
                                    <option value="">جميع الحالات</option>
                                    <option value="draft">مسودة</option>
                                    <option value="completed">مكتمل</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" id="dateFilter" placeholder="تاريخ التقييم">
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                                    <i class="fas fa-times me-1"></i>
                                    مسح الفلاتر
                                </button>
                            </div>
                        </div>

                        <!-- جدول التقييمات -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="evaluationsTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>نوع التقييم</th>
                                        <th>التدريب</th>
                                        <th>المقيم</th>
                                        <th>التاريخ</th>
                                        <th>الحالة</th>
                                        <th>متوسط الدرجات</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($evaluations as $evaluation)
                                        <tr>
                                            <td>{{ $evaluation->id }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $evaluation->type == 'performance' ? 'primary' : ($evaluation->type == 'training' ? 'success' : 'info') }}">
                                                    @if($evaluation->type == 'performance')
                                                        تقييم الأداء
                                                    @elseif($evaluation->type == 'training')
                                                        تقييم التدريب
                                                    @elseif($evaluation->type == 'employment')
                                                        تقييم التوظيف
                                                    @else
                                                        تقييم الشركة
                                                    @endif
                                                </span>
                                            </td>
                                            <td>{{ $evaluation->training->title ?? '-' }}</td>
                                            <td>{{ $evaluation->evaluator->name ?? '-' }}</td>
                                            <td>{{ $evaluation->evaluation_date ? \Carbon\Carbon::parse($evaluation->evaluation_date)->format('Y/m/d') : '-' }}
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $evaluation->status == 'completed' ? 'success' : 'warning' }}">
                                                    {{ $evaluation->status == 'completed' ? 'مكتمل' : 'مسودة' }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $allScores = [];
                                                    if ($evaluation->facilities_evaluation) {
                                                        $allScores = array_merge($allScores, array_filter((array) $evaluation->facilities_evaluation, 'is_numeric'));
                                                    }
                                                    if ($evaluation->content_evaluation) {
                                                        $allScores = array_merge($allScores, array_filter((array) $evaluation->content_evaluation, 'is_numeric'));
                                                    }
                                                    if ($evaluation->trainer_evaluation) {
                                                        $allScores = array_merge($allScores, array_filter((array) $evaluation->trainer_evaluation, 'is_numeric'));
                                                    }
                                                    if ($evaluation->organization_evaluation) {
                                                        $allScores = array_merge($allScores, array_filter((array) $evaluation->organization_evaluation, 'is_numeric'));
                                                    }
                                                    if ($evaluation->impact_evaluation) {
                                                        $allScores = array_merge($allScores, array_filter((array) $evaluation->impact_evaluation, 'is_numeric'));
                                                    }
                                                    if ($evaluation->employment_evaluation) {
                                                        $allScores = array_merge($allScores, array_filter((array) $evaluation->employment_evaluation, 'is_numeric'));
                                                    }
                                                    $avgScore = count($allScores) > 0 ? array_sum($allScores) / count($allScores) : 0;
                                                @endphp
                                                @if($avgScore > 0)
                                                    <span
                                                        class="fw-bold text-{{ $avgScore >= 4 ? 'success' : ($avgScore >= 3 ? 'warning' : 'danger') }}">
                                                        {{ number_format($avgScore, 1) }}/5
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('evaluation-followup.evaluations.show', $evaluation) }}"
                                                        class="btn btn-sm btn-outline-primary" title="عرض">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('evaluation-followup.evaluations.edit', $evaluation) }}"
                                                        class="btn btn-sm btn-outline-secondary" title="تعديل">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form
                                                        action="{{ route('evaluation-followup.evaluations.destroy', $evaluation) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا التقييم؟')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">لا توجد تقييمات</h5>
                                                <a href="{{ route('evaluation-followup.evaluations.create') }}"
                                                    class="btn btn-primary">
                                                    <i class="fas fa-plus me-1"></i> إضافة تقييم
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- التنقل بين الصفحات -->
                        @if($evaluations->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $evaluations->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // تطبيق الفلاتر
            const typeFilter = document.getElementById('typeFilter');
            const statusFilter = document.getElementById('statusFilter');
            const dateFilter = document.getElementById('dateFilter');

            [typeFilter, statusFilter, dateFilter].forEach(filter => {
                filter.addEventListener('change', applyFilters);
            });

            function applyFilters() {
                const type = typeFilter.value;
                const status = statusFilter.value;
                const date = dateFilter.value;

                const rows = document.querySelectorAll('#evaluationsTable tbody tr');
                rows.forEach(row => {
                    if (row.cells.length < 9) return; // تخطي صف الرسالة الفارغة

                    const rowType = row.cells[2].textContent.trim();
                    const rowStatus = row.cells[6].textContent.trim();
                    const rowDate = row.cells[5].textContent.trim();

                    let showRow = true;

                    if (type && !rowType.includes(getTypeText(type))) {
                        showRow = false;
                    }

                    if (status && !rowStatus.includes(getStatusText(status))) {
                        showRow = false;
                    }

                    if (date && rowDate !== formatDate(date)) {
                        showRow = false;
                    }

                    row.style.display = showRow ? '' : 'none';
                });
            }

            function getTypeText(type) {
                const types = {
                    'performance': 'تقييم الأداء',
                    'training': 'تقييم التدريب',
                    'company': 'تقييم الشركة'
                };
                return types[type] || '';
            }

            function getStatusText(status) {
                const statuses = {
                    'draft': 'مسودة',
                    'completed': 'مكتمل'
                };
                return statuses[status] || '';
            }

            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.getFullYear() + '/' +
                    String(date.getMonth() + 1).padStart(2, '0') + '/' +
                    String(date.getDate()).padStart(2, '0');
            }

            window.clearFilters = function () {
                typeFilter.value = '';
                statusFilter.value = '';
                dateFilter.value = '';

                const rows = document.querySelectorAll('#evaluationsTable tbody tr');
                rows.forEach(row => {
                    row.style.display = '';
                });
            };
        });
    </script>

    <style>
        .avatar-circle {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }

        .table-responsive {
            border-radius: 0.375rem;
        }

        .table th {
            border-top: none;
            font-weight: 600;
        }

        .btn-group .btn {
            margin-right: 2px;
        }

        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }

            .btn-group {
                flex-direction: column;
            }

            .btn-group .btn {
                margin-right: 0;
                margin-bottom: 2px;
            }
        }
    </style>
@endsection