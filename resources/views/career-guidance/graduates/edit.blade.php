@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">تعديل بيانات الخريج: {{ $graduate->name }}</h1>
            <a href="{{ route('career-guidance.graduates.show', $graduate->id) }}"
                class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-arrow-right fa-sm text-white-50"></i> العودة لتفاصيل الخريج
            </a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">نموذج تعديل بيانات الخريج</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('career-guidance.graduates.update', $graduate->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name">الاسم الكامل:</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                            value="{{ old('name', $graduate->name) }}" required>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">البريد الإلكتروني:</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email', $graduate->email) }}">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">رقم الهاتف:</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
                            value="{{ old('phone', $graduate->phone) }}">
                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="major">التخصص:</label>
                        <select class="form-control @error('major') is-invalid @enderror" id="major" name="major" required>
                            <option value="">اختر التخصص</option>
                            @foreach($majors as $major)
                                <option value="{{ $major }}" {{ old('major', $graduate->major) == $major ? 'selected' : '' }}>
                                    {{ $major }}</option>
                            @endforeach
                        </select>
                        @error('major')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="graduation_year">سنة التخرج:</label>
                        <select class="form-control @error('graduation_year') is-invalid @enderror" id="graduation_year"
                            name="graduation_year" required>
                            <option value="">اختر سنة التخرج</option>
                            @foreach($graduationYears as $year)
                                <option value="{{ $year }}" {{ old('graduation_year', $graduate->graduation_year) == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                        @error('graduation_year')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="gpa">المعدل التراكمي (GPA):</label>
                        <input type="number" step="0.01" class="form-control @error('gpa') is-invalid @enderror" id="gpa"
                            name="gpa" value="{{ old('gpa', $graduate->gpa) }}" min="0" max="4">
                        @error('gpa')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="degree">الدرجة العلمية:</label>
                        <select class="form-control @error('degree') is-invalid @enderror" id="degree" name="degree"
                            required>
                            <option value="">اختر الدرجة العلمية</option>
                            @foreach($degrees as $degree)
                                <option value="{{ $degree }}" {{ old('degree', $graduate->degree) == $degree ? 'selected' : '' }}>
                                    {{ $degree }}</option>
                            @endforeach
                        </select>
                        @error('degree')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="skills">المهارات (افصل بينها بفاصلة):</label>
                        <input type="text" class="form-control @error('skills') is-invalid @enderror" id="skills"
                            name="skills"
                            value="{{ old('skills', is_array($graduate->skills) ? implode(',', $graduate->skills) : $graduate->skills) }}">
                        @error('skills')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="languages">اللغات (افصل بينها بفاصلة):</label>
                        <input type="text" class="form-control @error('languages') is-invalid @enderror" id="languages"
                            name="languages"
                            value="{{ old('languages', is_array($graduate->languages) ? implode(',', $graduate->languages) : $graduate->languages) }}">
                        @error('languages')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="employment_status">حالة التوظيف:</label>
                        <select class="form-control @error('employment_status') is-invalid @enderror" id="employment_status"
                            name="employment_status" required>
                            <option value="">اختر حالة التوظيف</option>
                            @php
                                $arabicEmploymentStatuses = [
                                    'employed' => 'موظف',
                                    'seeking_opportunities' => 'باحث عن عمل',
                                    'unemployed' => 'غير موظف',
                                    'further_study' => 'مستكمل للدراسة',
                                ];
                            @endphp
                            @foreach($employmentStatuses as $status)
                                <option value="{{ $status }}" {{ old('employment_status', $graduate->employment_status) == $status ? 'selected' : '' }}>
                                    {{ $arabicEmploymentStatuses[$status] ?? $status }}
                                </option>
                            @endforeach
                        </select>
                        @error('employment_status')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="work_experience">الخبرة العملية:</label>
                        <textarea class="form-control @error('work_experience') is-invalid @enderror" id="work_experience"
                            name="work_experience"
                            rows="3">{{ old('work_experience', $graduate->work_experience) }}</textarea>
                        @error('work_experience')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="address">العنوان:</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address"
                            name="address" value="{{ old('address', $graduate->address) }}">
                        @error('address')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="linkedin_url">رابط لينكدإن:</label>
                        <input type="url" class="form-control @error('linkedin_url') is-invalid @enderror" id="linkedin_url"
                            name="linkedin_url" value="{{ old('linkedin_url', $graduate->linkedin_url) }}">
                        @error('linkedin_url')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="cv">السيرة الذاتية (PDF):</label>
                        @if($graduate->cv_path)
                            <div class="mb-2">
                                <span class="text-success"><i class="fas fa-check-circle"></i> يوجد ملف حالي: </span>
                                <a href="{{ Storage::url($graduate->cv_path) }}" target="_blank">{{ basename($graduate->cv_path) }}</a>
                            </div>
                        @endif
                        <input type="file" class="form-control-file @error('cv') is-invalid @enderror" id="cv" name="cv" accept=".pdf">
                        <small class="form-text text-muted">اترك هذا الحقل فارغاً إذا كنت لا تريد تغيير الملف الحالي. (الحد الأقصى: 5 ميجابايت)</small>
                        @error('cv')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-success btn-icon-split">
                        <span class="icon text-white-50">
                            <i class="fas fa-check"></i>
                        </span>
                        <span class="text">تحديث بيانات الخريج</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection