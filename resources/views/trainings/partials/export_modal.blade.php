<!-- Modal: تصدير تقرير إكسل لبرامج وورش العمل -->
<div class="modal fade" id="exportReportModal" tabindex="-1" aria-labelledby="exportReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
            <!-- ترويسة النافذة -->
            <div class="modal-header text-white px-4 py-3.5" style="background: linear-gradient(135deg, #0d3882 0%, #1e5bb8 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-success rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; font-size: 1.3rem;">
                        <i class="fas fa-file-excel"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="exportReportModalLabel">تصدير تقرير إكسل لبرامج وورش العمل</h5>
                        <p class="small text-white-50 mb-0">استخراج كشف التدريبات والمدربين ونسب الحضور والطلبة الملتزمين</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>

            <!-- محتوى النافذة -->
            <div class="modal-body p-4 bg-light">
                <!-- خيارات الفلترة والتخصيص -->
                <div class="card border-0 shadow-sm rounded-4 p-3.5 mb-3 bg-white">
                    <h6 class="fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                        <i class="fas fa-sliders-h"></i>
                        <span>معايير وفلاتر التقرير</span>
                    </h6>

                    <div class="row g-3">
                        <!-- اختيار الشهر -->
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label small fw-bold text-secondary mb-1">
                                <i class="far fa-calendar-alt me-1 text-primary"></i>الشهر المستهدف:
                            </label>
                            <select id="exportMonthSelect" class="form-select form-select-sm rounded-3 border-secondary-subtle fw-semibold">
                                <option value="9" selected>شهر 9 - سبتمبر (الحالي)</option>
                                <option value="1">شهر 1 - يناير</option>
                                <option value="2">شهر 2 - فبراير</option>
                                <option value="3">شهر 3 - مارس</option>
                                <option value="4">شهر 4 - أبريل</option>
                                <option value="5">شهر 5 - مايو</option>
                                <option value="6">شهر 6 - يونيو</option>
                                <option value="7">شهر 7 - يوليو</option>
                                <option value="8">شهر 8 - أغسطس</option>
                                <option value="10">شهر 10 - أكتوبر</option>
                                <option value="11">شهر 11 - نوفمبر</option>
                                <option value="12">شهر 12 - ديسمبر</option>
                                <option value="all">جميع أشهر السنة</option>
                            </select>
                        </div>

                        <!-- اختيار السنة -->
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label small fw-bold text-secondary mb-1">
                                <i class="far fa-calendar-check me-1 text-primary"></i>السنة:
                            </label>
                            <select id="exportYearSelect" class="form-select form-select-sm rounded-3 border-secondary-subtle fw-semibold">
                                <option value="2026" selected>2026</option>
                                <option value="2025">2025</option>
                                <option value="2024">2024</option>
                                <option value="all">جميع السنوات</option>
                            </select>
                        </div>

                        <!-- نوع البرنامج -->
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label small fw-bold text-secondary mb-1">
                                <i class="fas fa-shapes me-1 text-primary"></i>نوع النشاط:
                            </label>
                            <select id="exportTypeSelect" class="form-select form-select-sm rounded-3 border-secondary-subtle fw-semibold">
                                <option value="all" selected>الكل (ورش ودورات)</option>
                                <option value="workshop">ورش عمل فقط</option>
                                <option value="course">دورات تدريبية فقط</option>
                                <option value="internship">تدريب عملي / تعاوني</option>
                            </select>
                        </div>

                        <!-- معيار نسبة الالتزام -->
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label small fw-bold text-secondary mb-1">
                                <i class="fas fa-user-check me-1 text-primary"></i>حد الالتزام المطلوب:
                            </label>
                            <select id="exportCommitmentSelect" class="form-select form-select-sm rounded-3 border-secondary-subtle fw-semibold">
                                <option value="75" selected>حضور 75% فأكثر (معتمد)</option>
                                <option value="80">حضور 80% فأكثر (ممتاز)</option>
                                <option value="100">حضور 100% (كامل)</option>
                                <option value="50">حضور 50% فأكثر (أدنى)</option>
                            </select>
                        </div>

                        <!-- صيغة التقرير -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-secondary mb-1">
                                <i class="fas fa-table me-1 text-primary"></i>هيكل ملف الإكسل (الصيغة):
                            </label>
                            <div class="d-flex gap-3 flex-wrap">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="exportFormatRadio" id="formatSummary" value="summary" checked>
                                    <label class="form-check-label fw-semibold small" for="formatSummary">
                                        <strong>ملخص التدريبات وورش العمل</strong> (يشمل اسم التدريب، المدرب، التاريخ، عدد الحضور، عدد وقائمة أسماء الملتزمين)
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="exportFormatRadio" id="formatDetailed" value="detailed">
                                    <label class="form-check-label fw-semibold small" for="formatDetailed">
                                        <strong>كشف تفصيلي بالمتدربين</strong> (سطر لكل طالب يوضح الكلية والتخصص وأيام الحضور ونسبة الالتزام الفردية)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- شريط المعاينة والإحصائيات السريعة -->
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-2.5">
                        <span class="small fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="fas fa-chart-pie text-success"></i>
                            <span>معاينة فورية للبيانات المشمولة</span>
                        </span>
                        <button type="button" id="btnLivePreview" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1">
                            <i class="fas fa-sync-alt me-1"></i>تحديث المعاينة
                        </button>
                    </div>

                    <!-- إحصائيات سريعة للمعاينة -->
                    <div class="row g-2 mb-3" id="previewStatsRow">
                        <div class="col-4">
                            <div class="p-2.5 rounded-3 bg-primary bg-opacity-10 text-center">
                                <span class="d-block small text-muted">عدد التدريبات</span>
                                <strong class="fs-5 text-primary" id="previewTrainingsCount">—</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2.5 rounded-3 bg-info bg-opacity-10 text-center">
                                <span class="d-block small text-muted">إجمالي الحضور</span>
                                <strong class="fs-5 text-info" id="previewAttendeesCount">—</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2.5 rounded-3 bg-success bg-opacity-10 text-center">
                                <span class="d-block small text-muted">الطلبة الملتزمون</span>
                                <strong class="fs-5 text-success" id="previewCommittedCount">—</strong>
                            </div>
                        </div>
                    </div>

                    <!-- جدول المعاينة السريعة المصغر -->
                    <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                        <table class="table table-sm table-hover align-middle mb-0 font-monospace" style="font-size: 0.82rem;">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="py-1 px-2">التدريب / الورشة</th>
                                    <th class="py-1 px-2">المدرب</th>
                                    <th class="py-1 px-2">التاريخ</th>
                                    <th class="py-1 px-2 text-center">الحضور</th>
                                    <th class="py-1 px-2 text-center">الملتزمون</th>
                                </tr>
                            </thead>
                            <tbody id="previewTableBody">
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">
                                        <i class="fas fa-spinner fa-spin me-1 text-primary"></i> جاري استعراض بيانات الشهر...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- تلميح توضيحي -->
                <div class="alert alert-light border border-secondary-subtle rounded-3 py-2 px-3 small text-muted mb-0 d-flex align-items-center gap-2">
                    <i class="fas fa-info-circle text-primary fs-6"></i>
                    <span>الملف الصادر متوافق كلياً مع مايكروسوفت إكسل (UTF-8 BOM) ويفتح اللغة العربية تلقائياً بشكل سليم ومباشر بدون مشاكل ترميز.</span>
                </div>
            </div>

            <!-- تذييل وأزرار التحميل -->
            <div class="modal-footer bg-white px-4 py-3 d-flex justify-content-between">
                <button type="button" class="btn btn-light rounded-3 px-3 py-2 fw-semibold text-secondary" data-bs-dismiss="modal">
                    إلغاء
                </button>
                <a id="btnDownloadExcel" href="#" class="btn btn-success rounded-3 px-4 py-2 fw-bold text-white shadow-sm d-flex align-items-center gap-2" style="font-size: 0.95rem;">
                    <i class="fas fa-file-excel fs-5"></i>
                    <span>تحميل ملف Excel الآن</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const exportBaseUrl = "{{ $exportRoute }}";
    const monthSelect = document.getElementById('exportMonthSelect');
    const yearSelect = document.getElementById('exportYearSelect');
    const typeSelect = document.getElementById('exportTypeSelect');
    const commitmentSelect = document.getElementById('exportCommitmentSelect');
    const downloadBtn = document.getElementById('btnDownloadExcel');
    const previewBtn = document.getElementById('btnLivePreview');
    const modalEl = document.getElementById('exportReportModal');

    function getSelectedFormat() {
        const checked = document.querySelector('input[name="exportFormatRadio"]:checked');
        return checked ? checked.value : 'summary';
    }

    function updateDownloadLink() {
        const params = new URLSearchParams({
            month: monthSelect.value,
            year: yearSelect.value,
            type: typeSelect.value,
            commitment_rate: commitmentSelect.value,
            format: getSelectedFormat()
        });
        downloadBtn.href = `${exportBaseUrl}?${params.toString()}`;
    }

    function fetchPreview() {
        const tableBody = document.getElementById('previewTableBody');
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-muted py-3">
                    <span class="spinner-border spinner-border-sm text-primary me-1" role="status"></span>
                    جاري تحميل ومعالجة البيانات...
                </td>
            </tr>
        `;

        const params = new URLSearchParams({
            month: monthSelect.value,
            year: yearSelect.value,
            type: typeSelect.value,
            commitment_rate: commitmentSelect.value,
            preview: '1'
        });

        fetch(`${exportBaseUrl}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('previewTrainingsCount').textContent = data.trainings_count;
                document.getElementById('previewAttendeesCount').textContent = data.total_attendees;
                document.getElementById('previewCommittedCount').textContent = data.total_committed;

                if (data.data.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">
                                لا توجد تدريبات أو ورش عمل مسجلة وفق المعايير المحددة.
                            </td>
                        </tr>
                    `;
                } else {
                    let rowsHtml = '';
                    data.data.forEach(item => {
                        rowsHtml += `
                            <tr>
                                <td class="fw-bold text-dark py-2 px-2" style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${item.title}">
                                    <span class="badge bg-secondary-subtle text-dark me-1" style="font-size: 0.72rem;">${item.type_label}</span>
                                    ${item.title}
                                </td>
                                <td class="py-2 px-2 text-secondary text-nowrap">${item.instructor_name}</td>
                                <td class="py-2 px-2 text-nowrap small text-muted">${item.start_date}</td>
                                <td class="py-2 px-2 text-center">
                                    <span class="badge bg-info bg-opacity-10 text-info fw-bold">${item.attendees_count}</span>
                                </td>
                                <td class="py-2 px-2 text-center">
                                    <span class="badge bg-success bg-opacity-10 text-success fw-bold">${item.committed_count}</span>
                                </td>
                            </tr>
                        `;
                    });
                    tableBody.innerHTML = rowsHtml;
                }
            }
        })
        .catch(err => {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-danger py-3">
                        <i class="fas fa-exclamation-triangle me-1"></i> تعذر جلب المعاينة. يرجى المحاولة لاحقاً.
                    </td>
                </tr>
            `;
        });
    }

    // ربط الأحداث
    [monthSelect, yearSelect, typeSelect, commitmentSelect].forEach(el => {
        el.addEventListener('change', () => {
            updateDownloadLink();
            fetchPreview();
        });
    });

    document.querySelectorAll('input[name="exportFormatRadio"]').forEach(el => {
        el.addEventListener('change', updateDownloadLink);
    });

    if (previewBtn) {
        previewBtn.addEventListener('click', fetchPreview);
    }

    if (modalEl) {
        modalEl.addEventListener('shown.bs.modal', function () {
            updateDownloadLink();
            fetchPreview();
        });
    }

    // تهيئة الرابط عند التحميل
    updateDownloadLink();
});
</script>
