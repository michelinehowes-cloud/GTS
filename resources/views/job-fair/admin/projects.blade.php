@extends('layouts.app')

@section('title', 'إدارة مشاريع التخرج والأرشيف - ' . $fair->title)

@section('content')
<div class="container-fluid py-4" dir="rtl">
    <!-- Header Row -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">
                <i class="fas fa-folder-open text-primary me-2"></i>
                إدارة مشاريع التخرج والأرشيف السنوي
            </h1>
            <p class="text-muted mb-0">
                {{ $fair->title }} — منصة ابتكارات وبحوث تخرج طلبة كليات جامعة طرابلس
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('job-fair.public.projects', $fair->id) }}" target="_blank" class="btn btn-outline-primary shadow-sm">
                <i class="fas fa-external-link-alt"></i> معاينة معرض المشاريع للجمهور
            </a>
            <a href="{{ route('job-fair.admin.projects.export', $fair->id) }}" class="btn btn-outline-success shadow-sm">
                <i class="fas fa-file-excel"></i> تصدير دليل المشاريع (CSV)
            </a>
            <a href="{{ route('job-fair.admin.show', $fair->id) }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-right"></i> العودة لتفاصيل المعرض
            </a>
            <button class="btn btn-success shadow-sm" onclick="showModalSafe('addProjectModal')" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                <i class="fas fa-plus-circle"></i> إضافة مشروع تخرج جديد
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-1"></i> يرجى مراجعة الأخطاء التالية:
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-lg bg-white h-100 border-left-primary">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase mb-1">إجمالي المشاريع المعروضة</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] ?? 0 }}</div>
                    </div>
                    <div class="text-primary display-6"><i class="fas fa-project-diagram"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-lg bg-white h-100 border-left-info">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase mb-1">الكليات المشاركة</div>
                        <div class="h4 mb-0 font-weight-bold text-info">{{ $stats['faculties'] ?? 0 }}</div>
                    </div>
                    <div class="text-info display-6"><i class="fas fa-university"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-lg bg-white h-100 border-left-warning">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase mb-1">المشاريع المميزة</div>
                        <div class="h4 mb-0 font-weight-bold text-warning">{{ $stats['featured'] ?? 0 }}</div>
                    </div>
                    <div class="text-warning display-6"><i class="fas fa-star"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-lg bg-white h-100 border-left-success">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase mb-1">إجمالي المشاهدات</div>
                        <div class="h4 mb-0 font-weight-bold text-success">{{ $stats['views'] ?? 0 }}</div>
                    </div>
                    <div class="text-success display-6"><i class="fas fa-eye"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Table Card -->
    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list-ul me-1"></i> قائمة مشاريع التخرج المسجلة بالمعرض ({{ $projects->count() }})
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="border-0" style="width: 40px;">#</th>
                            <th class="border-0">عنوان المشروع</th>
                            <th class="border-0">الكلية والتخصص</th>
                            <th class="border-0">سنة التخرج</th>
                            <th class="border-0">فريق العمل</th>
                            <th class="border-0">المشرف الأكاديمي</th>
                            <th class="border-0 text-center">الجناح</th>
                            <th class="border-0 text-center">المشاهدات</th>
                            <th class="border-0 text-center" style="width: 160px;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $proj)
                            <tr>
                                <td class="align-middle text-muted">{{ $loop->iteration }}</td>
                                <td class="align-middle">
                                    <h6 class="mb-1 font-weight-bold text-dark">{{ $proj->title }}</h6>
                                    @if($proj->is_featured)
                                        <span class="badge badge-warning text-dark small"><i class="fas fa-star"></i> مميز في الرئيسية</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <div class="font-weight-bold text-primary">
                                        <i class="{{ $proj->faculty_icon }} me-1"></i> {{ $proj->faculty }}
                                    </div>
                                    <small class="text-muted">{{ $proj->department }}</small>
                                </td>
                                <td class="align-middle">
                                    <span class="badge badge-light border">{{ $proj->graduation_year }}</span>
                                </td>
                                <td class="align-middle">
                                    <small class="text-dark d-block">
                                        @php
                                            $names = collect($proj->team_list)->pluck('name')->filter()->implode(' • ');
                                        @endphp
                                        {{ Str::limit($names ?: 'غير محدد', 50) }}
                                    </small>
                                    <small class="text-muted">({{ count($proj->team_list) }} طلاب)</small>
                                </td>
                                <td class="align-middle">
                                    <div class="small font-weight-bold text-dark">{{ $proj->supervisor_name ?? '-' }}</div>
                                    @if($proj->supervisor_title)
                                        <small class="text-muted">{{ $proj->supervisor_title }}</small>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    @if($proj->booth_number)
                                        <span class="badge badge-success px-2 py-1">{{ $proj->booth_number }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <span class="badge badge-secondary px-2 py-1"><i class="fas fa-eye me-1"></i> {{ $proj->views_count }}</span>
                                </td>
                                <td class="align-middle text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <!-- Public Show + QR -->
                                        <a href="{{ route('job-fair.public.projects.show', $proj->id) }}" target="_blank" class="btn btn-outline-secondary" title="معاينة الصفحة العامة ورمز QR">
                                            <i class="fas fa-qrcode"></i>
                                        </a>
                                        <!-- Edit -->
                                        <button type="button" class="btn btn-outline-primary" title="تعديل بيانات المشروع" onclick="editProjectById({{ $proj->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <!-- Delete -->
                                        <form action="{{ route('job-fair.admin.projects.destroy', $proj->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المشروع نهائياً؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="حذف">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 d-block text-gray-300"></i>
                                    لا توجد مشاريع تخرج مسجلة في هذا المعرض حتى الآن.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Project -->
<div class="modal fade" id="addProjectModal" tabindex="-1" role="dialog" aria-labelledby="addProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="{{ route('job-fair.admin.projects.store', $fair->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="addProjectModalLabel">
                        <i class="fas fa-plus-circle me-1"></i> إضافة مشروع تخرج جديد
                    </h5>
                    <button type="button" class="btn-close btn-close-white close text-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 form-group mb-3">
                            <label class="font-weight-bold">عنوان المشروع <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required placeholder="مثال: منصة الرعاية الصحية الذكية بالذكاء الاصطناعي">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label class="font-weight-bold">الكلية <span class="text-danger">*</span></label>
                            <input type="text" name="faculty" class="form-control" required placeholder="مثال: كلية تقنية المعلومات">
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="font-weight-bold">القسم / التخصص <span class="text-danger">*</span></label>
                            <input type="text" name="department" class="form-control" required placeholder="مثال: هندسة البرمجيات">
                        </div>
                        <div class="col-md-2 form-group mb-3">
                            <label class="font-weight-bold">سنة التخرج <span class="text-danger">*</span></label>
                            <input type="number" name="graduation_year" class="form-control" required value="2026">
                        </div>
                        <div class="col-md-2 form-group mb-3">
                            <label class="font-weight-bold">رقم الجناح</label>
                            <input type="text" name="booth_number" class="form-control" placeholder="مثال: IT-01">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">المشرف الأكاديمي</label>
                            <input type="text" name="supervisor_name" class="form-control" placeholder="اسم الأستاذ المشرف">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">اللقب والصفة الأكاديمية</label>
                            <input type="text" name="supervisor_title" class="form-control" placeholder="مثال: أستاذ مشارك - قسم هندسة البرمجيات">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">فريق العمل (ضع اسم كل طالب في سطر مستقل)</label>
                        <textarea name="team_members_raw" class="form-control" rows="3" placeholder="محمد علي الورفلي&#10;سارة عبد الله الزنتاني&#10;عمر عبد الباسط الطاهر"></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">نبذة تعريفية مختصرة (Abstract)</label>
                        <textarea name="summary" class="form-control" rows="2" placeholder="ملخص الفكرة والمشكلة التي يعالجها المشروع..."></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">أهداف المشروع (ضع كل هدف في سطر)</label>
                        <textarea name="objectives" class="form-control" rows="2" placeholder="الهدف الأول&#10;الهدف الثاني"></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">التفاصيل الفنية والمخرجات والمواصفات</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="التقنيات المستخدمة والنتائج العملية..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">بوستر المشروع (Poster Image)</label>
                            <input type="file" name="poster_image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">صورة الغلاف / الواجهة (Cover)</label>
                            <input type="file" name="cover_image" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">رابط المشروع البرمجي أو GitHub</label>
                            <input type="url" name="project_url" class="form-control" placeholder="https://github.com/...">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">رابط فيديو العرض (Demo / YouTube)</label>
                            <input type="url" name="video_url" class="form-control" placeholder="https://youtube.com/...">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">حالة النشر</label>
                            <select name="status" class="form-control">
                                <option value="published">منشور ومتاح للجمهور</option>
                                <option value="draft">مسودة (غير منشور)</option>
                                <option value="archived">مؤرشف</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-3 d-flex align-items-center pt-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_featured" value="1" class="form-check-input" id="addProjFeatured">
                                <label class="form-check-label font-weight-bold text-warning" for="addProjFeatured">
                                    <i class="fas fa-star"></i> تمييز في الصفحة الرئيسية للمعرض
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ المشروع</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Project -->
<div class="modal fade" id="editProjectModal" tabindex="-1" role="dialog" aria-labelledby="editProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="editProjectForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title font-weight-bold" id="editProjectModalLabel">
                        <i class="fas fa-edit me-1"></i> تعديل بيانات مشروع التخرج
                    </h5>
                    <button type="button" class="btn-close btn-close-white close text-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 form-group mb-3">
                            <label class="font-weight-bold">عنوان المشروع <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="edit_title" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label class="font-weight-bold">الكلية <span class="text-danger">*</span></label>
                            <input type="text" name="faculty" id="edit_faculty" class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="font-weight-bold">القسم / التخصص <span class="text-danger">*</span></label>
                            <input type="text" name="department" id="edit_department" class="form-control" required>
                        </div>
                        <div class="col-md-2 form-group mb-3">
                            <label class="font-weight-bold">سنة التخرج <span class="text-danger">*</span></label>
                            <input type="number" name="graduation_year" id="edit_graduation_year" class="form-control" required>
                        </div>
                        <div class="col-md-2 form-group mb-3">
                            <label class="font-weight-bold">رقم الجناح</label>
                            <input type="text" name="booth_number" id="edit_booth_number" class="form-control">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">المشرف الأكاديمي</label>
                            <input type="text" name="supervisor_name" id="edit_supervisor_name" class="form-control">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">اللقب والصفة الأكاديمية</label>
                            <input type="text" name="supervisor_title" id="edit_supervisor_title" class="form-control">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">فريق العمل (اسم كل طالب في سطر)</label>
                        <textarea name="team_members_raw" id="edit_team_members_raw" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">نبذة تعريفية مختصرة (Abstract)</label>
                        <textarea name="summary" id="edit_summary" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">أهداف المشروع</label>
                        <textarea name="objectives" id="edit_objectives" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">التفاصيل الفنية والمخرجات والمواصفات</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">تحديث بوستر المشروع</label>
                            <input type="file" name="poster_image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">تحديث صورة الغلاف</label>
                            <input type="file" name="cover_image" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">رابط المشروع البرمجي أو GitHub</label>
                            <input type="url" name="project_url" id="edit_project_url" class="form-control">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">رابط فيديو العرض (Demo / YouTube)</label>
                            <input type="url" name="video_url" id="edit_video_url" class="form-control">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">حالة النشر</label>
                            <select name="status" id="edit_status" class="form-control">
                                <option value="published">منشور ومتاح للجمهور</option>
                                <option value="draft">مسودة (غير منشور)</option>
                                <option value="archived">مؤرشف</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-3 d-flex align-items-center pt-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_featured" value="1" class="form-check-input" id="editProjFeatured">
                                <label class="form-check-label font-weight-bold text-warning" for="editProjFeatured">
                                    <i class="fas fa-star"></i> تمييز في الصفحة الرئيسية للمعرض
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const fairProjects = @json($projects->keyBy('id'));

    function showModalSafe(modalId) {
        const modalEl = document.getElementById(modalId);
        if (!modalEl) return;
        if (window.bootstrap && window.bootstrap.Modal) {
            const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
            bsModal.show();
            return;
        }
        if (window.jQuery && typeof $(modalEl).modal === 'function') {
            $(modalEl).modal('show');
            return;
        }
        // Vanilla fallback
        modalEl.style.display = 'block';
        modalEl.classList.add('show');
        modalEl.removeAttribute('aria-hidden');
        let backdrop = document.getElementById(modalId + '-backdrop');
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = modalId + '-backdrop';
            backdrop.onclick = function() { hideModalSafe(modalId); };
            document.body.appendChild(backdrop);
        }
        document.body.classList.add('modal-open');
    }

    function hideModalSafe(modalId) {
        const modalEl = document.getElementById(modalId);
        if (!modalEl) return;
        if (window.bootstrap && window.bootstrap.Modal) {
            const bsModal = bootstrap.Modal.getInstance(modalEl);
            if (bsModal) { bsModal.hide(); return; }
        }
        if (window.jQuery && typeof $(modalEl).modal === 'function') {
            $(modalEl).modal('hide');
            return;
        }
        modalEl.style.display = 'none';
        modalEl.classList.remove('show');
        modalEl.setAttribute('aria-hidden', 'true');
        const backdrop = document.getElementById(modalId + '-backdrop');
        if (backdrop) backdrop.remove();
        document.body.classList.remove('modal-open');
    }

    // Auto bind close buttons
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('[data-bs-dismiss="modal"], [data-dismiss="modal"]').forEach(btn => {
            btn.addEventListener('click', function() {
                const modal = this.closest('.modal');
                if (modal) hideModalSafe(modal.id);
            });
        });
    });

    function editProjectById(id) {
        const proj = fairProjects[id];
        if (!proj) {
            console.error('Project not found with ID:', id);
            return;
        }

        document.getElementById('editProjectForm').action = "/admin/job-fair/projects/" + proj.id;
        document.getElementById('edit_title').value = proj.title || '';
        document.getElementById('edit_faculty').value = proj.faculty || '';
        document.getElementById('edit_department').value = proj.department || '';
        document.getElementById('edit_graduation_year').value = proj.graduation_year || 2026;
        document.getElementById('edit_booth_number').value = proj.booth_number || '';
        document.getElementById('edit_supervisor_name').value = proj.supervisor_name || '';
        document.getElementById('edit_supervisor_title').value = proj.supervisor_title || '';
        document.getElementById('edit_summary').value = proj.summary || '';
        document.getElementById('edit_objectives').value = proj.objectives || '';
        document.getElementById('edit_description').value = proj.description || '';
        document.getElementById('edit_project_url').value = proj.project_url || '';
        document.getElementById('edit_video_url').value = proj.video_url || '';
        document.getElementById('edit_status').value = proj.status || 'published';
        document.getElementById('editProjFeatured').checked = Boolean(proj.is_featured);

        // Format team members to lines
        let teamLines = '';
        if (Array.isArray(proj.team_members)) {
            teamLines = proj.team_members.map(m => m.name || m).filter(Boolean).join('\n');
        } else if (typeof proj.team_members === 'string') {
            try {
                const parsed = JSON.parse(proj.team_members);
                if (Array.isArray(parsed)) {
                    teamLines = parsed.map(m => m.name || m).filter(Boolean).join('\n');
                } else {
                    teamLines = proj.team_members;
                }
            } catch(e) {
                teamLines = proj.team_members;
            }
        }
        document.getElementById('edit_team_members_raw').value = teamLines;

        showModalSafe('editProjectModal');
    }
</script>
@endpush
@endsection
