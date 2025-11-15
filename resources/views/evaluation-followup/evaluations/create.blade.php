@extends('layouts.app')

@section('title', 'إضافة تقييم جديد - نظام إدارة الخريجين')
@section('page-title', 'إضافة تقييم جديد')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-plus me-2"></i>
                        إضافة تقييم جديد
                    </h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('evaluation-followup.evaluations.store') }}" method="POST" id="evaluationForm">
                        @csrf

                        <div class="row">
                            <!-- نوع التقييم -->
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label fw-bold">
                                    نوع التقييم <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('type') is-invalid @enderror"
                                        id="type" name="type" required>
                                    <option value="">اختر نوع التقييم</option>
                                    <option value="performance" {{ old('type') == 'performance' ? 'selected' : '' }}>
                                        تقييم الأداء
                                    </option>
                                    <option value="training" {{ old('type') == 'training' ? 'selected' : '' }}>
                                        تقييم التدريب
                                    </option>
                                    <option value="company" {{ old('type') == 'company' ? 'selected' : '' }}>
                                        تقييم الشركة
                                    </option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- تاريخ التقييم -->
                            <div class="col-md-6 mb-3">
                                <label for="evaluation_date" class="form-label fw-bold">
                                    تاريخ التقييم <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('evaluation_date') is-invalid @enderror"
                                       id="evaluation_date" name="evaluation_date"
                                       value="{{ old('evaluation_date', date('Y-m-d')) }}" required>
                                @error('evaluation_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- المستخدم المقيم -->
                            <div class="col-md-6 mb-3">
                                <label for="user_id" class="form-label fw-bold">
                                    المستخدم المقيم <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('user_id') is-invalid @enderror"
                                        id="user_id" name="user_id" required>
                                    <option value="">اختر المستخدم</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- المقيم -->
                            <div class="col-md-6 mb-3">
                                <label for="evaluator_id" class="form-label fw-bold">
                                    المقيم <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('evaluator_id') is-invalid @enderror"
                                        id="evaluator_id" name="evaluator_id" required>
                                    <option value="">اختر المقيم</option>
                                    @foreach($evaluators as $evaluator)
                                    <option value="{{ $evaluator->id }}" {{ old('evaluator_id') == $evaluator->id ? 'selected' : '' }}>
                                        {{ $evaluator->name }} ({{ $evaluator->email }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('evaluator_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- التدريب (اختياري) -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="training_id" class="form-label fw-bold">
                                    التدريب (اختياري)
                                </label>
                                <select class="form-select @error('training_id') is-invalid @enderror"
                                        id="training_id" name="training_id">
                                    <option value="">اختر التدريب (اختياري)</option>
                                    @foreach($trainings as $training)
                                    <option value="{{ $training->id }}" {{ old('training_id') == $training->id ? 'selected' : '' }}>
                                        {{ $training->title }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('training_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- الحالة -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label fw-bold">
                                    الحالة <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                    <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>
                                        مسودة
                                    </option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>
                                        مكتمل
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- معايير التقييم -->
                        <div id="criteriaSection" class="mb-4" style="display: none;">
                            <h6 class="fw-bold mb-3">معايير التقييم</h6>
                            <div id="criteriaContainer" class="row">
                                <!-- سيتم إضافة معايير التقييم هنا ديناميكياً -->
                            </div>
                        </div>

                        <!-- التعليقات -->
                        <div class="mb-3">
                            <label for="comments" class="form-label fw-bold">التعليقات</label>
                            <textarea class="form-control @error('comments') is-invalid @enderror"
                                      id="comments" name="comments" rows="3"
                                      placeholder="أدخل تعليقاتك حول التقييم">{{ old('comments') }}</textarea>
                            @error('comments')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- التوصيات -->
                        <div class="mb-3">
                            <label for="recommendations" class="form-label fw-bold">التوصيات</label>
                            <textarea class="form-control @error('recommendations') is-invalid @enderror"
                                      id="recommendations" name="recommendations" rows="3"
                                      placeholder="أدخل توصياتك للتحسين">{{ old('recommendations') }}</textarea>
                            @error('recommendations')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- أزرار التحكم -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('evaluation-followup.evaluations.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                العودة للقائمة
                            </a>
                            <div>
                                <button type="button" class="btn btn-outline-primary me-2" onclick="saveAsDraft()">
                                    <i class="fas fa-save me-1"></i>
                                    حفظ كمسودة
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check me-1"></i>
                                    إنشاء التقييم
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const criteriaSection = document.getElementById('criteriaSection');
    const criteriaContainer = document.getElementById('criteriaContainer');
    const statusSelect = document.getElementById('status');

    // تحديث معايير التقييم عند تغيير النوع
    typeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        updateCriteria(selectedType);
    });

    // تحديث المعايير عند تحميل الصفحة إذا كان هناك نوع محدد
    if (typeSelect.value) {
        updateCriteria(typeSelect.value);
    }

    function updateCriteria(type) {
        if (!type) {
            criteriaSection.style.display = 'none';
            return;
        }

        // الحصول على معايير التقييم من الخادم
        fetch(`/evaluation-followup/evaluations/criteria/${type}`)
            .then(response => response.json())
            .then(data => {
                if (Object.keys(data).length > 0) {
                    renderCriteria(data);
                    criteriaSection.style.display = 'block';
                } else {
                    criteriaSection.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error fetching criteria:', error);
                criteriaSection.style.display = 'none';
            });
    }

    function renderCriteria(criteria) {
        criteriaContainer.innerHTML = '';

        Object.entries(criteria).forEach(([label, key]) => {
            const col = document.createElement('div');
            col.className = 'col-md-6 mb-3';

            col.innerHTML = `
                <label class="form-label fw-bold">${label} <span class="text-danger">*</span></label>
                <select class="form-select" name="scores[${key}]" required>
                    <option value="">اختر الدرجة</option>
                    <option value="1">1 - ضعيف جداً</option>
                    <option value="2">2 - ضعيف</option>
                    <option value="3">3 - متوسط</option>
                    <option value="4">4 - جيد</option>
                    <option value="5">5 - ممتاز</option>
                </select>
            `;

            criteriaContainer.appendChild(col);
        });
    }

    // حفظ كمسودة
    window.saveAsDraft = function() {
        statusSelect.value = 'draft';
        document.getElementById('evaluationForm').submit();
    };

    // التحقق من صحة النموذج
    document.getElementById('evaluationForm').addEventListener('submit', function(e) {
        const requiredSelects = document.querySelectorAll('select[required]');
        let isValid = true;

        requiredSelects.forEach(select => {
            if (!select.value) {
                select.classList.add('is-invalid');
                isValid = false;
            } else {
                select.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('يرجى ملء جميع الحقول المطلوبة');
        }
    });
});
</script>

<style>
.form-label {
    color: #495057;
    font-weight: 600;
}

.card-header {
    border-bottom: none;
}

.btn {
    border-radius: 0.375rem;
}

.form-select:focus,
.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

@media (max-width: 768px) {
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }

    .d-flex.justify-content-between > div {
        text-align: center;
    }
}
</style>
@endsection
