@extends('layouts.app')

@section('title', 'السيرة الذاتية - ' . $graduate->name)

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- أزرار الإجراءات -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right"></i> عودة
                </a>
                
                <div class="d-flex gap-2">
                    @if(auth()->user()->role === 'company')
                        <a href="{{ route('messages.show', $graduate->id) }}" class="btn btn-success">
                            <i class="fas fa-envelope"></i> مراسلة الخريج
                        </a>
                    @endif
                    
                    @if($graduate->graduateData && $graduate->graduateData->cv_path)
                        <a href="{{ Storage::url($graduate->graduateData->cv_path) }}" target="_blank" class="btn btn-primary">
                            <i class="fas fa-download"></i> تحميل الـ CV
                        </a>
                    @endif
                </div>
            </div>

            <!-- بطاقة الملف الشخصي (Header) -->
            <div class="premium-card mb-4 overflow-hidden">
                <div class="position-relative bg-gradient-dark" style="min-height: 220px;">
                    <!-- Decorative shapes -->
                    <div class="position-absolute" style="top: -50px; right: -50px; width: 200px; height: 200px; border-radius: 50%; background: rgba(255,255,255,0.05);"></div>
                    <div class="position-absolute" style="bottom: -20px; left: 10%; width: 100px; height: 100px; border-radius: 50%; background: rgba(255,255,255,0.05);"></div>
                    
                    <div class="position-absolute w-100" style="bottom: -60px; left: 0; right: 0; z-index: 10;">
                        <div class="px-4 px-md-5 d-flex flex-column flex-md-row align-items-center align-items-md-end gap-3 gap-md-4">
                            <!-- Avatar -->
                            <div class="bg-white rounded-circle shadow-lg hover-scale" style="width: 150px; height: 150px; border: 6px solid #fff; transition: transform 0.3s ease;">
                                <div class="rounded-circle bg-light d-flex justify-content-center align-items-center w-100 h-100" style="font-size: 3.5rem; color: var(--primary-blue); font-weight: bold;">
                                    {{ mb_substr($graduate->name, 0, 1) }}
                                </div>
                            </div>
                            
                            <!-- Text info (pushed up to sit inside the blue area on desktop) -->
                            <div class="text-center text-md-start flex-grow-1 pb-md-5 pb-0 mt-3 mt-md-0">
                                <h2 class="fw-bold mb-1 text-white">{{ $graduate->name }}</h2>
                                <p class="text-white-50 fs-5 mb-3">
                                    <i class="fas fa-graduation-cap me-2"></i>{{ $graduate->graduateData->major ?? 'خريج جامعة طرابلس' }}
                                </p>
                                <div class="d-flex justify-content-center justify-content-md-start gap-3 mt-2">
                                    <span class="badge glass-panel text-white p-2 shadow-sm fs-6">
                                        <i class="fas fa-star text-warning me-1"></i> المعدل: {{ $graduate->graduateData->gpa ? $graduate->graduateData->gpa . '%' : 'N/A' }}
                                    </span>
                                    <span class="badge glass-panel text-white p-2 shadow-sm fs-6">
                                        <i class="fas fa-calendar-alt text-light me-1"></i> الدفعة: {{ $graduate->graduateData->graduation_year ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Spacer for avatar overlap -->
                <div style="height: 70px; background-color: var(--background-white);"></div>
                
                <!-- Social links & Actions -->
                <div class="card-body bg-white px-4 px-md-5 py-3 border-top">
                    <div class="d-flex justify-content-center justify-content-md-end gap-3">
                        @if($graduate->graduateData && $graduate->graduateData->linkedin_url)
                            <a href="{{ $graduate->graduateData->linkedin_url }}" target="_blank" class="btn btn-outline-primary rounded-circle hover-scale" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                                <i class="fab fa-linkedin-in fs-5"></i>
                            </a>
                        @endif
                        @if($graduate->graduateData && $graduate->graduateData->portfolio_url)
                            <a href="{{ $graduate->graduateData->portfolio_url }}" target="_blank" class="btn btn-outline-dark rounded-circle hover-scale" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-globe fs-5"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- تفاصيل السيرة الذاتية (Body) -->
            <div class="row">
                <!-- الجانب الأيمن (معلومات شخصية ومهارات) -->
                <div class="col-lg-4 mb-4">
                    <div class="premium-card h-100">
                        <div class="card-body p-4 text-start">
                            <h5 class="fw-bold text-primary mb-4 border-bottom pb-2">
                                <i class="fas fa-address-card me-2"></i> التواصل
                            </h5>
                            
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-box bg-light text-primary me-3">
                                        <i class="fas fa-envelope fa-fw"></i>
                                    </div>
                                    <div style="min-width: 0;">
                                        <div class="small text-muted text-uppercase tracking-wider">البريد الإلكتروني</div>
                                        <a href="mailto:{{ $graduate->email }}" class="text-dark fw-bold text-decoration-none" style="word-break: break-all;">{{ $graduate->email }}</a>
                                    </div>
                                </div>
                                
                                @if($graduate->graduateData && $graduate->graduateData->phone)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-box bg-light text-primary me-3">
                                        <i class="fas fa-phone fa-fw"></i>
                                    </div>
                                    <div>
                                        <div class="small text-muted text-uppercase tracking-wider">رقم الهاتف</div>
                                        <div class="fw-bold">{{ $graduate->graduateData->phone }}</div>
                                    </div>
                                </div>
                                @endif
                                
                                @if($graduate->graduateData && $graduate->graduateData->address)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-box bg-light text-primary me-3">
                                        <i class="fas fa-map-marker-alt fa-fw"></i>
                                    </div>
                                    <div>
                                        <div class="small text-muted text-uppercase tracking-wider">العنوان</div>
                                        <div class="fw-bold">{{ $graduate->graduateData->address }}</div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <h5 class="fw-bold text-primary mb-4 border-bottom pb-2">المهارات</h5>
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                @if($graduate->graduateData && !empty($graduate->graduateData->skills))
                                    @foreach($graduate->graduateData->skills as $skill)
                                        <span class="badge bg-soft-primary text-primary border border-primary px-3 py-2 rounded-pill">{{ $skill }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted small">لم يتم إضافة مهارات</span>
                                @endif
                            </div>

                            <h5 class="fw-bold text-primary mb-4 border-bottom pb-2">اللغات</h5>
                            <div class="d-flex flex-wrap gap-2">
                                @if($graduate->graduateData && !empty($graduate->graduateData->languages))
                                    @foreach($graduate->graduateData->languages as $lang)
                                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><i class="fas fa-language text-muted me-1"></i> {{ $lang }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted small">لم يتم إضافة لغات</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الجانب الأيسر (الخبرات والتعليم) -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-4 p-md-5 text-start">
                            
                            <h4 class="fw-bold text-gray-800 mb-4"><i class="fas fa-briefcase text-primary me-2"></i> الحالة والخبرات</h4>
                            
                            <div class="mb-4">
                                <h6 class="text-muted mb-2">الحالة الوظيفية:</h6>
                                @php
                                    $statusEnum = $graduate->graduateData->employment_status ?? null;
                                    $statusMap = [
                                        'seeking_opportunities' => 'يبحث عن فرص عمل',
                                        'employed' => 'موظف',
                                        'training' => 'في فترة تدريب',
                                        'freelancer' => 'عمل حر',
                                    ];
                                    $statusText = $statusEnum ? ($statusMap[$statusEnum] ?? $statusEnum) : 'غير محدد';
                                @endphp
                                @if($statusEnum)
                                    <span class="badge bg-success fs-6 px-3 py-2 rounded-pill">{{ $statusText }}</span>
                                @else
                                    <span class="badge bg-secondary fs-6 px-3 py-2 rounded-pill">{{ $statusText }}</span>
                                @endif
                            </div>
                            
                            <div class="mb-5">
                                <h6 class="text-muted mb-3">تفاصيل الخبرة:</h6>
                                @if($graduate->graduateData && $graduate->graduateData->work_experience)
                                    <div class="p-3 bg-light rounded" style="border-right: 4px solid #0d6efd;">
                                        {{ $graduate->graduateData->work_experience }}
                                    </div>
                                @else
                                    <p class="text-muted">لا توجد خبرات سابقة مضافة.</p>
                                @endif
                            </div>

                            <hr class="my-5">

                            <h4 class="fw-bold text-gray-800 mb-4"><i class="fas fa-university text-primary me-2"></i> المؤهل العلمي</h4>
                            
                            <div class="position-relative ms-0 me-3 border-end border-2 border-primary pb-4 pe-4">
                                <div class="position-absolute bg-primary rounded-circle" style="width: 14px; height: 14px; right: -8px; top: 0;"></div>
                                <div>
                                    <h5 class="fw-bold mb-1">{{ $graduate->graduateData->major ?? 'التخصص غير محدد' }}</h5>
                                    <div class="text-primary fw-bold mb-2">{{ $graduate->graduateData->university ?? 'جامعة طرابلس' }} - {{ $graduate->graduateData->faculty ?? '' }}</div>
                                    
                                    <div class="row mt-3 text-muted">
                                        <div class="col-sm-6 mb-2">
                                            <i class="fas fa-award fa-fw"></i> الدرجة: <strong>{{ $graduate->graduateData->degree ?? 'بكالوريوس' }}</strong>
                                        </div>
                                        <div class="col-sm-6 mb-2">
                                            <i class="fas fa-chart-line fa-fw"></i> المعدل: <strong dir="ltr">{{ $graduate->graduateData->gpa ? $graduate->graduateData->gpa . '%' : 'N/A' }}</strong>
                                        </div>
                                        <div class="col-sm-6 mb-2">
                                            <i class="fas fa-calendar-check fa-fw"></i> سنة التخرج: <strong>{{ $graduate->graduateData->graduation_year ?? 'N/A' }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($graduate->graduateData && $graduate->graduateData->notes)
                            <hr class="my-5">
                            <h4 class="fw-bold text-gray-800 mb-4"><i class="fas fa-info-circle text-primary me-2"></i> معلومات إضافية</h4>
                            <p class="text-muted lh-lg">
                                {{ $graduate->graduateData->notes }}
                            </p>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<style>
    .bg-soft-primary {
        background-color: rgba(13, 110, 253, 0.1);
    }
</style>
@endsection
