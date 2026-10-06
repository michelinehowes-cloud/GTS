{{-- Modal: تصدير تقرير التدريب الشهري --}}
<div class="modal fade" id="exportReportModal" tabindex="-1" aria-labelledby="exportReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-success text-white rounded-top-4 py-3 px-4">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="exportReportModalLabel">
                    <i class="fas fa-file-excel"></i>
                    تصدير تقرير التدريب إلى إكسل
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form method="GET" action="{{ $exportRoute }}">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small">الشهر</label>
                            <select name="month" class="form-select form-select-sm rounded-3">
                                <option value="all">كل الأشهر</option>
                                @foreach(range(1,12) as $m)
                                    <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small">السنة</label>
                            <select name="year" class="form-select form-select-sm rounded-3">
                                <option value="all">كل السنوات</option>
                                @foreach(range(date('Y')-2, date('Y')+1) as $y)
                                    <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small">نوع البرنامج</label>
                            <select name="type" class="form-select form-select-sm rounded-3">
                                <option value="all">الكل</option>
                                <option value="workshop">ورشة عمل</option>
                                <option value="course">دورة تدريبية</option>
                                <option value="internship">تدريب ميداني</option>
                                <option value="seminar">ندوة</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small">نسبة الالتزام الأدنى (%)</label>
                            <input type="number" name="commitment_rate" class="form-control form-control-sm rounded-3"
                                   min="0" max="100" value="75" placeholder="75">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-muted small">صيغة التقرير</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="format" id="formatSummary" value="summary" checked>
                                    <label class="form-check-label small" for="formatSummary">ملخص</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="format" id="formatDetailed" value="detailed">
                                    <label class="form-check-label small" for="formatDetailed">تفصيلي</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success rounded-3 px-4 fw-bold">
                        <i class="fas fa-download me-1"></i> تصدير التقرير
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
