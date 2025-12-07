@extends('layouts.app')

@section('title', 'إضافة خريج جديد')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>إضافة خريج جديد</h3>
                        <a href="{{ route('career-guidance.graduates') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('career-guidance.graduates.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">الاسم الكامل *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                        name="name" value="{{ old('name') }}" required
                                        placeholder="أدخل الاسم الكامل للخريج">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">البريد الإلكتروني *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                        name="email" value="{{ old('email') }}" required placeholder="example@email.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">رقم الهاتف</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                        name="phone" value="{{ old('phone') }}" placeholder="+218 XXX XXX XXX">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="national_id" class="form-label">الرقم الوطني</label>
                                    <input type="text" class="form-control @error('national_id') is-invalid @enderror"
                                        id="national_id" name="national_id" value="{{ old('national_id') }}"
                                        placeholder="الرقم الوطني (اختياري)">
                                    @error('national_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="university" class="form-label">الجامعة *</label>
                                    <select class="form-select @error('university') is-invalid @enderror" id="university"
                                        name="university" required>
                                        <option value="">اختر الجامعة</option>
                                        <option value="جامعة طرابلس" {{ old('university') == 'جامعة طرابلس' ? 'selected' : '' }}>جامعة طرابلس</option>
                                    </select>
                                    @error('university')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="sector" class="form-label">القطاع *</label>
                                    <select class="form-select @error('sector') is-invalid @enderror" id="sector"
                                        name="sector" required disabled>
                                        <option value="">اختر القطاع</option>
                                    </select>
                                    <input type="hidden" id="old_sector" value="{{ old('sector') }}">
                                    @error('sector')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="faculty" class="form-label">الكلية *</label>
                                    <select class="form-select @error('faculty') is-invalid @enderror" id="faculty"
                                        name="faculty" required disabled>
                                        <option value="">اختر الكلية</option>
                                    </select>
                                    <input type="hidden" id="old_faculty" value="{{ old('faculty') }}">
                                    @error('faculty')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="major" class="form-label">التخصص *</label>
                                    <select class="form-select @error('major') is-invalid @enderror" id="specialization"
                                        name="major" required disabled>
                                        <option value="">اختر التخصص</option>
                                    </select>
                                    <input type="hidden" id="old_specialization" value="{{ old('major') }}">
                                    @error('major')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="graduation_year" class="form-label">سنة التخرج *</label>
                                    <select class="form-select @error('graduation_year') is-invalid @enderror"
                                        id="graduation_year" name="graduation_year" required>
                                        <option value="">اختر سنة التخرج</option>
                                        @for($year = date('Y'); $year >= 2000; $year--)
                                            <option value="{{ $year }}" {{ old('graduation_year') == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('graduation_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="gpa" class="form-label">المعدل التراكمي</label>
                                    <input type="number" step="0.01" min="0" max="4"
                                        class="form-control @error('gpa') is-invalid @enderror" id="gpa" name="gpa"
                                        value="{{ old('gpa') }}" placeholder="مثال: 3.75">
                                    @error('gpa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror>
                                    <small class="text-muted">من 0 إلى 4 (اختياري)</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="employment_status" class="form-label">حالة التوظيف *</label>
                                    <select class="form-select @error('employment_status') is-invalid @enderror"
                                        id="employment_status" name="employment_status" required>
                                        <option value="">اختر الحالة</option>
                                        <option value="employed" {{ old('employment_status') == 'employed' ? 'selected' : '' }}>موظف</option>
                                        <option value="unemployed" {{ old('employment_status') == 'unemployed' ? 'selected' : '' }}>غير موظف</option>
                                        <option value="seeking_opportunities" {{ old('employment_status') == 'seeking_opportunities' ? 'selected' : '' }}>باحث عن
                                            عمل</option>
                                        <option value="continuing_education" {{ old('employment_status') == 'continuing_education' ? 'selected' : '' }}>مستكمل
                                            للدراسة</option>
                                    </select>
                                    @error('employment_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="skills" class="form-label">المهارات (افصل بينها بفواصل)</label>
                                    <input type="text" class="form-control @error('skills') is-invalid @enderror"
                                        id="skills" name="skills" value="{{ old('skills') }}"
                                        placeholder="مثال: PHP, Laravel, JavaScript, إدارة المشاريع">
                                    @error('skills')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">أدخل المهارات مفصولة بفواصل - سيتم تحويلها تلقائياً إلى
                                        قائمة</small>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="languages" class="form-label">اللغات (افصل بينها بفواصل)</label>
                                    <input type="text" class="form-control @error('languages') is-invalid @enderror"
                                        id="languages" name="languages" value="{{ old('languages') }}"
                                        placeholder="مثال: العربية, الإنجليزية, الفرنسية">
                                    @error('languages')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="work_experience" class="form-label">الخبرات العملية</label>
                                    <textarea class="form-control @error('work_experience') is-invalid @enderror"
                                        id="work_experience" name="work_experience" rows="3"
                                        placeholder="صف الخبرات العملية السابقة للخريج...">{{ old('work_experience') }}</textarea>
                                    @error('work_experience')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="address" class="form-label">العنوان</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" id="address"
                                        name="address" rows="2" placeholder="عنوان السكن...">{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="notes" class="form-label">ملاحظات إضافية</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes"
                                        name="notes" rows="3"
                                        placeholder="أي ملاحظات إضافية عن الخريج...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- بطاقة الملخص -->
                            <div class="card border-info mb-4">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        ملاحظات حول إضافة الخريج
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <ul class="mb-0">
                                        <li>الحقول marked بـ (*) إلزامية</li>
                                        <li>سيتم إضافة الخريج إلى قاعدة البيانات فور الضغط على حفظ</li>
                                        <li>يمكنك تعديل البيانات لاحقاً من صفحة تفاصيل الخريج</li>
                                        <li>المهارات واللغات سيتم حفظها كقوائم تلقائياً</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('career-guidance.graduates') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"> إلغاء</i>
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-user-plus me-2"></i>إضافة الخريج
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
        <!-- University Data Script -->
        <script src="{{ asset('js/university-data.js') }}"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // إضافة نصائح تفاعلية
                const employmentStatus = document.getElementById('employment_status');
                const skillsInput = document.getElementById('skills');

                if (employmentStatus) {
                    employmentStatus.addEventListener('change', function () {
                        // يمكنك إضافة منطق إضافي هنا إذا لزم الأمر
                        console.log('حالة التوظيف المحددة:', this.value);
                    });
                }

                if (skillsInput) {
                    skillsInput.addEventListener('input', function () {
                        // التحقق من أن المستخدم يستخدم الفواصل بشكل صحيح
                        const skills = this.value.split(',').map(skill => skill.trim());
                        console.log('المهارات المدخلة:', skills);
                    });
                }
            });
        </script>
    @endsection
@endsection