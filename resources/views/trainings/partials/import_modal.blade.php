{{-- Modal: استيراد برامج التدريب من ملف CSV/Excel --}}
<div class="modal fade" id="importTrainingsModal" tabindex="-1" aria-labelledby="importTrainingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white rounded-top-4 py-3 px-4">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="importTrainingsModalLabel">
                    <i class="fas fa-file-import"></i>
                    استيراد برامج التدريب من إكسل / CSV
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form method="POST" action="{{ $importRoute }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 rounded-3 small py-2 px-3 mb-3 d-flex gap-2 align-items-start">
                        <i class="fas fa-info-circle mt-1 flex-shrink-0"></i>
                        <div>
                            يجب أن يكون الملف بصيغة <strong>CSV</strong> بترميز <strong>UTF-8</strong>.
                            الأعمدة المطلوبة: <code>title, type, start_date, end_date, seats, location, description</code>.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="csv_file" class="form-label fw-semibold text-muted small">اختر الملف</label>
                        <input type="file" name="csv_file" id="csv_file"
                               class="form-control form-control-sm rounded-3 @error('csv_file') is-invalid @enderror"
                               accept=".csv,.xlsx,.xls">
                        @error('csv_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text small text-muted mt-1">
                            الحد الأقصى للحجم: 5 ميجابايت — الصيغ المقبولة: CSV, XLSX
                        </div>
                    </div>

                    {{-- رابط تحميل قالب الاستيراد --}}
                    @if(isset($templateRoute) || Route::has('training-coordinator.reports.download-template') || Route::has('admin.reports.download-template'))
                    <div class="text-center mt-2">
                        @php
                            $tmplRoute = isset($templateRoute) ? $templateRoute
                                : (Route::has('training-coordinator.reports.download-template')
                                    ? route('training-coordinator.reports.download-template', 'trainings')
                                    : (Route::has('admin.reports.download-template')
                                        ? route('admin.reports.download-template', 'trainings')
                                        : null));
                        @endphp
                        @if($tmplRoute)
                        <a href="{{ $tmplRoute }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                            <i class="fas fa-download me-1"></i>
                            تحميل قالب CSV فارغ
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-bold">
                        <i class="fas fa-upload me-1"></i> استيراد البيانات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
