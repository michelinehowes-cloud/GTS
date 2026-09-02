@extends('layouts.app')

@section('title', 'الكتيب الرقمي - ' . $fair->title)

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'منصة الخريجين', 'url' => route('graduate.dashboard')],
            ['label' => 'المعارض المهنية', 'url' => route('job-fair.public')],
            ['label' => 'الكتيب الرقمي: ' . $fair->title, 'active' => true],
        ]
    ])

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="text-primary fw-bold mb-0">
                <i class="fas fa-book-open me-2"></i>الكتيب الرقمي: {{ $fair->title }}
            </h2>
            <div class="text-muted small mt-1">استكشف الشركات المشاركة والفعاليات المصاحبة للمعرض المهني</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('job-fair.public') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right me-1"></i> العودة للمعرض
            </a>
            @php
                $userReg = $fair->registrations()->where('user_id', auth()->id())->first();
            @endphp
            @if($userReg)
                <a href="{{ route('job-fair.my-ticket', $userReg->id) }}" class="btn btn-primary-modern">
                    <i class="fas fa-ticket-alt me-1"></i> تذكرتي
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <ul class="nav nav-pills mb-4 gap-2" id="catalogTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold px-4 py-2 rounded-pill" id="companies-tab" data-bs-toggle="tab" data-bs-target="#companies" type="button" role="tab" aria-controls="companies" aria-selected="true">
                <i class="fas fa-building me-1"></i> الشركات المشاركة ({{ $companies->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold px-4 py-2 rounded-pill" id="recommended-tab" data-bs-toggle="tab" data-bs-target="#recommended" type="button" role="tab" aria-controls="recommended" aria-selected="false">
                <i class="fas fa-magic me-1"></i> موصى بها لك ({{ $recommendedCompanies->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold px-4 py-2 rounded-pill" id="agenda-tab" data-bs-toggle="tab" data-bs-target="#agenda" type="button" role="tab" aria-controls="agenda" aria-selected="false">
                <i class="fas fa-calendar-alt me-1"></i> جدول الفعاليات ({{ $events->count() }})
            </button>
        </li>
    </ul>

    <div class="tab-content" id="catalogTabsContent">
        <!-- الشركات المشاركة -->
        <div class="tab-pane fade show active" id="companies" role="tabpanel" aria-labelledby="companies-tab">
            <div class="row g-4">
                @forelse($companies as $company)
                    <div class="col-md-6 col-lg-4">
                        <div class="card-modern h-100 d-flex flex-column position-relative">
                            <button type="button" class="btn btn-sm btn-light border position-absolute toggle-wishlist" style="top: 15px; left: 15px; z-index: 2;" data-id="{{ $company->id }}">
                                <i class="fas fa-heart text-danger {{ in_array($company->id, $wishlistedIds) ? '' : 'd-none' }}"></i>
                                <i class="far fa-heart text-muted {{ in_array($company->id, $wishlistedIds) ? 'd-none' : '' }}"></i>
                            </button>
                            
                            <div class="card-body p-4 flex-grow-1 text-center">
                                <div class="mb-3 mt-1">
                                    @if($company->company && $company->company->logo)
                                        <img src="{{ Storage::url($company->company->logo) }}" alt="شعار الشركة" class="img-fluid rounded-circle border p-1" style="width: 76px; height: 76px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-light border d-inline-flex align-items-center justify-content-center text-primary" style="width: 76px; height: 76px; font-size: 24px;">
                                            <i class="fas fa-building"></i>
                                        </div>
                                    @endif
                                </div>
                                <h5 class="card-title fw-bold text-dark mb-1">{{ $company->company->name_ar ?? ($company->company->name ?? 'شركة') }}</h5>
                                <p class="text-muted small mb-3">{{ $company->company->industry ?? 'مجال عام' }}</p>
                                
                                <div class="p-3 bg-light rounded-3 small text-start">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted"><i class="fas fa-map-marker-alt text-danger me-1"></i> رقم الجناح:</span>
                                        <span class="fw-bold text-dark">{{ $company->booth_number ?? 'غير محدد' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted"><i class="fas fa-briefcase text-primary me-1"></i> المجالات:</span>
                                        <span class="fw-bold text-dark">{{ $company->participating_sectors ?? 'عام' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white p-3 border-top text-center">
                                <button type="button" class="btn btn-outline-primary-modern btn-sm w-100" data-bs-toggle="modal" data-bs-target="#companyModal{{ $company->id }}">
                                    <i class="fas fa-eye me-1"></i> عرض التفاصيل
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal details -->
                    <div class="modal fade" id="companyModal{{ $company->id }}" tabindex="-1" aria-labelledby="companyModalLabel{{ $company->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-white border-bottom">
                                    <h5 class="modal-title text-primary fw-bold" id="companyModalLabel{{ $company->id }}">
                                        <i class="fas fa-building me-2"></i>{{ $company->company->name_ar ?? ($company->company->name ?? 'تفاصيل الشركة') }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">تفاصيل الجناح</h6>
                                    <div class="mb-2"><strong>رقم الجناح:</strong> {{ $company->booth_number ?? 'غير محدد' }}</div>
                                    <div class="mb-4"><strong>موقع الجناح:</strong> {{ $company->booth_location ?? 'غير محدد' }}</div>
                                    
                                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">الفرص المتاحة</h6>
                                    <p class="text-muted mb-4">{{ $company->available_positions ?? 'سيتم الإعلان عنها في المعرض.' }}</p>
                                    
                                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">المتطلبات</h6>
                                    <p class="text-muted mb-0">{{ $company->requirements ?? 'لا توجد متطلبات محددة.' }}</p>
                                </div>
                                <div class="modal-footer bg-light border-0">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إغلاق</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="card-modern text-center py-5">
                            <div class="card-body py-5">
                                <i class="fas fa-building display-3 text-muted mb-3 opacity-50"></i>
                                <h4 class="fw-bold text-dark mb-2">لا توجد شركات مشاركة حتى الآن</h4>
                                <p class="text-muted mb-0">يرجى مراجعة الكتيب لاحقاً للاطلاع على الشركات المسجلة</p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- الشركات الموصى بها -->
        <div class="tab-pane fade" id="recommended" role="tabpanel" aria-labelledby="recommended-tab">
            <div class="alert alert-info border-0 rounded-3 shadow-sm mb-4">
                <i class="fas fa-magic me-2 text-primary"></i> بناءً على تخصصك <strong>({{ auth()->user()->graduateProfile->university_specialization ?? 'غير محدد' }})</strong>، نوصيك بزيارة أجنحة الشركات التالية لأنها تبحث عن مهارات وتخصصات مطابقة لتخصصك.
            </div>
            <div class="row g-4">
                @forelse($recommendedCompanies as $company)
                    <div class="col-md-6 col-lg-4">
                        <div class="card-modern h-100 d-flex flex-column border-primary">
                            <div class="card-body p-4 flex-grow-1 text-center position-relative">
                                <div class="position-absolute text-warning" style="top: 15px; right: 15px; font-size: 1.3rem;">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="mb-3 mt-1">
                                    @if($company->company && $company->company->logo)
                                        <img src="{{ Storage::url($company->company->logo) }}" alt="شعار الشركة" class="img-fluid rounded-circle border p-1" style="width: 76px; height: 76px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-light border d-inline-flex align-items-center justify-content-center text-primary" style="width: 76px; height: 76px; font-size: 24px;">
                                            <i class="fas fa-building"></i>
                                        </div>
                                    @endif
                                </div>
                                <h5 class="card-title fw-bold text-dark mb-1">{{ $company->company->name_ar ?? ($company->company->name ?? 'شركة') }}</h5>
                                <span class="badge bg-light text-success border border-success rounded-pill px-3 py-1 mb-3">
                                    <i class="fas fa-check-circle me-1"></i> تطابق التخصص
                                </span>
                                <div class="p-3 bg-light rounded-3 small text-start">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted"><i class="fas fa-map-marker-alt text-danger me-1"></i> رقم الجناح:</span>
                                        <span class="fw-bold text-dark">{{ $company->booth_number ?? 'غير محدد' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white p-3 border-top text-center">
                                <button type="button" class="btn btn-outline-primary-modern btn-sm w-100" data-bs-toggle="modal" data-bs-target="#companyModal{{ $company->id }}">
                                    <i class="fas fa-eye me-1"></i> عرض التفاصيل
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="card-modern text-center py-5">
                            <div class="card-body py-5">
                                <i class="fas fa-info-circle display-3 text-muted mb-3 opacity-50"></i>
                                <h4 class="fw-bold text-dark mb-2">لم نجد شركات تطابق تخصصك بدقة في الوقت الحالي</h4>
                                <p class="text-muted mb-0">نرجو منك استكشاف جميع الشركات المشاركة في المعرض</p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- جدول الفعاليات -->
        <div class="tab-pane fade" id="agenda" role="tabpanel" aria-labelledby="agenda-tab">
            <div class="card-modern">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-calendar-alt me-2"></i>جدول الجلسات والورش المصاحبة
                    </h5>
                </div>
                <div class="card-body p-4">
                    @forelse($events as $event)
                        <div class="p-4 rounded-3 bg-light border mb-3">
                            <div class="row align-items-center g-3">
                                <div class="col-md-3">
                                    <div class="text-primary fw-bold fs-5 mb-1">{{ $event->start_time->format('h:i A') }}</div>
                                    <div class="text-muted small">إلى {{ $event->end_time->format('h:i A') }}</div>
                                    <div class="text-secondary small mt-2">
                                        <i class="fas fa-calendar-day me-1"></i> {{ $event->start_time->format('Y-m-d') }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="fw-bold text-dark mb-1">{{ $event->title }}</h5>
                                    @if($event->speaker_name)
                                        <p class="text-muted small mb-2"><i class="fas fa-user-tie me-1 text-info"></i> {{ $event->speaker_name }}</p>
                                    @endif
                                    <p class="text-muted small mb-2">{{ $event->description }}</p>
                                    
                                    <div class="d-flex gap-2 flex-wrap">
                                        @if($event->location)
                                            <span class="badge bg-light text-danger border border-danger rounded-pill px-3 py-1 small">
                                                <i class="fas fa-map-marker-alt me-1"></i> {{ $event->location }}
                                            </span>
                                        @endif
                                        @if($event->capacity)
                                            @php
                                                $attendeesCount = \App\Models\JobFairEventAttendee::where('job_fair_event_id', $event->id)->count();
                                            @endphp
                                            <span class="badge bg-light text-primary border border-primary rounded-pill px-3 py-1 small">
                                                <i class="fas fa-users me-1"></i> {{ $attendeesCount }} / {{ $event->capacity }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3 text-md-end">
                                    <form action="{{ route('job-fair.event.toggle', $event->id) }}" method="POST">
                                        @csrf
                                        @if(in_array($event->id, $registeredEventIds))
                                            <button type="submit" class="btn btn-outline-success btn-sm rounded-pill px-4">
                                                <i class="fas fa-check-circle me-1"></i> تم التسجيل
                                            </button>
                                        @else
                                            @if($event->capacity && $attendeesCount >= $event->capacity)
                                                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" disabled>
                                                    مكتمل العدد
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-primary-modern btn-sm rounded-pill px-4">
                                                    <i class="fas fa-plus-circle me-1"></i> التسجيل للفعالية
                                                </button>
                                            @endif
                                        @endif
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times display-4 text-muted mb-3 opacity-50"></i>
                            <h5 class="fw-bold text-dark">لا توجد فعاليات مجدولة بعد</h5>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.toggle-wishlist').click(function(e) {
            e.preventDefault();
            var btn = $(this);
            var companyId = btn.data('id');
            var url = '{{ route("job-fair.wishlist.toggle", ":id") }}'.replace(':id', companyId);

            $.ajax({
                url: url,
                type: 'POST',
                success: function(response) {
                    if (response.status === 'added') {
                        btn.find('.far.fa-heart').addClass('d-none');
                        btn.find('.fas.fa-heart').removeClass('d-none');
                        toastr.success(response.message);
                    } else {
                        btn.find('.fas.fa-heart').addClass('d-none');
                        btn.find('.far.fa-heart').removeClass('d-none');
                        toastr.info(response.message);
                    }
                },
                error: function() {
                    toastr.error('حدث خطأ أثناء تنفيذ العملية.');
                }
            });
        });
    });
</script>
@endpush

