@extends('layouts.app')

@section('title', 'تفاصيل التغطية الإعلامية: ' . $training->title)
@section('page-title', 'تفاصيل التغطية الإعلامية للتدريب')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3" dir="rtl">

    <div class="row g-4">
        <!-- معلومات التدريب الأساسية -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 rounded-4 shadow-sm h-100" style="background: #ffffff;">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="fas fa-info-circle text-primary"></i>
                        <span>بيانات المحطة التدريبية</span>
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3" onclick="updateCoverageStatus({{ $training->id }}, '{{ $training->media_coverage_status }}')">
                            <i class="fas fa-edit me-1"></i>تحديث حالة التغطية
                        </button>
                        <a href="{{ route('media.trainings.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            <i class="fas fa-arrow-right me-1"></i>العودة
                        </a>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h4 class="fw-bold text-dark mb-2" style="color: #0d3882 !important;">{{ $training->title }}</h4>
                            <p class="text-muted small mb-3">{{ $training->description ?: 'لا يوجد وصف تفصيلي مسجل.' }}</p>

                            <div class="mb-2 small">
                                <strong class="text-secondary">النوع:</strong>
                                <span class="text-dark fw-semibold">{{ $training->getTypeArabicAttribute() }}</span>
                            </div>
                            <div class="mb-2 small">
                                <strong class="text-secondary">المدة:</strong>
                                <span class="text-dark fw-semibold">{{ $training->duration ?: '—' }}</span>
                            </div>
                            <div class="mb-2 small">
                                <strong class="text-secondary">عدد المقاعد:</strong>
                                <span class="text-dark fw-semibold">{{ $training->seats ?: '—' }}</span>
                            </div>
                            <div class="mb-2 small">
                                <strong class="text-secondary">الحالة:</strong>
                                <span class="badge bg-{{ $training->status == 'active' ? 'success' : ($training->status == 'completed' ? 'info' : 'secondary') }} rounded-pill px-2.5 py-1">
                                    {{ $training->getStatusArabicAttribute() }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2 small">
                                <strong class="text-secondary">تاريخ البدء:</strong>
                                <span class="text-dark fw-semibold">{{ $training->start_date ? $training->start_date->format('Y/m/d') : '—' }}</span>
                            </div>
                            <div class="mb-2 small">
                                <strong class="text-secondary">تاريخ الانتهاء:</strong>
                                <span class="text-dark fw-semibold">{{ $training->end_date ? $training->end_date->format('Y/m/d') : '—' }}</span>
                            </div>
                            <div class="mb-2 small">
                                <strong class="text-secondary">الموقع:</strong>
                                <span class="text-dark fw-semibold">{{ $training->location ?: 'جامعة طرابلس' }}</span>
                            </div>
                            <div class="mb-2 small">
                                <strong class="text-secondary">المنسق:</strong>
                                <span class="text-dark fw-semibold">{{ $training->coordinator ? $training->coordinator->name : 'مكتب تدريب الخريجين' }}</span>
                            </div>
                            @if($training->company)
                            <div class="mb-2 small">
                                <strong class="text-secondary">الجهة الشريكة:</strong>
                                <span class="text-primary fw-bold">{{ $training->company->name }}</span>
                            </div>
                            @endif
                            <div class="mt-3">
                                <strong class="text-secondary d-block mb-1 small">حالة التغطية الإعلامية:</strong>
                                <span class="badge bg-{{ $training->media_coverage_status == 'covered' ? 'success' : ($training->media_coverage_status == 'pending' ? 'warning text-dark' : 'secondary') }} rounded-pill px-3 py-1.5 fw-bold">
                                    {{ $training->getMediaCoverageStatusText() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- بطاقة التقرير الصحفي والإجراءات -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 rounded-4 shadow-sm h-100" style="background: #ffffff;">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="fas fa-newspaper text-primary"></i>
                        <span>التقرير والتوثيق الصحفي</span>
                    </h6>
                </div>
                <div class="card-body px-4 pb-4 text-center d-flex flex-column justify-content-between">
                    <div>
                        <div class="stat-circle rounded-circle bg-primary bg-opacity-10 text-primary mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <i class="fas fa-feather-alt fa-2x"></i>
                        </div>
                        @if($training->media_press_release || $training->media_coverage_summary)
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1.5 fw-bold border border-success border-opacity-25 mb-2">
                                <i class="fas fa-check me-1"></i>تم تحرير البيان الصحفي
                            </span>
                            <p class="text-muted small mb-3">التقرير الصحفي والبيان الرسمي جاهزان ومعتمدان للطباعة والنشر.</p>
                        @else
                            <span class="badge bg-warning bg-opacity-15 text-dark rounded-pill px-3 py-1.5 fw-bold border border-warning border-opacity-25 mb-2">
                                <i class="fas fa-hourglass-half me-1"></i>بانتظار التحرير الصحفي
                            </span>
                            <p class="text-muted small mb-3">لم يتم تحرير البيان الصحفي أو توثيق الروابط الخارجية لهذا التدريب بعد.</p>
                        @endif
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('media.reports.coverage.edit', $training) }}" class="btn btn-warning text-dark fw-bold py-2.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="fas fa-pen"></i>
                            <span>تحرير وصياغة التقرير</span>
                        </a>
                        <a href="{{ route('media.reports.coverage.show', $training) }}" class="btn btn-outline-primary py-2.5 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2">
                            <i class="fas fa-file-invoice"></i>
                            <span>معاينة وطباعة التقرير الرسمي</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- التوثيق والبيان الصحفي وروابط السوشيال ميديا -->
    <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: #ffffff;">
        <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="fas fa-file-alt text-primary"></i>
                <span>تفاصيل البيان الصحفي والتوثيق الميداني</span>
            </h5>
            <a href="{{ route('media.reports.coverage.edit', $training) }}" class="btn btn-sm btn-outline-warning text-dark fw-bold rounded-pill px-3">
                <i class="fas fa-edit me-1"></i>تعديل المحتوى
            </a>
        </div>
        <div class="card-body p-4">
            @if($training->media_press_release)
                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                        <i class="fas fa-newspaper text-primary"></i>
                        <span>نص البيان الصحفي الرسمي المعتمد:</span>
                    </h6>
                    <div class="p-3.5 rounded-4 bg-light text-dark border" style="white-space: pre-line; line-height: 1.8; font-size: 0.95rem;">
                        {{ $training->media_press_release }}
                    </div>
                </div>
            @endif

            @if($training->media_coverage_summary)
                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                        <i class="fas fa-file-signature text-success"></i>
                        <span>وقائع وتفاصيل التغطية الميدانية:</span>
                    </h6>
                    <div class="p-3.5 rounded-4 bg-light text-secondary border" style="white-space: pre-line; line-height: 1.7; font-size: 0.92rem;">
                        {{ $training->media_coverage_summary }}
                    </div>
                </div>
            @endif

            @if($training->media_coverage_links)
                <div class="mb-3">
                    <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                        <i class="fas fa-cloud text-primary"></i>
                        <span>روابط التغطية السحابية، مشاركة الملفات، والنشر الخارجي:</span>
                    </h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach(explode("\n", str_replace("\r", "", $training->media_coverage_links)) as $link)
                            @php $cleanLink = trim($link); @endphp
                            @if(!empty($cleanLink))
                                <a href="{{ $cleanLink }}" target="_blank" class="btn btn-sm btn-light border rounded-pill px-3 text-primary d-inline-flex align-items-center gap-1.5" style="direction: ltr; font-size: 0.8rem;">
                                    <i class="fas fa-external-link-alt"></i>
                                    <span>{{ \Illuminate\Support\Str::limit($cleanLink, 50) }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            @if(!$training->media_press_release && !$training->media_coverage_summary && !$training->media_coverage_links)
                <div class="text-center py-5">
                    <i class="fas fa-feather-alt fa-3x text-muted opacity-50 mb-3"></i>
                    <h5 class="fw-bold text-dark mb-2">لا يوجد توثيق صحفي مسجل لهذا التدريب</h5>
                    <p class="text-muted small mb-3">يمكنك صياغة البيان الصحفي الرسمي وتوثيق روابط التخزين السحابي الآن.</p>
                    <a href="{{ route('media.reports.coverage.edit', $training) }}" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="fas fa-pen me-1"></i>كتابة وصياغة التقرير الآن
                    </a>
                </div>
            @endif
        </div>
    </div>

</div>

<!-- نافذة تحديث حالة التغطية -->
<div class="modal fade" id="coverageStatusModal" tabindex="-1" dir="rtl">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">تحديث حالة التغطية الإعلامية</h5>
                <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal"></button>
            </div>
            <form id="coverageStatusForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">حالة التغطية</label>
                        <select name="media_coverage_status" class="form-select rounded-3 p-2.5" id="coverageStatusSelect" required>
                            <option value="pending">⏳ تحت التغطية / قيد المتابعة</option>
                            <option value="covered">✅ تمت التغطية والتوثيق</option>
                            <option value="not_required">⚪ لا يتطلب تغطية</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateCoverageStatus(trainingId, currentStatus) {
    document.getElementById('coverageStatusSelect').value = currentStatus;
    document.getElementById('coverageStatusForm').action = `/media/trainings/${trainingId}/coverage-status`;

    new bootstrap.Modal(document.getElementById('coverageStatusModal')).show();
}
</script>
@endpush
