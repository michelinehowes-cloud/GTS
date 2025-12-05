@extends("layouts.app")

@section("title", "تفاصيل برنامج التدريب")
@section("page-title", "إدارة برامج التدريب")

@section("content")
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">تفاصيل برنامج التدريب: {{ $training->title }}</h6>
                    <a href="{{ route("training-coordinator.trainings") }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <!-- معلومات التدريب الرئيسية -->
                        <div class="col-lg-6">
                            <div class="p-3 bg-light rounded border h-100">
                                <h5 class="text-primary mb-3 border-bottom pb-2">المعلومات الأساسية</h5>
                                <p><strong><i class="fas fa-graduation-cap me-2"></i> اسم البرنامج:</strong> {{ $training->title }}</p>
                                <p><strong><i class="fas fa-tag me-2"></i> الفئة:</strong> {{ $training->category ?? "غير محدد" }}</p>
                                <p><strong><i class="fas fa-lightbulb me-2"></i> نوع البرنامج:</strong>
                                    @switch($training->type)
                                        @case("workshop") ورشة عمل @break
                                        @case("course") دورة @break
                                        @case("seminar") ندوة @break
                                        @case("internship") تدريب عملي @break
                                        @default {{ $training->type }}
                                    @endswitch
                                </p>
                                <p><strong><i class="fas fa-building me-2"></i> الشركة المنظمة:</strong> {{ $training->company->name ?? "غير محدد" }}</p>
                                <p><strong><i class="fas fa-user-tie me-2"></i> منسق التدريب:</strong> {{ $training->coordinator->name ?? "غير محدد" }}</p>
                                <p><strong><i class="fas fa-chalkboard-teacher me-2"></i> اسم المدرب:</strong> {{ $training->instructor_name ?? "غير محدد" }}</p>
                                <p><strong><i class="fas fa-chart-line me-2"></i> الحالة:</strong>
                                    <span class="badge bg-{{ $training->status == 'active' ? 'success' : ($training->status == 'completed' ? 'info' : 'secondary') }}">
                                        @if($training->status == 'active')
                                            نشط
                                        @elseif($training->status == 'completed')
                                            مكتمل
                                        @else
                                            غير نشط
                                        @endif
                                    </span>
                                </p>
                            </div>
                        </div>

                        <!-- تفاصيل التوقيت والمكان -->
                        <div class="col-lg-6">
                            <div class="p-3 bg-light rounded border h-100">
                                <h5 class="text-primary mb-3 border-bottom pb-2">التوقيت والمكان</h5>
                                <p><strong><i class="fas fa-clock me-2"></i> المدة:</strong> {{ $training->duration }}</p>
                                <p><strong><i class="fas fa-calendar-alt me-2"></i> تاريخ البدء:</strong> {{ \Carbon\Carbon::parse($training->start_date)->format("Y-m-d") }}</p>
                                <p><strong><i class="fas fa-calendar-check me-2"></i> تاريخ الانتهاء:</strong> {{ \Carbon\Carbon::parse($training->end_date)->format("Y-m-d") }}</p>
                                <p><strong><i class="fas fa-map-marker-alt me-2"></i> المكان:</strong> {{ $training->location }}</p>
                                <p><strong><i class="fas fa-users me-2"></i> عدد المقاعد:</strong> {{ $training->seats }}</p>
                                <p><strong><i class="fas fa-plus-circle me-2"></i> تاريخ الإنشاء:</strong> {{ $training->created_at->format("Y-m-d H:i") }}</p>
                                <p><strong><i class="fas fa-sync-alt me-2"></i> آخر تحديث:</strong> {{ $training->updated_at->format("Y-m-d H:i") }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- تفاصيل المحتوى: الوصف والأهداف والمتطلبات والمؤهلات -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3 border-bottom pb-2">تفاصيل المحتوى</h5>
                            
                            <div class="mb-4">
                                <h6 class="font-weight-bold"><i class="fas fa-file-alt me-2"></i> وصف البرنامج:</h6>
                                <div class="p-3 bg-light rounded border">
                                    {{ $training->description }}
                                </div>
                            </div>

                            @if($training->objectives)
                            <div class="mb-4">
                                <h6 class="font-weight-bold"><i class="fas fa-bullseye me-2"></i> أهداف البرنامج:</h6>
                                <div class="p-3 bg-light rounded border">
                                    {{ $training->objectives }}
                                </div>
                            </div>
                            @endif

                            @if($training->requirements)
                            <div class="mb-4">
                                <h6 class="font-weight-bold"><i class="fas fa-list-alt me-2"></i> متطلبات البرنامج:</h6>
                                <div class="p-3 bg-light rounded border">
                                    {{ $training->requirements }}
                                </div>
                            </div>
                            @endif

                            @if($training->instructor_qualifications)
                            <div class="mb-4">
                                <h6 class="font-weight-bold"><i class="fas fa-user-graduate me-2"></i> مؤهلات المدرب:</h6>
                                <div class="p-3 bg-light rounded border">
                                    {{ $training->instructor_qualifications }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- قسم التقييمات -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3 border-bottom pb-2">التقييمات</h5>
                            @if($evaluations->count() > 0)
                                <div class="accordion" id="evaluationAccordion">
                                    @foreach($evaluations as $index => $evaluation)
                                        <div class="accordion-item mb-2">
                                            <h2 class="accordion-header" id="heading{{ $evaluation->id }}">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $evaluation->id }}" aria-expanded="false" aria-controls="collapse{{ $evaluation->id }}">
                                                    تقييم بواسطة: {{ $evaluation->evaluator->name ?? "غير معروف" }} - بتاريخ: {{ $evaluation->created_at->format("Y-m-d") }} | التقييم العام: {{ $evaluation->overall_rating ?? '0' }}/5
                                                </button>
                                            </h2>
                                            <div id="collapse{{ $evaluation->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $evaluation->id }}" data-bs-parent="#evaluationAccordion">
                                                <div class="accordion-body">
                                                    
                                                        <div class="mb-4 p-3 border rounded">
                                                            <!-- الدرجات العامة -->
                                                            <div class="row mb-4">
                                                                <div class="col-md-6">
                                                                    <div class="card border-primary">
                                                                        <div class="card-body text-center">
                                                                            <h5 class="card-title text-primary">الدرجة الكلية</h5>
                                                                            <h2 class="text-primary">{{ $evaluation->score ?? "0" }}/5</h2>
                                                                            <div class="progress mt-2">
                                                                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($evaluation->score ?? 0) * 20 }}%" aria-valuenow="{{ $evaluation->score ?? 0 }}" aria-valuemin="0" aria-valuemax="5"></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="card border-info">
                                                                        <div class="card-body text-center">
                                                                            <h5 class="card-title text-info">التقييم الإجمالي</h5>
                                                                            <h2 class="text-info">{{ $evaluation->overall_rating ?? "0" }}/5</h2>
                                                                            <div class="progress mt-2">
                                                                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ ($evaluation->overall_rating ?? 0) * 20 }}%" aria-valuenow="{{ $evaluation->overall_rating ?? 0 }}" aria-valuemin="0" aria-valuemax="5"></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- تقييم المرافق -->
                                                            @if($evaluation->facilities_evaluation && is_array($evaluation->facilities_evaluation))
                                                                <div class="mb-4">
                                                                    <h6 class="text-success"><i class="fas fa-building me-2"></i>تقييم المرافق</h6>
                                                                    <div class="row">
                                                                        @php
                                                                            $facilitiesLabels = [
                                                                                'room_quality' => 'جودة الغرفة',
                                                                                'equipment' => 'المعدات',
                                                                                'comfort' => 'الراحة',
                                                                                'cleanliness' => 'النظافة',
                                                                                'accessibility' => 'سهولة الوصول',
                                                                                'lighting' => 'الإضاءة',
                                                                                'ventilation' => 'التهوية',
                                                                                'noise_level' => 'مستوى الضوضاء'
                                                                            ];
                                                                        @endphp
                                                                        @foreach($evaluation->facilities_evaluation as $key => $value)
                                                                            <div class="col-md-6 mb-2">
                                                                                <div class="d-flex justify-content-between align-items-center">
                                                                                    <strong>{{ $facilitiesLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                                                    <span class="badge bg-success">{{ is_numeric($value) ? $value . '/5' : $value }}</span>
                                                                                </div>
                                                                                @if(is_numeric($value))
                                                                                    <div class="progress" style="height: 6px;">
                                                                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $value * 20 }}%" aria-valuenow="{{ $value }}" aria-valuemin="0" aria-valuemax="5"></div>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            <!-- تقييم المحتوى -->
                                                            @if($evaluation->content_evaluation && is_array($evaluation->content_evaluation))
                                                                <div class="mb-4">
                                                                    <h6 class="text-primary"><i class="fas fa-book me-2"></i>تقييم المحتوى</h6>
                                                                     @if(array_keys($evaluation->content_evaluation) === range(0, count($evaluation->content_evaluation) - 1))
                                                                        {{-- إنه مصفوفة مفهرسة (قائمة أيام) --}}
                                                                        @foreach($evaluation->content_evaluation as $dayEval)
                                                                            <div class="card mb-2 bg-light">
                                                                                <div class="card-body p-2">
                                                                                    <strong>اليوم: {{ $dayEval['date'] ?? 'غير محدد' }}</strong> - التقييم: {{ $dayEval['score'] ?? 0 }}/5
                                                                                    <p class="mb-0 text-muted small">{{ $dayEval['topics'] ?? '' }}</p>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                     @else
                                                                        {{-- إنه كائن (معايير) --}}
                                                                        <div class="row">
                                                                            @php
                                                                                $contentLabels = [
                                                                                    'relevance' => 'الصلة بالموضوع',
                                                                                    'quality' => 'الجودة',
                                                                                    'organization' => 'التنظيم',
                                                                                    'practical' => 'العملية',
                                                                                    'updated' => 'التحديث'
                                                                                ];
                                                                            @endphp
                                                                            @foreach($evaluation->content_evaluation as $key => $value)
                                                                                 <div class="col-md-6 mb-2">
                                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                                        <strong>{{ $contentLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                                                        <span class="badge bg-primary">{{ is_numeric($value) ? $value . '/5' : $value }}</span>
                                                                                    </div>
                                                                                    @if(is_numeric($value))
                                                                                        <div class="progress" style="height: 6px;">
                                                                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $value * 20 }}%" aria-valuenow="{{ $value }}" aria-valuemin="0" aria-valuemax="5"></div>
                                                                                        </div>
                                                                                    @endif
                                                                                 </div>
                                                                            @endforeach
                                                                        </div>
                                                                     @endif
                                                                </div>
                                                            @endif

                                                            <!-- تقييم المدربين (من العلاقة) -->
                                                            @if($evaluation->trainerEvaluations->count() > 0)
                                                                <div class="mb-4">
                                                                    <h6 class="text-warning"><i class="fas fa-chalkboard-teacher me-2"></i>تقييم المدربين</h6>
                                                                    @foreach($evaluation->trainerEvaluations as $trainerEval)
                                                                        <div class="card mb-2 border-warning">
                                                                            <div class="card-body p-2">
                                                                                <div class="d-flex justify-content-between">
                                                                                    <strong>{{ $trainerEval->trainer->name ?? 'مدرب غير معروف' }}</strong>
                                                                                    <span class="badge bg-warning text-dark">{{ $trainerEval->rating ?? 0 }}/5</span>
                                                                                </div>
                                                                                @if($trainerEval->notes)
                                                                                    <p class="mb-0 mt-1 small text-muted"><i class="fas fa-comment"></i> {{ $trainerEval->notes }}</p>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif

                                                            <!-- تقييم التنظيم -->
                                                            @if($evaluation->organization_evaluation && is_array($evaluation->organization_evaluation))
                                                                <div class="mb-4">
                                                                    <h6 class="text-info"><i class="fas fa-cogs me-2"></i>تقييم التنظيم</h6>
                                                                    <div class="row">
                                                                        @php
                                                                            $organizationLabels = [
                                                                                'scheduling' => 'الجدولة',
                                                                                'coordination' => 'التنسيق',
                                                                                'support' => 'الدعم',
                                                                                'communication_admin' => 'التواصل الإداري'
                                                                            ];
                                                                        @endphp
                                                                        @foreach($evaluation->organization_evaluation as $key => $value)
                                                                            <div class="col-md-6 mb-2">
                                                                                <div class="d-flex justify-content-between align-items-center">
                                                                                    <strong>{{ $organizationLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                                                    <span class="badge bg-info">{{ is_numeric($value) ? $value . '/5' : $value }}</span>
                                                                                </div>
                                                                                @if(is_numeric($value))
                                                                                    <div class="progress" style="height: 6px;">
                                                                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $value * 20 }}%" aria-valuenow="{{ $value }}" aria-valuemin="0" aria-valuemax="5"></div>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            <!-- تقييم التأثير -->
                                                            @if($evaluation->impact_evaluation && is_array($evaluation->impact_evaluation))
                                                                <div class="mb-4">
                                                                    <h6 class="text-secondary"><i class="fas fa-chart-line me-2"></i>تقييم التأثير</h6>
                                                                    <div class="row">
                                                                        @php
                                                                            $impactLabels = [
                                                                                'skills_gained' => 'المهارات المكتسبة',
                                                                                'knowledge_gained' => 'المعرفة المكتسبة',
                                                                                'practical_application' => 'التطبيق العملي',
                                                                                'career_impact' => 'التأثير المهني',
                                                                                'overall_satisfaction' => 'الرضا العام'
                                                                            ];
                                                                        @endphp
                                                                        @foreach($evaluation->impact_evaluation as $key => $value)
                                                                            <div class="col-md-6 mb-2">
                                                                                <div class="d-flex justify-content-between align-items-center">
                                                                                    <strong>{{ $impactLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                                                    <span class="badge bg-secondary">{{ is_numeric($value) ? $value . '/5' : $value }}</span>
                                                                                </div>
                                                                                @if(is_numeric($value))
                                                                                    <div class="progress" style="height: 6px;">
                                                                                        <div class="progress-bar bg-secondary" role="progressbar" style="width: {{ $value * 20 }}%" aria-valuenow="{{ $value }}" aria-valuemin="0" aria-valuemax="5"></div>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            <!-- النقاط القوية -->
                                                            @if($evaluation->strengths)
                                                                <div class="mb-3">
                                                                    <h6 class="text-success"><i class="fas fa-plus-circle me-2"></i>النقاط القوية</h6>
                                                                    <div class="alert alert-success">
                                                                        {{ $evaluation->strengths }}
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            <!-- النقاط الضعيفة -->
                                                            @if($evaluation->weaknesses)
                                                                <div class="mb-3">
                                                                    <h6 class="text-danger"><i class="fas fa-minus-circle me-2"></i>النقاط الضعيفة</h6>
                                                                    <div class="alert alert-warning">
                                                                        {{ $evaluation->weaknesses }}
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            <!-- التوصيات -->
                                                            @if($evaluation->recommendations)
                                                                <div class="mb-3">
                                                                    <h6 class="text-info"><i class="fas fa-lightbulb me-2"></i>التوصيات</h6>
                                                                    <div class="alert alert-info">
                                                                        {{ $evaluation->recommendations }}
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            <!-- التعليقات -->
                                                            @if($evaluation->comments)
                                                                <div class="mb-3">
                                                                    <h6><i class="fas fa-comment me-2"></i>التعليقات العامة</h6>
                                                                    <div class="bg-light p-3 rounded">
                                                                        {{ $evaluation->comments }}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">لا توجد تقييمات لهذا التدريب حتى الآن.</p>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-4 border-top pt-3">
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <a href="{{ route("training-coordinator.trainings.edit", $training->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-2"></i>تعديل البرنامج
                            </a>
                            <form action="{{ route("training-coordinator.trainings.destroy", $training->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method("DELETE")
                                <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                    <i class="fas fa-trash me-2"></i>حذف البرنامج
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push("styles")
<style>
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e3e6f0;
    }
    .card-body p {
        margin-bottom: 0.5rem;
    }
    .card-body strong {
        color: #333;
    }
    .accordion-button {
        background-color: #e9ecef;
        color: #495057;
        font-weight: bold;
    }
    .accordion-button:not(.collapsed) {
        color: #0056b3;
        background-color: #cfe2ff;
        box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .125);
    }
    .accordion-body {
        background-color: #f8f9fa;
        border-top: 1px solid #e3e6f0;
    }
    .p-3 {
        padding: 1rem !important;
    }
    .bg-light {
        background-color: #f8f9fa !important;
    }
    .rounded {
        border-radius: 0.375rem !important;
    }
    .border {
        border: 1px solid #dee2e6 !important;
    }
    .h-100 {
        height: 100% !important;
    }
    .border-bottom {
        border-bottom: 1px solid #dee2e6 !important;
    }
</style>
@endpush
