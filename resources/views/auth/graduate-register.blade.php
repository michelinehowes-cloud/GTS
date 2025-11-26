@extends('layouts.app')

@section('title', 'تسجيل خريج جديد')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h3 class="mb-0 font-weight-bold">تسجيل خريج جديد</h3>
                        <p class="mb-0 mt-2 text-white-50">انضم إلينا للاستفادة من خدمات التدريب والتوظيف</p>
                    </div>
                    <div class="card-body p-5">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('graduate.register.store') }}">
                            @csrf

                            <h5 class="text-primary mb-4 border-bottom pb-2">
                                <i class="fas fa-user me-2"></i>
                                البيانات الشخصية
                            </h5>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">الاسم الرباعي <span
                                            class="text-danger">*</span></label>
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                                        placeholder="الاسم كما هو في الهوية">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="national_id" class="form-label">رقم القيد <span
                                            class="text-danger">*</span></label>
                                    <input id="national_id" type="text"
                                        class="form-control @error('national_id') is-invalid @enderror" name="national_id"
                                        value="{{ old('national_id') }}" required placeholder="رقم القيد بالجامعة">
                                    @error('national_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="email" class="form-label">البريد الإلكتروني <span
                                            class="text-danger">*</span></label>
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                        name="email" value="{{ old('email') }}" required autocomplete="email"
                                        placeholder="example@domain.com">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">رقم الهاتف <span
                                            class="text-danger">*</span></label>
                                    <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror"
                                        name="phone" value="{{ old('phone') }}" required placeholder="09xxxxxxxx">
                                    @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">كلمة المرور <span
                                            class="text-danger">*</span></label>
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="new-password">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password-confirm" class="form-label">تأكيد كلمة المرور <span
                                            class="text-danger">*</span></label>
                                    <input id="password-confirm" type="password" class="form-control"
                                        name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="date_of_birth" class="form-label">تاريخ الميلاد <span
                                            class="text-danger">*</span></label>
                                    <input id="date_of_birth" type="date"
                                        class="form-control @error('date_of_birth') is-invalid @enderror"
                                        name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                                    @error('date_of_birth')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="gender" class="form-label">الجنس <span class="text-danger">*</span></label>
                                    <select id="gender" class="form-select @error('gender') is-invalid @enderror"
                                        name="gender" required>
                                        <option value="">اختر الجنس</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                                    </select>
                                    @error('gender')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="city" class="form-label">المدينة <span class="text-danger">*</span></label>
                                    <input id="city" type="text" class="form-control @error('city') is-invalid @enderror"
                                        name="city" value="{{ old('city') }}" required>
                                    @error('city')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="address" class="form-label">العنوان بالتفصيل <span
                                            class="text-danger">*</span></label>
                                    <input id="address" type="text"
                                        class="form-control @error('address') is-invalid @enderror" name="address"
                                        value="{{ old('address') }}" required>
                                    @error('address')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <h5 class="text-primary mb-4 mt-5 border-bottom pb-2">
                                <i class="fas fa-graduation-cap me-2"></i>
                                البيانات الأكاديمية
                            </h5>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="university" class="form-label">الجامعة <span
                                            class="text-danger">*</span></label>
                                    <input id="university" type="text"
                                        class="form-control @error('university') is-invalid @enderror" name="university"
                                        value="{{ old('university') }}" required>
                                    @error('university')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="qualification" class="form-label">المؤهل العلمي <span
                                            class="text-danger">*</span></label>
                                    <input id="qualification" type="text"
                                        class="form-control @error('qualification') is-invalid @enderror"
                                        name="qualification" value="{{ old('qualification') }}" required
                                        placeholder="بكالوريوس، ليسانس...">
                                    @error('qualification')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="specialization" class="form-label">التخصص <span
                                            class="text-danger">*</span></label>
                                    <input id="specialization" type="text"
                                        class="form-control @error('specialization') is-invalid @enderror"
                                        name="specialization" value="{{ old('specialization') }}" required>
                                    @error('specialization')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="graduation_year" class="form-label">سنة التخرج <span
                                            class="text-danger">*</span></label>
                                    <input id="graduation_year" type="number"
                                        class="form-control @error('graduation_year') is-invalid @enderror"
                                        name="graduation_year" value="{{ old('graduation_year') }}" required min="1950"
                                        max="{{ date('Y') + 1 }}">
                                    @error('graduation_year')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="gpa" class="form-label">المعدل التراكمي</label>
                                    <input id="gpa" type="number" step="0.01"
                                        class="form-control @error('gpa') is-invalid @enderror" name="gpa"
                                        value="{{ old('gpa') }}" min="0" max="4" placeholder="من 4.00">
                                    @error('gpa')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0 mt-4">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-user-plus me-2"></i>
                                        تسجيل حساب جديد
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center py-3 bg-light">
                        <p class="mb-0">لديك حساب بالفعل؟ <a href="{{ route('login') }}" class="text-primary fw-bold">تسجيل
                                الدخول</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection