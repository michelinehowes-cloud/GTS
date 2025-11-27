@extends('layouts.app')

@section('title', 'الملف الشخصي')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-header mb-4">
                    <h1 class="page-title">
                        <i class="fas fa-user-circle me-2"></i>
                        الملف الشخصي
                    </h1>
                    <p class="text-muted">إدارة بياناتك الشخصية وإعدادات الحساب</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- البيانات الشخصية -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user-edit me-2"></i>
                            البيانات الشخصية
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('graduate.profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- الاسم الكامل -->
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">الاسم الكامل <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                        name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- البريد الإلكتروني -->
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">البريد الإلكتروني <span
                                            class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                        name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- رقم الهاتف -->
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">رقم الهاتف</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                        @enderror </div>

                                    <!-- تاريخ الميلاد -->
                                    <div class="col-md-6 mb-3">
                                        <label for="date_of_birth" class="form-label">تاريخ الميلاد</label>
                                        <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                            id="date_of_birth" name="date_of_birth"
                                            value="{{ old('date_of_birth', $user->date_of_birth) }}">
                                        @error('date_of_birth')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- الجنس -->
                                    <div class="col-md-6 mb-3">
                                        <label for="gender" class="form-label">الجنس</label>
                                        <select class="form-select @error('gender') is-invalid @enderror" id="gender"
                                            name="gender">
                                            <option value="">-- اختر --</option>
                                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>
                                                ذكر</option>
                                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>أنثى</option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- المدينة -->
                                    <div class="col-md-6 mb-3">
                                        <label for="city" class="form-label">المدينة</label>
                                        <input type="text" class="form-control @error('city') is-invalid @enderror"
                                            id="city" name="city" value="{{ old('city', $user->city) }}">
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- العنوان -->
                                    <div class="col-md-6 mb-3">
                                        <label for="address" class="form-label">العنوان</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" id="address"
                                            name="address" rows="2">{{ old('address', $user->address) }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- المؤهل العلمي -->
                                    <div class="col-md-6 mb-3">
                                        <label for="qualification" class="form-label">المؤهل العلمي</label>
                                        <input type="text" class="form-control @error('qualification') is-invalid @enderror"
                                            id="qualification" name="qualification"
                                            value="{{ old('qualification', $user->qualification) }}"
                                            placeholder="بكالوريوس، ماجستير، إلخ...">
                                        @error('qualification')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- التخصص -->
                                    <div class="col-md-6 mb-3">
                                        <label for="specialization" class="form-label">التخصص</label>
                                        <input type="text"
                                            class="form-control @error('specialization') is-invalid @enderror"
                                            id="specialization" name="specialization"
                                            value="{{ old('specialization', $user->specialization) }}">
                                        @error('specialization')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- الجامعة -->
                                    <div class="col-md-6 mb-3">
                                        <label for="university" class="form-label">الجامعة</label>
                                        <input type="text" class="form-control @error('university') is-invalid @enderror"
                                            id="university" name="university"
                                            value="{{ old('university', $user->university) }}">
                                        @error('university')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- سنة التخرج -->
                                    <div class="col-md-3 mb-3">
                                        <label for="graduation_year" class="form-label">سنة التخرج</label>
                                        <input type="number"
                                            class="form-control @error('graduation_year') is-invalid @enderror"
                                            id="graduation_year" name="graduation_year"
                                            value="{{ old('graduation_year', $user->graduation_year) }}" min="1950"
                                            max="{{ date('Y') + 1 }}">
                                        @error('graduation_year')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- المعدل التراكمي -->
                                    <div class="col-md-3 mb-3">
                                        <label for="gpa" class="form-label">المعدل التراكمي</label>
                                        <input type="number" class="form-control @error('gpa') is-invalid @enderror"
                                            id="gpa" name="gpa" value="{{ old('gpa', $user->gpa) }}" min="0" max="4"
                                            step="0.01" placeholder="من 4.00">
                                        @error('gpa')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>
                                        حفظ التغييرات
                                    </button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- تغيير كلمة المرور -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-key me-2"></i>
                            تغيير كلمة المرور
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('graduate.password.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="current_password" class="form-label">كلمة المرور الحالية <span
                                        class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                    id="current_password" name="current_password" required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">كلمة المرور الجديدة <span
                                        class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" required minlength="8">
                                <small class="text-muted">يجب أن تكون 8 أحرف على الأقل</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">تأكيد كلمة المرور <span
                                        class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" required>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-warning text-white">
                                    <i class="fas fa-lock me-2"></i>
                                    تحديث كلمة المرور
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- معلومات الحساب -->
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            معلومات الحساب
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <strong>نوع الحساب:</strong>
                                <span class="badge bg-success">خريج</span>
                            </li>
                            <li class="mb-2">
                                <strong>تاريخ الإنشاء:</strong><br>
                                {{ $user->created_at->format('Y-m-d') }}
                            </li>
                            <li>
                                <strong>آخر تحديث:</strong><br>
                                {{ $user->updated_at->format('Y-m-d') }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection