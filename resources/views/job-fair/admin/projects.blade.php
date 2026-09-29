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
            <form action="{{ route('job-fair.admin.toggle-feature', $fair->id) }}" method="POST" class="d-inline m-0">
                @csrf
                <input type="hidden" name="feature" value="projects">
                <button type="submit" class="btn {{ $fair->is_projects_published ? 'btn-success' : 'btn-warning text-dark' }} shadow-sm fw-bold d-inline-flex align-items-center gap-1.5" title="انقر للتبديل بين إظهار المشاريع للجمهور أو إخفائها كـ Coming Soon">
                    <i class="fas {{ $fair->is_projects_published ? 'fa-eye' : 'fa-clock' }}"></i>
                    <span>{{ $fair->is_projects_published ? 'المشاريع منشورة ومتاحة للجمهور' : 'المشاريع قيد التحضير (Coming Soon)' }}</span>
                </button>
            </form>
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
        <form id="addProjectForm" action="{{ route('job-fair.admin.projects.store', $fair->id) }}" method="POST" enctype="multipart/form-data">
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
                            <label class="font-weight-bold d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-image text-primary me-1"></i> بوستر المشروع (Poster)</span>
                                <small class="text-success"><i class="fas fa-bolt me-1"></i>ضغط ذكي فوري</small>
                            </label>
                            <input type="file" name="poster_image" id="add_poster_image" class="form-control" accept="image/*">
                            <div id="add_poster_preview" class="mt-2 d-none"></div>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-camera text-primary me-1"></i> صورة الغلاف / الواجهة (Cover)</span>
                                <small class="text-success"><i class="fas fa-bolt me-1"></i>ضغط ذكي فوري</small>
                            </label>
                            <input type="file" name="cover_image" id="add_cover_image" class="form-control" accept="image/*">
                            <div id="add_cover_preview" class="mt-2 d-none"></div>
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

                <div id="add_upload_progress" class="px-3 py-2 d-none bg-light border-top">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small font-weight-bold text-primary">
                            <i class="fas fa-spinner fa-spin me-1"></i> جاري حفظ المشروع ورفع الصور...
                        </span>
                        <span class="small text-muted" id="add_progress_text">يرجى الانتظار لحظات</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">إلغاء</button>
                    <button type="submit" id="addProjectSubmitBtn" class="btn btn-primary fw-bold">
                        <i class="fas fa-save me-1"></i> حفظ المشروع
                    </button>
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
                            <label class="font-weight-bold d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-image text-primary me-1"></i> تحديث بوستر المشروع</span>
                                <small class="text-success"><i class="fas fa-bolt me-1"></i>ضغط ذكي فوري</small>
                            </label>
                            <input type="file" name="poster_image" id="edit_poster_image" class="form-control" accept="image/*">
                            <div id="edit_poster_preview" class="mt-2 d-none"></div>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-camera text-primary me-1"></i> تحديث صورة الغلاف</span>
                                <small class="text-success"><i class="fas fa-bolt me-1"></i>ضغط ذكي فوري</small>
                            </label>
                            <input type="file" name="cover_image" id="edit_cover_image" class="form-control" accept="image/*">
                            <div id="edit_cover_preview" class="mt-2 d-none"></div>
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

                <div id="edit_upload_progress" class="px-3 py-2 d-none bg-light border-top">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small font-weight-bold text-primary">
                            <i class="fas fa-spinner fa-spin me-1"></i> جاري حفظ التعديلات ورفع الصور...
                        </span>
                        <span class="small text-muted" id="edit_progress_text">يرجى الانتظار لحظات</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">إلغاء</button>
                    <button type="submit" id="editProjectSubmitBtn" class="btn btn-primary fw-bold">
                        <i class="fas fa-save me-1"></i> حفظ التعديلات
                    </button>
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

    // تنسيق الحجم بصيغة مقروءة (KB / MB)
    function formatBytes(bytes, decimals = 1) {
        if (!bytes || bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    // ضغط ذكي فوري للصور باستخدام HTML5 Canvas قبل الرفع لتسريع الحفظ 10 أضعاف
    async function compressImageFile(file, maxWidth = 1920, maxHeight = 1920, quality = 0.85) {
        if (!file || !file.type.startsWith('image/')) return file;
        if (file.type === 'image/svg+xml' || file.type === 'image/gif') return file;
        if (file.size < 350 * 1024) return file; // إذا كان حجمها أقل من 350 كيلوبايت لا تحتاج لضغط

        return new Promise((resolve) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    let width = img.width;
                    let height = img.height;

                    if (width > maxWidth || height > maxHeight) {
                        if (width / height > maxWidth / maxHeight) {
                            height = Math.round((height * maxWidth) / width);
                            width = maxWidth;
                        } else {
                            width = Math.round((width * maxHeight) / height);
                            height = maxHeight;
                        }
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    canvas.toBlob((blob) => {
                        if (!blob || blob.size >= file.size) {
                            resolve(file); // إذا لم ينخفض الحجم نبقي الأصل
                        } else {
                            const newName = file.name.replace(/\.[^/.]+$/, "") + ".jpg";
                            const compressed = new File([blob], newName, {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            });
                            resolve(compressed);
                        }
                    }, 'image/jpeg', quality);
                };
                img.onerror = () => resolve(file);
                img.src = e.target.result;
            };
            reader.onerror = () => resolve(file);
            reader.readAsDataURL(file);
        });
    }

    // إعداد معاينة وضغط الصور للحقول
    function setupImageHandler(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (!input || !preview) return;

        input.addEventListener('change', async function() {
            const file = this.files[0];
            if (!file) {
                preview.classList.add('d-none');
                preview.innerHTML = '';
                return;
            }

            const origSize = file.size;
            preview.classList.remove('d-none');
            preview.innerHTML = `
                <div class="d-flex align-items-center gap-2 p-2 rounded bg-light border">
                    <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                    <small class="text-muted">جاري معالجة وضغط الصورة لسرعة الرفع...</small>
                </div>
            `;

            try {
                const compressedFile = await compressImageFile(file);
                
                // تحديث ملف الحقل بالنسخة المضغوطة لتسريع الإرسال
                if (window.DataTransfer) {
                    const dt = new DataTransfer();
                    dt.items.add(compressedFile);
                    this.files = dt.files;
                }

                const newSize = compressedFile.size;
                const savedPercent = Math.max(0, Math.round((1 - (newSize / origSize)) * 100));

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <div class="card p-2 border shadow-sm bg-white mt-1">
                            <div class="d-flex align-items-center gap-3">
                                <img src="${e.target.result}" class="rounded border" style="width: 55px; height: 55px; object-fit: cover;">
                                <div class="flex-grow-1" style="font-size: 0.8rem;">
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 200px;">${compressedFile.name}</div>
                                    <div class="text-muted">
                                        الحجم: <strong class="text-success">${formatBytes(newSize)}</strong>
                                        ${savedPercent > 10 ? `<span class="badge bg-success-subtle text-success border ms-1">⚡ توفير ${savedPercent}%</span>` : ''}
                                    </div>
                                    <small class="text-primary"><i class="fas fa-check-circle me-1"></i> جاهزة للرفع السريع</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 rounded-pill" onclick="clearFileInput('${inputId}', '${previewId}')" title="إلغاء الصورة">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    `;
                };
                reader.readAsDataURL(compressedFile);
            } catch(err) {
                console.error('Image compression error:', err);
                preview.classList.add('d-none');
            }
        });
    }

    function clearFileInput(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (input) input.value = '';
        if (preview) {
            preview.classList.add('d-none');
            preview.innerHTML = '';
        }
    }

    // تفعيل مراقبة رفع الصور لكل النماذج
    document.addEventListener('DOMContentLoaded', function() {
        setupImageHandler('add_poster_image', 'add_poster_preview');
        setupImageHandler('add_cover_image', 'add_cover_preview');
        setupImageHandler('edit_poster_image', 'edit_poster_preview');
        setupImageHandler('edit_cover_image', 'edit_cover_preview');

        // ربط أزرار الإغلاق
        document.querySelectorAll('[data-bs-dismiss="modal"], [data-dismiss="modal"]').forEach(btn => {
            btn.addEventListener('click', function() {
                const modal = this.closest('.modal');
                if (modal) hideModalSafe(modal.id);
            });
        });

        // إدارة حالة الرفع للنموذج الأول (إضافة مشروع)
        const addForm = document.getElementById('addProjectForm');
        if (addForm) {
            addForm.addEventListener('submit', function() {
                const submitBtn = document.getElementById('addProjectSubmitBtn');
                const progressDiv = document.getElementById('add_upload_progress');
                if (progressDiv) progressDiv.classList.remove('d-none');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> جاري الرفع والحفظ...';
                }
            });
        }

        // إدارة حالة الرفع للنموذج الثاني (تعديل مشروع)
        const editForm = document.getElementById('editProjectForm');
        if (editForm) {
            editForm.addEventListener('submit', function() {
                const submitBtn = document.getElementById('editProjectSubmitBtn');
                const progressDiv = document.getElementById('edit_upload_progress');
                if (progressDiv) progressDiv.classList.remove('d-none');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> جاري حفظ التعديلات...';
                }
            });
        }
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

        // مسح وإعادة تعيين حقول الصور ومعايناتها
        clearFileInput('edit_poster_image', 'edit_poster_preview');
        clearFileInput('edit_cover_image', 'edit_cover_preview');

        // إظهار الصور الحالية للمشروع إن وُجدت
        const posterPrev = document.getElementById('edit_poster_preview');
        if (proj.poster_image && posterPrev) {
            const posterUrl = proj.poster_image.startsWith('http') ? proj.poster_image : ('/storage/' + proj.poster_image);
            posterPrev.classList.remove('d-none');
            posterPrev.innerHTML = `
                <div class="card p-2 border bg-light mt-1">
                    <div class="d-flex align-items-center gap-2">
                        <img src="${posterUrl}" class="rounded border" style="width: 45px; height: 45px; object-fit: cover;">
                        <small class="text-muted flex-grow-1">البوستر الحالي مسجل بالنظام (يمكنك تركه أو اختيار جديد لتغييره)</small>
                    </div>
                </div>
            `;
        }

        const coverPrev = document.getElementById('edit_cover_preview');
        if (proj.cover_image && coverPrev) {
            const coverUrl = proj.cover_image.startsWith('http') ? proj.cover_image : ('/storage/' + proj.cover_image);
            coverPrev.classList.remove('d-none');
            coverPrev.innerHTML = `
                <div class="card p-2 border bg-light mt-1">
                    <div class="d-flex align-items-center gap-2">
                        <img src="${coverUrl}" class="rounded border" style="width: 45px; height: 45px; object-fit: cover;">
                        <small class="text-muted flex-grow-1">الغلاف الحالي مسجل بالنظام</small>
                    </div>
                </div>
            `;
        }

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
