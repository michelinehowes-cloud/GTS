<!-- Modal: استيراد برامج التدريب من ملف إكسل / CSV -->
<div class="modal fade" id="importTrainingsModal" tabindex="-1" aria-labelledby="importTrainingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
            <div class="modal-header text-white px-4 py-3.5" style="background: linear-gradient(135deg, #1565c0 0%, #0d3882 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; font-size: 1.3rem;">
                        <i class="fas fa-file-import"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="importTrainingsModalLabel">استيراد برامج التدريب من ملف إكسل</h5>
                        <p class="small text-white-50 mb-0">رفع جدول البرامج والدورات من ملف CSV أو Excel مباشرة</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>

            <form action="{{ $importRoute }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <!-- تعليمات الأعمدة المقبولة -->
                    <div class="card border-0 shadow-sm rounded-4 p-3 mb-3 bg-white">
                        <h6 class="fw-bold text-primary mb-2 d-flex align-items-center gap-2" style="font-size: 0.88rem;">
                            <i class="fas fa-columns text-info"></i>
                            <span>الأعمدة المدعومة في الملف:</span>
                        </h6>
                        <div class="small text-muted font-monospace bg-light p-2 rounded-3 mb-2" style="font-size: 0.78rem; word-break: break-all;">
                            title, description, type, duration, start_date, end_date, location, seats, status, company_id
                        </div>
                        <ul class="text-secondary small mb-0 ps-3" style="font-size: 0.82rem; line-height: 1.6;">
                            <li>يدعم التواريخ بصيغة: <code>M/D/Y</code> (مثل 10/4/2026) أو <code>Y-m-d</code>.</li>
                            <li>يدعم تصنيف نوع التدريب تلقائياً من العنوان والمدة (ورشة عمل / دورة).</li>
                            <li>يقبل ملفات <code>.csv</code> أو <code>.txt</code> أو <code>.xlsx</code> بحجم حتى 5 ميغابايت.</li>
                        </ul>
                    </div>

                    <!-- منطقة رفع الملف -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark mb-1">
                            <i class="fas fa-cloud-upload-alt text-primary me-1"></i>اختر ملف البيانات:
                        </label>
                        <input type="file" name="csv_file" class="form-control rounded-3 border-secondary-subtle py-2" accept=".csv,.txt,.xlsx,.xls" required>
                    </div>

                    <div class="alert alert-info border-0 rounded-3 py-2 px-3 small text-muted mb-0 d-flex align-items-center gap-2">
                        <i class="fas fa-magic text-primary fs-6"></i>
                        <span>يقوم النظام بقراءة البيانات ومطابقتها وتصنيف التخصصات تلقائياً.</span>
                    </div>
                </div>

                <div class="modal-footer bg-white px-4 py-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-light rounded-3 px-3 py-2 fw-semibold text-secondary" data-bs-dismiss="modal">
                        إلغاء
                    </button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 py-2 fw-bold shadow-sm d-flex align-items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        <span>بدء الاستيراد الآن</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
