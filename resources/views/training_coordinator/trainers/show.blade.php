@extends('layouts.app')

@section('title', 'تفاصيل المدرب')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chalkboard-teacher text-primary"></i>
                تفاصيل المدرب
            </h1>
            <div>
                <a href="{{ route('training-coordinator.trainers.edit', $trainer->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <a href="{{ route('training-coordinator.trainers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i> العودة للقائمة
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-body text-center">
                        @if($trainer->photo)
                            <img src="{{ Storage::url($trainer->photo) }}" alt="{{ $trainer->name }}"
                                class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mx-auto mb-3"
                                style="width: 150px; height: 150px; font-size: 3rem;">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                        <h4>{{ $trainer->name }}</h4>
                        <p class="text-muted">{{ $trainer->specialization }}</p>

                        @if($trainer->linkedin_url)
                            <a href="{{ $trainer->linkedin_url }}" target="_blank" class="btn btn-sm btn-primary">
                                <i class="fab fa-linkedin"></i> LinkedIn
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">المعلومات الأساسية</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>البريد الإلكتروني:</strong></div>
                            <div class="col-md-8">{{ $trainer->email ?? '-' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>الهاتف:</strong></div>
                            <div class="col-md-8">{{ $trainer->phone ?? '-' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>التخصص:</strong></div>
                            <div class="col-md-8">{{ $trainer->specialization }}</div>
                        </div>
                        @if($trainer->bio)
                            <div class="row">
                                <div class="col-md-4"><strong>النبذة:</strong></div>
                                <div class="col-md-8">{{ $trainer->bio }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">التدريبات المرتبطة ({{ $trainer->trainings->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if($trainer->trainings->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>عنوان التدريب</th>
                                            <th>النوع</th>
                                            <th>تاريخ البدء</th>
                                            <th>الحالة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($trainer->trainings as $training)
                                            <tr>
                                                <td>{{ $training->title }}</td>
                                                <td>{{ $training->type_arabic }}</td>
                                                <td>{{ $training->start_date->format('Y-m-d') }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $training->status == 'active' ? 'success' : 'secondary' }}">
                                                        {{ $training->status_arabic }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center">لا توجد تدريبات مرتبطة بهذا المدرب حالياً</p>
                        @endif
                    </div>
                </div>

                {{-- قسم التقييمات --}}
                <div class="card shadow mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-star text-warning"></i>
                            تقييمات المدرب
                        </h5>
                        @if($averageRating)
                            <div class="text-end">
                                <span class="badge bg-warning text-dark fs-5">
                                    <i class="fas fa-star"></i>
                                    {{ number_format($averageRating, 1) }} / 5
                                </span>
                                <small class="text-muted d-block">من {{ $trainer->evaluations->count() }} تقييم</small>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        {{-- نموذج إضافة تقييم جديد (للمستخدمين الذين لديهم صلاحية) --}}
                        @if(auth()->user()->role == 'evaluation_followup')
                            <div class="alert alert-info">
                                <h6><i class="fas fa-plus-circle"></i> إضافة تقييم جديد</h6>
                                <form action="{{ route('training-coordinator.trainers.evaluate', $trainer) }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">التدريب المرتبط <span class="text-danger">*</span></label>
                                            <select name="training_id" class="form-select" required>
                                                <option value="">-- اختر التدريب --</option>
                                                @foreach($trainer->trainings as $training)
                                                    <option value="{{ $training->id }}">{{ $training->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">التقييم <span class="text-danger">*</span></label>
                                            <select name="rating" class="form-select" required>
                                                <option value="">-- اختر التقييم --</option>
                                                <option value="5">⭐⭐⭐⭐⭐ ممتاز (5)</option>
                                                <option value="4">⭐⭐⭐⭐ جيد جداً (4)</option>
                                                <option value="3">⭐⭐⭐ جيد (3)</option>
                                                <option value="2">⭐⭐ مقبول (2)</option>
                                                <option value="1">⭐ ضعيف (1)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">نقاط القوة</label>
                                            <textarea name="strengths" class="form-control" rows="3"
                                                placeholder="اذكر نقاط القوة..."></textarea>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">نقاط الضعف</label>
                                            <textarea name="weaknesses" class="form-control" rows="3"
                                                placeholder="اذكر نقاط الضعف..."></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">التوصيات</label>
                                            <textarea name="recommendations" class="form-control" rows="3"
                                                placeholder="اذكر التوصيات..."></textarea>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">ملاحظات إضافية</label>
                                            <textarea name="notes" class="form-control" rows="3"
                                                placeholder="ملاحظات أخرى..."></textarea>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> حفظ التقييم
                                    </button>
                                </form>
                            </div>
                            <hr>
                        @endif

                        {{-- قائمة التقييمات السابقة --}}
                        @if($trainer->trainerEvaluations->count() > 0)
                            <h6 class="mb-3">التقييمات السابقة ({{ $trainer->trainerEvaluations->count() }})</h6>
                            @foreach($trainer->trainerEvaluations as $trainerEvaluation)
                                @php
                                    $parentEvaluation = $trainerEvaluation->evaluation;
                                    $rating = $trainerEvaluation->rating ?? 0;
                                @endphp
                                <div
                                    class="card mb-3 border-start border-4 border-{{ $rating >= 4 ? 'success' : ($rating >= 3 ? 'warning' : 'danger') }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1">
                                                    <i class="fas fa-graduation-cap text-primary"></i>
                                                    {{ $parentEvaluation?->training?->title ?? 'تدريب محذوف / غير متاح' }}
                                                </h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-user"></i>
                                                    بواسطة: {{ $parentEvaluation?->evaluator?->name ?? 'غير معروف' }}
                                                    |
                                                    <i class="fas fa-calendar"></i>
                                                    {{ $trainerEvaluation->created_at->format('Y-m-d') }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span
                                                    class="badge bg-{{ $rating >= 4 ? 'success' : ($rating >= 3 ? 'warning' : 'danger') }} fs-6">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $rating)
                                                            <i class="fas fa-star"></i>
                                                        @else
                                                            <i class="far fa-star"></i>
                                                        @endif
                                                    @endfor
                                                    ({{ $rating }}/5)
                                                </span>
                                            </div>
                                        </div>

                                        @if($trainerEvaluation->comments)
                                            <div class="mt-2">
                                                <strong class="text-secondary"><i class="fas fa-comment"></i> ملاحظات:</strong>
                                                <p class="mb-0 ms-3">{{ $trainerEvaluation->comments }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-light text-center">
                                <i class="fas fa-info-circle"></i>
                                لا توجد تقييمات لهذا المدرب حتى الآن
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection