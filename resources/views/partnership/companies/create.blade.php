@extends('layouts.app')

@section('title', 'إضافة شركة جديدة - مسؤول الشراكات')

@section('page-title', 'إضافة شركة جديدة')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('partnership.dashboard')],
            ['label' => 'إدارة الشركات', 'url' => route('partnership.companies')],
            ['label' => 'إضافة شركة', 'active' => true],
        ]
    ])

    <x-bento-form title="إضافة شركة جديدة" subtitle="بيانات المؤسسة أو الشركة الشريكة ومسؤول الاتصال" icon="fa-building" :backRoute="route('partnership.companies')">
                    @if($errors->any())
                    <div class="alert alert-danger border-0 bg-light text-danger mb-4">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('partnership.companies.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- =================== القسم الأول: بيانات الشركة الأساسية =================== --}}
                        <div class="d-flex align-items-center gap-2 mb-3 mt-1">
                            <span class="badge rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:linear-gradient(135deg,#0ea5e9,#6366f1);font-size:.9rem;"><i class="fas fa-building text-white"></i></span>
                            <h6 class="fw-bold text-dark mb-0">بيانات الشركة الأساسية</h6>
                        </div>

                        <div class="row g-3">
                            <!-- اسم الشركة -->
                            <div class="col-md-6">
                                <label for="name" class="form-label-modern">اسم الشركة <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-building text-muted"></i></span>
                                    <input type="text" class="form-control-modern border-start-0 ps-0 @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required placeholder="اسم الشركة الرسمي">
                                </div>
                                @error('name')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- المجال الصناعي -->
                            <div class="col-md-6">
                                <label for="industry" class="form-label-modern">المجال الصناعي <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-industry text-muted"></i></span>
                                    <input type="text" class="form-control-modern border-start-0 ps-0 @error('industry') is-invalid @enderror" 
                                           id="industry" name="industry" value="{{ old('industry') }}" required 
                                           placeholder="مثل: تكنولوجيا، تسويق، تعليم...">
                                </div>
                                @error('industry')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- البريد الإلكتروني -->
                            <div class="col-md-6">
                                <label for="email" class="form-label-modern">البريد الإلكتروني الرسمي <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                    <input type="email" class="form-control-modern border-start-0 ps-0 @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" required placeholder="info@company.ly">
                                </div>
                                <small class="text-muted"><i class="fas fa-info-circle me-1"></i>سيُستخدم هذا البريد لتسجيل الدخول واستلام الإشعارات</small>
                                @error('email')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- رقم الهاتف -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label-modern">رقم هاتف الشركة <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone text-muted"></i></span>
                                    <input type="text" class="form-control-modern border-start-0 ps-0 @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}" required placeholder="رقم هاتف الشركة">
                                </div>
                                @error('phone')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- المدينة -->
                            <div class="col-md-4">
                                <label for="city" class="form-label-modern">المدينة</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-city text-muted"></i></span>
                                    <input type="text" class="form-control-modern border-start-0 ps-0" 
                                           id="city" name="city" value="{{ old('city', 'طرابلس') }}" placeholder="طرابلس">
                                </div>
                            </div>

                            <!-- العنوان -->
                            <div class="col-md-8">
                                <label for="address" class="form-label-modern">العنوان التفصيلي <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                    <input type="text" class="form-control-modern border-start-0 ps-0 @error('address') is-invalid @enderror" 
                                           id="address" name="address" value="{{ old('address') }}" required placeholder="الشارع، المنطقة، أقرب نقطة دالة">
                                </div>
                                @error('address')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- الموقع الإلكتروني -->
                            <div class="col-md-6">
                                <label for="website" class="form-label-modern">الموقع الإلكتروني</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-globe text-muted"></i></span>
                                    <input type="url" class="form-control-modern border-start-0 ps-0 @error('website') is-invalid @enderror" 
                                           id="website" name="website" value="{{ old('website') }}" placeholder="https://example.ly">
                                </div>
                            </div>

                            <!-- شعار الشركة -->
                            <div class="col-md-6">
                                <label for="logo" class="form-label-modern">شعار الشركة (اختياري)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-image text-muted"></i></span>
                                    <input type="file" class="form-control-modern border-start-0 ps-0 @error('logo') is-invalid @enderror" 
                                           id="logo" name="logo" accept="image/*">
                                </div>
                                <div class="form-text mt-1">PNG, JPG, WEBP بحد أقصى 2MB</div>
                                @error('logo')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- الوصف -->
                            <div class="col-12">
                                <label for="description" class="form-label-modern">نبذة تعريفية عن الشركة</label>
                                <textarea class="form-control-modern @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3" 
                                          placeholder="اكتب نبذة تعريفية عن أنشطة الشركة ورؤيتها...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- =================== القسم الثاني: مسؤول الاتصال =================== --}}
                        <hr class="my-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:linear-gradient(135deg,#059669,#10b981);font-size:.9rem;"><i class="fas fa-id-card text-white"></i></span>
                            <h6 class="fw-bold text-dark mb-0">بيانات مسؤول الاتصال</h6>
                        </div>

                        <div class="row g-3">
                            <!-- اسم مسؤول الاتصال -->
                            <div class="col-md-6">
                                <label for="contact_person" class="form-label-modern">اسم مسؤول الاتصال <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                                    <input type="text" class="form-control-modern border-start-0 ps-0 @error('contact_person') is-invalid @enderror" 
                                           id="contact_person" name="contact_person" value="{{ old('contact_person') }}"
                                           placeholder="الاسم الثلاثي لممثل الشركة">
                                </div>
                                @error('contact_person')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- المسمى الوظيفي -->
                            <div class="col-md-6">
                                <label for="contact_position" class="form-label-modern">المسمى الوظيفي لمسؤول الاتصال</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-briefcase text-muted"></i></span>
                                    <input type="text" class="form-control-modern border-start-0 ps-0" 
                                           id="contact_position" name="contact_position" value="{{ old('contact_position') }}"
                                           placeholder="مثال: مدير الموارد البشرية">
                                </div>
                            </div>

                            <!-- هاتف مسؤول الاتصال -->
                            <div class="col-md-6">
                                <label for="contact_phone" class="form-label-modern">رقم هاتف مسؤول الاتصال <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-mobile-alt text-muted"></i></span>
                                    <input type="tel" class="form-control-modern border-start-0 ps-0 @error('contact_phone') is-invalid @enderror" 
                                           id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}"
                                           placeholder="09XXXXXXXX">
                                </div>
                                @error('contact_phone')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- بريد مسؤول الاتصال -->
                            <div class="col-md-6">
                                <label for="contact_email" class="form-label-modern">البريد الإلكتروني لمسؤول الاتصال</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-at text-muted"></i></span>
                                    <input type="email" class="form-control-modern border-start-0 ps-0" 
                                           id="contact_email" name="contact_email" value="{{ old('contact_email') }}"
                                           placeholder="hr@company.ly">
                                </div>
                            </div>
                        </div>

                        {{-- =================== القسم الثالث: حساب الدخول =================== --}}
                        <hr class="my-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:linear-gradient(135deg,#f59e0b,#f97316);font-size:.9rem;"><i class="fas fa-key text-white"></i></span>
                            <h6 class="fw-bold text-dark mb-0">بيانات حساب تسجيل الدخول (اختياري)</h6>
                        </div>

                        <div class="row g-3">
                            <!-- كلمة المرور -->
                            <div class="col-md-6">
                                <label for="password" class="form-label-modern">كلمة المرور</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                    <input type="password" class="form-control-modern border-start-0 ps-0 @error('password') is-invalid @enderror" 
                                           id="password" name="password" minlength="8" placeholder="اتركها فارغة إذا لم ترغب بإنشاء حساب فوري">
                                </div>
                                @error('password')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- تأكيد كلمة المرور -->
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label-modern">تأكيد كلمة المرور</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-check-double text-muted"></i></span>
                                    <input type="password" class="form-control-modern border-start-0 ps-0" 
                                           id="password_confirmation" name="password_confirmation" placeholder="أعد كتابة كلمة المرور">
                                </div>
                            </div>
                        </div>

                        {{-- =================== القسم الرابع: تفاصيل الشراكة =================== --}}
                        <hr class="my-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:linear-gradient(135deg,#7c3aed,#a855f7);font-size:.9rem;"><i class="fas fa-handshake text-white"></i></span>
                            <h6 class="fw-bold text-dark mb-0">تفاصيل ومجالات الشراكة</h6>
                        </div>

                        <div class="row g-3">
                            <!-- نوع الشراكة -->
                            <div class="col-12 mb-1">
                                <label class="form-label-modern mb-2">
                                    أنواع الشراكة <span class="text-danger">*</span>
                                    <small class="text-muted fw-normal me-2">(يمكن تحديد أكثر من نوع شراكة معاً لنفس الشركة)</small>
                                </label>
                                @php
                                    $typesOptions = [
                                        'employment' => ['label' => 'توظيف الخريجين', 'icon' => 'fa-user-tie', 'color' => '#0284c7', 'bg' => '#e0f2fe'],
                                        'training' => ['label' => 'تدريب ميداني وتأهيل', 'icon' => 'fa-graduation-cap', 'color' => '#059669', 'bg' => '#d1fae5'],
                                        'logistic_support' => ['label' => 'دعم لوجستي ورعاية', 'icon' => 'fa-award', 'color' => '#d97706', 'bg' => '#fef3c7'],
                                        'academic' => ['label' => 'تعاون أكاديمي وبحثي', 'icon' => 'fa-flask', 'color' => '#7c3aed', 'bg' => '#ede9fe'],
                                        'workshops' => ['label' => 'ورش عمل وندوات', 'icon' => 'fa-chalkboard-teacher', 'color' => '#db2777', 'bg' => '#fce7f3'],
                                    ];
                                    $selectedTypes = (array) old('partnership_types', ['employment']);
                                @endphp
                                <div class="row g-2">
                                    @foreach($typesOptions as $key => $opt)
                                    <div class="col-md-4 col-sm-6">
                                        <label class="d-flex align-items-center gap-2 border rounded-3 bg-white shadow-sm" style="cursor:pointer;padding:10px 12px;transition:box-shadow .2s;">
                                            <input type="checkbox" name="partnership_types[]" value="{{ $key }}" class="form-check-input mt-0 flex-shrink-0" {{ in_array($key, $selectedTypes) ? 'checked' : '' }}>
                                            <span class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:28px;height:28px;background:{{ $opt['bg'] }};color:{{ $opt['color'] }};font-size:.75rem;">
                                                <i class="fas {{ $opt['icon'] }}"></i>
                                            </span>
                                            <span class="fw-semibold text-dark small">{{ $opt['label'] }}</span>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                @error('partnership_types')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- حالة الشراكة -->
                            <div class="col-md-4">
                                <label for="partnership_status" class="form-label-modern">حالة الشراكة <span class="text-danger">*</span></label>
                                <select class="form-select @error('partnership_status') is-invalid @enderror" 
                                        id="partnership_status" name="partnership_status" required>
                                    <option value="">اختر حالة الشراكة...</option>
                                    <option value="active" {{ old('partnership_status') == 'active' ? 'selected' : '' }}>🟢 نشطة ومعتمدة</option>
                                    <option value="under_review" {{ old('partnership_status', 'under_review') == 'under_review' ? 'selected' : '' }}>⏳ قيد المراجعة</option>
                                    <option value="expired" {{ old('partnership_status') == 'expired' ? 'selected' : '' }}>⚪ منتهية</option>
                                </select>
                                @error('partnership_status')
                                    <div class="text-danger small mt-1 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- تاريخ بداية الشراكة -->
                            <div class="col-md-4">
                                <label for="partnership_start_date" class="form-label-modern">تاريخ بداية الشراكة</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-calendar-alt text-muted"></i></span>
                                    <input type="date" class="form-control-modern border-start-0 ps-0" 
                                           id="partnership_start_date" name="partnership_start_date" value="{{ old('partnership_start_date') }}">
                                </div>
                            </div>

                            <!-- تاريخ نهاية الشراكة -->
                            <div class="col-md-4">
                                <label for="partnership_end_date" class="form-label-modern">تاريخ نهاية الشراكة</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-calendar-times text-muted"></i></span>
                                    <input type="date" class="form-control-modern border-start-0 ps-0" 
                                           id="partnership_end_date" name="partnership_end_date" value="{{ old('partnership_end_date') }}">
                                </div>
                            </div>

                            <!-- ملاحظات الشراكة -->
                            <div class="col-12">
                                <label for="partnership_notes" class="form-label-modern">ملاحظات الشراكة</label>
                                <textarea class="form-control-modern" id="partnership_notes" name="partnership_notes" rows="2"
                                          placeholder="أي ملاحظات خاصة بالشراكة مع هذه الشركة...">{{ old('partnership_notes') }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('partnership.companies') }}" class="btn btn-secondary px-4 py-2 rounded-pill">
                                <i class="fas fa-times me-2"></i>إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                                <i class="fas fa-save me-2"></i>حفظ الشركة
                            </button>
                        </div>
                    </form>
    </x-bento-form>
</div>
@endsection
