@extends('layouts.app')

@section('title', 'تفاصيل التدريب - ' . $training->title)

@section('content')
<div class="container-fluid px-2 px-md-3">
    @php
        $trainingTypeName = match($training->type) {
            'workshop' => 'ورشة عمل',
            'course' => 'دورة تدريبية',
            'seminar' => 'ندوة',
            'internship' => 'تدريب عملي',
            default => $training->type,
        };
        $trainingStatusName = match($training->status) {
            'active' => 'متاح للتسجيل',
            'completed' => 'مكتمل',
            default => 'غير نشط',
        };
    @endphp

    <!-- Unified Page Hero Banner -->
    <x-page-hero
        title="{{ $training->title }}"
        subtitle="{{ $training->company->name ?? 'مكتب تدريب الخريجين' }} • {{ $training->location ?? 'جامعة طرابلس' }}"
        icon="fas fa-graduation-cap"
        :breadcrumbs="[
            ['label' => 'منصة الخريجين', 'url' => route('graduate.dashboard')],
            ['label' => 'البرامج التدريبية', 'url' => route('graduate.trainings')],
            ['label' => $training->title]
        ]"
        badge="{{ $trainingTypeName }}"
        badgeIcon="fas fa-certificate"
        secondaryBadge="{{ $trainingStatusName }}"
        secondaryBadgeIcon="fas fa-check-circle"
    >
        <a href="{{ route('graduate.trainings') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-arrow-right fs-6"></i>
            <span>العودة للبرامج</span>
        </a>
        <a href="{{ route('graduate.dashboard') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-home fs-6"></i>
            <span>لوحة التحكم</span>
        </a>
    </x-page-hero>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: #ffffff;">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0 text-dark fw-bold fs-6">
                        <i class="fas fa-info-circle text-primary me-2"></i>تفاصيل البرنامج التدريبي
                    </h5>
                    <div class="d-flex gap-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1.5 fw-bold">
                            {{ $trainingTypeName }}
                        </span>
                        <span class="badge bg-{{ $training->status == 'active' ? 'success' : ($training->status == 'completed' ? 'info' : 'secondary') }} bg-opacity-10 text-{{ $training->status == 'active' ? 'success' : ($training->status == 'completed' ? 'info' : 'secondary') }} border border-{{ $training->status == 'active' ? 'success' : ($training->status == 'completed' ? 'info' : 'secondary') }} border-opacity-25 rounded-pill px-3 py-1.5 fw-bold">
                            {{ $trainingStatusName }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="p-3 bg-light rounded-4 mb-4 border border-light-subtle">
                        <p class="mb-0 text-dark" style="font-size: 1rem; line-height: 1.8;">{{ $training->description }}</p>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 border rounded-4 bg-light bg-opacity-50 h-100">
                                <h6 class="text-primary fw-bold mb-3 d-flex align-items-center gap-2"><i class="fas fa-calendar-alt text-warning"></i>التوقيت والمكان</h6>
                                <div class="small mb-2 d-flex justify-content-between">
                                    <span class="text-muted">المدة:</span>
                                    <span class="fw-bold text-dark">{{ $training->duration }}</span>
                                </div>
                                <div class="small mb-2 d-flex justify-content-between">
                                    <span class="text-muted">تاريخ البدء:</span>
                                    <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($training->start_date)->format('Y-m-d') }}</span>
                                </div>
                                <div class="small mb-2 d-flex justify-content-between">
                                    <span class="text-muted">تاريخ الانتهاء:</span>
                                    <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($training->end_date)->format('Y-m-d') }}</span>
                                </div>
                                <div class="small mb-2 d-flex justify-content-between">
                                    <span class="text-muted">أيام التدريب:</span>
                                    <span class="fw-bold text-primary">{{ $training->training_days_text }}</span>
                                </div>
                                <div class="small mb-2 d-flex justify-content-between">
                                    <span class="text-muted">المكان:</span>
                                    <span class="fw-bold text-dark">{{ $training->location }}</span>
                                </div>
                                <div class="small d-flex justify-content-between">
                                    <span class="text-muted">المقاعد المتاحة:</span>
                                    <span class="fw-bold text-dark">{{ $training->seats }} مقاعد</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="p-3 border rounded-4 bg-light bg-opacity-50 h-100">
                                <h6 class="text-primary fw-bold mb-3 d-flex align-items-center gap-2"><i class="fas fa-building text-info"></i>الجهة المنظمة والمدرب</h6>
                                <div class="small mb-2 d-flex justify-content-between">
                                    <span class="text-muted">الجهة المنظمة:</span>
                                    <span class="fw-bold text-dark">{{ $training->company->name ?? 'مكتب تدريب الخريجين' }}</span>
                                </div>
                                @if($training->company && $training->company->industry)
                                    <div class="small mb-2 d-flex justify-content-between">
                                        <span class="text-muted">المجال:</span>
                                        <span class="fw-bold text-dark">{{ $training->company->industry }}</span>
                                    </div>
                                @endif
                                <div class="small mb-2 d-flex justify-content-between">
                                    <span class="text-muted">المدرب:</span>
                                    <span class="fw-bold text-dark">{{ $training->instructor_name ?? 'غير محدد' }}</span>
                                </div>
                                @if($training->category)
                                    <div class="small d-flex justify-content-between">
                                        <span class="text-muted">التصنيف:</span>
                                        <span class="fw-bold text-dark">{{ $training->category }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($training->objectives)
                        <div class="mb-4">
                            <h6 class="text-dark fw-bold border-bottom pb-2 mb-3"><i class="fas fa-bullseye me-2 text-danger"></i>أهداف البرنامج</h6>
                            <div class="p-3 bg-light rounded-4 text-dark lh-lg">
                                {{ $training->objectives }}
                            </div>
                        </div>
                    @endif

                    @if($training->requirements)
                        <div class="mb-4">
                            <h6 class="text-dark fw-bold border-bottom pb-2 mb-3"><i class="fas fa-tasks me-2 text-success"></i>متطلبات وشروط الالتحاق</h6>
                            <div class="p-3 bg-light rounded-4 text-dark lh-lg">
                                {{ $training->requirements }}
                            </div>
                        </div>
                    @endif

                    @if($training->instructor_qualifications)
                        <div class="mb-3">
                            <h6 class="text-dark fw-bold border-bottom pb-2 mb-3"><i class="fas fa-user-graduate me-2 text-primary"></i>مؤهلات وخبرات المدرب</h6>
                            <div class="p-3 bg-light rounded-4 text-dark lh-lg">
                                {{ $training->instructor_qualifications }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm sticky-top" style="top: 20px; z-index: 1; background: #ffffff;">
                <div class="card-header bg-white py-3 px-4 border-bottom text-center">
                    <h5 class="card-title mb-0 text-dark fw-bold fs-6">
                        <i class="fas fa-paper-plane text-primary me-2"></i>التسجيل في البرنامج
                    </h5>
                </div>
                <div class="card-body p-4 text-center">
                    @php
                        $application = \App\Models\TrainingApplication::where('user_id', auth()->id())
                            ->where('training_id', $training->id)
                            ->first();
                    @endphp
                    
                    @if($application)
                        @if($application->status === 'approved')
                            @php
                                $myAttendances = \App\Models\TrainingAttendance::where('training_id', $training->id)
                                    ->where('user_id', auth()->id())
                                    ->get()
                                    ->keyBy(fn($a) => $a->date->format('Y-m-d'));
                                
                                $trainingDays = $training->training_days;
                                $totalDays = $training->total_days_count;
                                $myPresentCount = $myAttendances->where('status', 'present')->count();
                                $myPct = $totalDays > 0 ? round(($myPresentCount / $totalDays) * 100) : 0;
                            @endphp
                            <div class="p-3 bg-success bg-opacity-10 rounded-4 border border-success border-opacity-25 mb-3">
                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mx-auto mb-2 shadow-sm" style="width: 48px; height: 48px;">
                                    <i class="fas fa-check fa-lg"></i>
                                </div>
                                <h6 class="fw-bold text-success mb-1">تم قبولك في هذا البرنامج!</h6>
                                <p class="text-muted small mb-0">يمكنك إبراز بطاقتك الرقمية عند الحضور لتسجيل كل جلسة.</p>
                            </div>

                            <a href="{{ route('graduate.id-card') }}" class="btn btn-outline-primary rounded-3 w-100 py-2.5 mb-3 fw-bold d-flex align-items-center justify-content-center gap-2">
                                <i class="fas fa-qrcode"></i>
                                <span>فتح بطاقتي الرقمية (QR)</span>
                            </a>

                            {{-- سجل الحضور اليومي للمتدرب --}}
                            <div class="p-3 bg-light rounded-4 border border-light-subtle text-start mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold text-dark small"><i class="fas fa-clipboard-check text-primary me-1"></i>سجل حضوري بالدورة:</span>
                                    <span class="badge {{ $myPct >= 80 ? 'bg-success' : ($myPct >= 50 ? 'bg-warning text-dark' : 'bg-secondary') }} rounded-pill px-2.5 py-1 small">
                                        {{ $myPresentCount }} / {{ $totalDays }} أيام ({{ $myPct }}%)
                                    </span>
                                </div>
                                <div class="progress mb-3 rounded-pill" style="height: 8px;">
                                    <div class="progress-bar {{ $myPct >= 80 ? 'bg-success' : ($myPct >= 50 ? 'bg-warning' : 'bg-primary') }}" role="progressbar" style="width: {{ $myPct }}%"></div>
                                </div>

                                <div class="small">
                                    @foreach($trainingDays as $day)
                                        @php
                                            $att = $myAttendances[$day['date']] ?? null;
                                        @endphp
                                        <div class="d-flex justify-content-between align-items-center py-1.5 border-bottom border-light-subtle">
                                            <span class="text-secondary" style="font-size: 0.82rem;">
                                                يوم {{ $day['day_number'] }} ({{ $day['day_name'] }} {{ \Carbon\Carbon::parse($day['date'])->format('m/d') }}):
                                            </span>
                                            @if($att && $att->status === 'present')
                                                <span class="badge rounded-pill px-2.5 py-1" style="background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; font-size: 0.75rem;">
                                                    <i class="fas fa-check me-1"></i> حاضر ({{ $att->attended_at ? $att->attended_at->format('h:i A') : '' }})
                                                </span>
                                            @elseif($att && $att->status === 'excused')
                                                <span class="badge rounded-pill px-2.5 py-1" style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; font-size: 0.75rem;">
                                                    معذور
                                                </span>
                                            @elseif($day['is_future'])
                                                <span class="text-muted small">قادمة</span>
                                            @else
                                                <span class="badge rounded-pill px-2.5 py-1" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; font-size: 0.75rem;">
                                                    <i class="fas fa-times me-1"></i> غائب
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @elseif($application->status === 'rejected')
                            <div class="p-4 bg-danger bg-opacity-10 rounded-4 border border-danger border-opacity-25 mb-3">
                                <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 52px; height: 52px;">
                                    <i class="fas fa-times fa-2x"></i>
                                </div>
                                <h6 class="fw-bold text-danger mb-1">تم رفض الطلب</h6>
                                <p class="text-muted small mb-0">نأسف، لم يتم قبول طلبك لهذا البرنامج التدريبي.</p>
                            </div>
                        @else
                            <div class="p-4 bg-warning bg-opacity-10 rounded-4 border border-warning border-opacity-25 mb-3">
                                <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 52px; height: 52px;">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                                <h6 class="fw-bold text-warning text-dark mb-1">طلبك قيد المراجعة</h6>
                                <p class="text-muted small mb-0">تم إرسال طلبك بنجاح، وسيتم إعلامك بالنتيجة فور المراجعة.</p>
                            </div>
                        @endif
                    @else
                        @if($training->status == 'active')
                            <p class="text-muted small mb-4">هل أنت مهتم بهذا البرنامج؟ قدم طلبك الآن للانضمام قبل اكتمال المقاعد.</p>
                            <form action="{{ route('graduate.trainings.apply', $training->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary rounded-3 w-100 py-3 mb-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                                    <i class="fas fa-paper-plane"></i>
                                    <span>تقديم طلب التسجيل الآن</span>
                                </button>
                            </form>
                        @else
                            <div class="p-4 bg-light rounded-4 border border-light-subtle mb-3">
                                <i class="fas fa-lock fa-2x text-secondary mb-2"></i>
                                <h6 class="fw-bold text-dark mb-1">التسجيل مغلق</h6>
                                <p class="text-muted small mb-0">هذا البرنامج غير متاح للتسجيل حالياً.</p>
                            </div>
                        @endif
                        
                        <div class="text-start p-3 bg-light rounded-4 border border-light-subtle small">
                            <div class="mb-2 text-muted">
                                <i class="fas fa-info-circle text-primary me-1"></i> تأكد من تحديث بياناتك الشخصية في ملفك التعريفي.
                            </div>
                            <div class="text-muted">
                                <i class="fas fa-check-circle text-success me-1"></i> يتم فحص الطلبات وفق أسبقية التقديم والمقاعد المتاحة.
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection