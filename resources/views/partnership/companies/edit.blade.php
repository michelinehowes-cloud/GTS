@extends('layouts.app')

@section('title', 'تعديل شركة: ' . $company->name . ' - مسؤول الشراكات')

@section('content')
<div class="container-fluid py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger mb-4">
        <h6>يوجد أخطاء في البيانات:</h6>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <x-bento-form title="تعديل بيانات الشركة: {{ $company->name }}" subtitle="تحديث بيانات المؤسسة أو الشركة الشريكة ومسؤول الاتصال" icon="fa-building" :backRoute="route('partnership.companies')">
        <form action="{{ route('partnership.companies.update', $company->id) }}" method="POST" id="companyEditForm">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $company->name) }}" 
                                       placeholder="أدخل اسم الشركة" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $company->email) }}" 
                                       placeholder="example@company.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $company->phone) }}" 
                                       placeholder="0912345678" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="industry" class="form-label">المجال الصناعي <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('industry') is-invalid @enderror" 
                                       id="industry" name="industry" value="{{ old('industry', $company->industry) }}" 
                                       placeholder="مثل: تكنولوجيا، تسويق، تعليم..." required>
                                @error('industry')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">العنوان <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                   id="address" name="address" value="{{ old('address', $company->address) }}" 
                                   placeholder="العنوان الكامل للشركة" required>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="website" class="form-label">الموقع الإلكتروني</label>
                                <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                       id="website" name="website" value="{{ old('website', $company->website) }}" 
                                       placeholder="https://example.com">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="partnership_type" class="form-label">نوع الشراكة <span class="text-danger">*</span></label>
                                <select name="partnership_type" id="partnership_type" class="form-select @error('partnership_type') is-invalid @enderror" required>
                                    <option value="">اختر نوع الشراكة</option>
                                    @php $currentType = old('partnership_type', $company->partnership_type); @endphp
                                    <option value="employment" {{ $currentType == 'employment' ? 'selected' : '' }}>توظيف</option>
                                    <option value="training" {{ $currentType == 'training' ? 'selected' : '' }}>تدريب</option>
                                    <option value="logistic_support" {{ $currentType == 'logistic_support' ? 'selected' : '' }}>دعم لوجستي</option>
                                    <option value="academic" {{ $currentType == 'academic' ? 'selected' : '' }}>أكاديمي</option>
                                    <option value="training_employment" {{ $currentType == 'training_employment' ? 'selected' : '' }}>تدريب + توظيف</option>
                                </select>
                                @error('partnership_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="partnership_status" class="form-label">حالة الشراكة <span class="text-danger">*</span></label>
                                <select name="partnership_status" id="partnership_status" class="form-select @error('partnership_status') is-invalid @enderror" required>
                                    @php $currentStatus = old('partnership_status', $company->partnership_status ?? 'active'); @endphp
                                    <option value="active" {{ $currentStatus == 'active' ? 'selected' : '' }}>نشطة</option>
                                    <option value="expired" {{ $currentStatus == 'expired' ? 'selected' : '' }}>منتهية</option>
                                    <option value="under_review" {{ $currentStatus == 'under_review' ? 'selected' : '' }}>قيد المراجعة</option>
                                </select>
                                @error('partnership_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="partnership_start_date" class="form-label">تاريخ بدء الشراكة</label>
                                <input type="date" class="form-control @error('partnership_start_date') is-invalid @enderror" 
                                       id="partnership_start_date" name="partnership_start_date" 
                                       value="{{ old('partnership_start_date', $company->partnership_start_date ? \Carbon\Carbon::parse($company->partnership_start_date)->format('Y-m-d') : '') }}">
                                @error('partnership_start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="partnership_end_date" class="form-label">تاريخ انتهاء الشراكة</label>
                                <input type="date" class="form-control @error('partnership_end_date') is-invalid @enderror" 
                                       id="partnership_end_date" name="partnership_end_date" 
                                       value="{{ old('partnership_end_date', $company->partnership_end_date ? \Carbon\Carbon::parse($company->partnership_end_date)->format('Y-m-d') : '') }}">
                                @error('partnership_end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">وصف الشركة</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="وصف مختصر عن نشاط الشركة وخبراتها...">{{ old('description', $company->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- معلومات شخص الاتصال -->
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-user-tie me-2"></i>معلومات شخص الاتصال</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="contact_person" class="form-label">اسم شخص الاتصال</label>
                                        <input type="text" class="form-control" 
                                               id="contact_person" name="contact_person" 
                                               value="{{ old('contact_person', $company->contact_person) }}" 
                                               placeholder="اسم شخص الاتصال في الشركة">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="contact_position" class="form-label">المنصب</label>
                                        <input type="text" class="form-control" 
                                               id="contact_position" name="contact_position" 
                                               value="{{ old('contact_position', $company->contact_position) }}" 
                                               placeholder="منصب شخص الاتصال">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="contact_phone" class="form-label">هاتف الاتصال</label>
                                        <input type="text" class="form-control" 
                                               id="contact_phone" name="contact_phone" 
                                               value="{{ old('contact_phone', $company->contact_phone) }}" 
                                               placeholder="هاتف شخص الاتصال">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="contact_email" class="form-label">بريد الاتصال</label>
                                        <input type="email" class="form-control" 
                                               id="contact_email" name="contact_email" 
                                               value="{{ old('contact_email', $company->contact_email) }}" 
                                               placeholder="بريد شخص الاتصال">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('partnership.companies') }}" class="btn btn-secondary px-4 py-2 rounded-pill">
                                <i class="fas fa-times me-2"></i>إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm" id="submitBtn">
                                <i class="fas fa-save me-2"></i>حفظ التعديلات
                            </button>
                        </div>
                    </form>
    </x-bento-form>
</div>
@endsection
