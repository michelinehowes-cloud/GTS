@extends('layouts.app')

@section('title', 'الكتيب الرقمي - ' . $fair->title)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-book-open text-primary mr-2"></i> الكتيب الرقمي: {{ $fair->title }}</h1>
            <p class="text-muted mt-2">استكشف الشركات المشاركة والفعاليات المصاحبة للمعرض.</p>
        </div>
        <div>
            <a href="{{ route('job-fair.public') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right mr-1"></i> العودة للمعرض
            </a>
            <a href="{{ route('job-fair.my-ticket', $fair->registrations()->where('user_id', auth()->id())->first()->id) }}" class="btn btn-primary ml-2">
                <i class="fas fa-ticket-alt mr-1"></i> تذكرتي
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <ul class="nav nav-tabs mb-4" id="catalogTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active font-weight-bold" id="companies-tab" data-toggle="tab" href="#companies" role="tab" aria-controls="companies" aria-selected="true">
                <i class="fas fa-building mr-1"></i> الشركات المشاركة ({{ $companies->count() }})
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link font-weight-bold text-success" id="recommended-tab" data-toggle="tab" href="#recommended" role="tab" aria-controls="recommended" aria-selected="false">
                <i class="fas fa-magic mr-1"></i> موصى بها لك ({{ $recommendedCompanies->count() }})
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link font-weight-bold text-info" id="agenda-tab" data-toggle="tab" href="#agenda" role="tab" aria-controls="agenda" aria-selected="false">
                <i class="fas fa-calendar-alt mr-1"></i> جدول الفعاليات ({{ $events->count() }})
            </a>
        </li>
    </ul>

    <div class="tab-content" id="catalogTabsContent">
        <!-- الشركات المشاركة -->
        <div class="tab-pane fade show active" id="companies" role="tabpanel" aria-labelledby="companies-tab">
            <div class="row">
                @forelse($companies as $company)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm border-0 rounded-lg">
                            <div class="card-body position-relative">
                                <button type="button" class="btn btn-sm btn-outline-danger position-absolute toggle-wishlist" style="top: 15px; left: 15px;" data-id="{{ $company->id }}">
                                    <i class="fas fa-heart {{ in_array($company->id, $wishlistedIds) ? '' : 'd-none' }}"></i>
                                    <i class="far fa-heart {{ in_array($company->id, $wishlistedIds) ? 'd-none' : '' }}"></i>
                                </button>
                                
                                <div class="text-center mb-3 mt-2">
                                    @if($company->company->logo)
                                        <img src="{{ Storage::url($company->company->logo) }}" alt="شعار الشركة" class="img-fluid rounded-circle border" style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-light border d-inline-flex align-items-center justify-content-center text-secondary" style="width: 80px; height: 80px; font-size: 24px;">
                                            <i class="fas fa-building"></i>
                                        </div>
                                    @endif
                                </div>
                                <h5 class="card-title text-center font-weight-bold">{{ $company->company->name_ar }}</h5>
                                <p class="text-muted text-center small mb-3">{{ $company->company->industry }}</p>
                                
                                <ul class="list-group list-group-flush small">
                                    <li class="list-group-item px-0 border-0 py-1"><i class="fas fa-map-marker-alt text-danger w-20px"></i> <strong>رقم الجناح:</strong> {{ $company->booth_number ?? 'غير محدد' }}</li>
                                    <li class="list-group-item px-0 border-0 py-1"><i class="fas fa-briefcase text-primary w-20px"></i> <strong>المجالات:</strong> {{ $company->participating_sectors ?? 'عام' }}</li>
                                </ul>
                            </div>
                            <div class="card-footer bg-white border-top-0 pt-0 pb-3 text-center">
                                <button type="button" class="btn btn-primary btn-sm rounded-pill px-4" data-toggle="modal" data-target="#companyModal{{ $company->id }}">
                                    عرض التفاصيل
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal details -->
                    <div class="modal fade" id="companyModal{{ $company->id }}" tabindex="-1" aria-labelledby="companyModalLabel{{ $company->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="companyModalLabel{{ $company->id }}">{{ $company->company->name_ar }}</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <h6 class="font-weight-bold text-primary border-bottom pb-2 mb-3">تفاصيل الجناح</h6>
                                    <p><strong>رقم الجناح:</strong> {{ $company->booth_number ?? 'غير محدد' }}</p>
                                    <p><strong>موقع الجناح:</strong> {{ $company->booth_location ?? 'غير محدد' }}</p>
                                    
                                    <h6 class="font-weight-bold text-primary border-bottom pb-2 mt-4 mb-3">الفرص المتاحة</h6>
                                    <p>{{ $company->available_positions ?? 'سيتم الإعلان عنها في المعرض.' }}</p>
                                    
                                    <h6 class="font-weight-bold text-primary border-bottom pb-2 mt-4 mb-3">المتطلبات</h6>
                                    <p>{{ $company->requirements ?? 'لا توجد متطلبات محددة.' }}</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="display-4 text-muted mb-3"><i class="fas fa-building"></i></div>
                        <h4>لا توجد شركات مشاركة حتى الآن</h4>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- الشركات الموصى بها -->
        <div class="tab-pane fade" id="recommended" role="tabpanel" aria-labelledby="recommended-tab">
            <div class="alert alert-success bg-success text-white border-0 shadow-sm mb-4">
                <i class="fas fa-magic mr-2"></i> بناءً على تخصصك <strong>({{ auth()->user()->graduateProfile->university_specialization ?? 'غير محدد' }})</strong>، نوصيك بزيارة أجنحة الشركات التالية لأنها تبحث عن مهارات وتخصصات مطابقة لتخصصك.
            </div>
            <div class="row">
                @forelse($recommendedCompanies as $company)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm border-success rounded-lg" style="border-width: 2px;">
                            <div class="card-body text-center position-relative">
                                <div class="position-absolute text-success" style="top: 15px; right: 15px; font-size: 1.5rem;">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="mb-3 mt-2">
                                    @if($company->company->logo)
                                        <img src="{{ Storage::url($company->company->logo) }}" alt="شعار الشركة" class="img-fluid rounded-circle border" style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-light border d-inline-flex align-items-center justify-content-center text-secondary" style="width: 80px; height: 80px; font-size: 24px;">
                                            <i class="fas fa-building"></i>
                                        </div>
                                    @endif
                                </div>
                                <h5 class="card-title font-weight-bold">{{ $company->company->name_ar }}</h5>
                                <p class="text-success small font-weight-bold mb-3"><i class="fas fa-check-circle mr-1"></i> تطابق التخصص</p>
                                <ul class="list-group list-group-flush small text-right">
                                    <li class="list-group-item px-0 border-0 py-1"><i class="fas fa-map-marker-alt text-danger w-20px"></i> <strong>رقم الجناح:</strong> {{ $company->booth_number ?? 'غير محدد' }}</li>
                                </ul>
                            </div>
                            <div class="card-footer bg-white border-top-0 pt-0 pb-3 text-center">
                                <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-4" data-toggle="modal" data-target="#companyModal{{ $company->id }}">
                                    عرض التفاصيل
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="display-4 text-muted mb-3"><i class="fas fa-frown"></i></div>
                        <h4>لم نجد شركات تطابق تخصصك بدقة في الوقت الحالي.</h4>
                        <p class="text-muted">نرجو منك استكشاف جميع الشركات المشاركة.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- جدول الفعاليات -->
        <div class="tab-pane fade" id="agenda" role="tabpanel" aria-labelledby="agenda-tab">
            <div class="row">
                <div class="col-12">
                    <div class="timeline p-4 rounded-lg bg-white shadow-sm border">
                        @forelse($events as $event)
                            <div class="timeline-item pb-4 border-bottom mb-4 last-border-0">
                                <div class="row">
                                    <div class="col-md-3 text-md-right mb-3 mb-md-0">
                                        <div class="text-primary font-weight-bold h5 mb-1">{{ $event->start_time->format('h:i A') }}</div>
                                        <div class="text-muted small">إلى {{ $event->end_time->format('h:i A') }}</div>
                                        <div class="text-secondary small mt-2">
                                            <i class="fas fa-calendar-day mr-1"></i> {{ $event->start_time->format('Y-m-d') }}
                                        </div>
                                    </div>
                                    <div class="col-md-9 border-right pr-4">
                                        <h5 class="font-weight-bold text-dark">{{ $event->title }}</h5>
                                        @if($event->speaker_name)
                                            <p class="text-muted mb-2"><i class="fas fa-user-tie mr-1 text-info"></i> {{ $event->speaker_name }}</p>
                                        @endif
                                        <p class="mb-3">{{ $event->description }}</p>
                                        
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                            <div>
                                                @if($event->location)
                                                    <span class="badge badge-light border mr-2 py-2 px-3"><i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ $event->location }}</span>
                                                @endif
                                                @if($event->capacity)
                                                    @php
                                                        $attendeesCount = \App\Models\JobFairEventAttendee::where('job_fair_event_id', $event->id)->count();
                                                    @endphp
                                                    <span class="badge badge-light border py-2 px-3"><i class="fas fa-users text-primary mr-1"></i> {{ $attendeesCount }} / {{ $event->capacity }}</span>
                                                @endif
                                            </div>
                                            <div class="mt-3 mt-md-0">
                                                <form action="{{ route('job-fair.event.toggle', $event->id) }}" method="POST">
                                                    @csrf
                                                    @if(in_array($event->id, $registeredEventIds))
                                                        <button type="submit" class="btn btn-success rounded-pill px-4">
                                                            <i class="fas fa-check-circle mr-1"></i> تم التسجيل
                                                        </button>
                                                    @else
                                                        @if($event->capacity && $attendeesCount >= $event->capacity)
                                                            <button type="button" class="btn btn-secondary rounded-pill px-4" disabled>
                                                                مكتمل العدد
                                                            </button>
                                                        @else
                                                            <button type="submit" class="btn btn-outline-primary rounded-pill px-4">
                                                                <i class="fas fa-plus-circle mr-1"></i> التسجيل للفعالية
                                                            </button>
                                                        @endif
                                                    @endif
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <div class="display-4 text-muted mb-3"><i class="fas fa-calendar-times"></i></div>
                                <h4>لا توجد فعاليات مجدولة بعد.</h4>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .w-20px { width: 20px; text-align: center; }
    .last-border-0:last-child { border-bottom: none !important; margin-bottom: 0 !important; padding-bottom: 0 !important; }
    
    .timeline { position: relative; }
    .timeline::before {
        content: '';
        position: absolute;
        right: calc(25% - 1px); /* Match col-md-3 border-right */
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
        z-index: 1;
        display: none;
    }
    @media (min-width: 768px) {
        .timeline::before { display: block; }
    }
    
    .timeline-item { position: relative; z-index: 2; }
    .timeline-item::after {
        content: '';
        position: absolute;
        right: calc(25% - 6px); /* Align with border-right */
        top: 0;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #4e73df;
        border: 3px solid #fff;
        z-index: 2;
        display: none;
    }
    @media (min-width: 768px) {
        .timeline-item::after { display: block; }
    }
</style>

@endsection

@section('scripts')
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
@endsection
