@extends('layouts.app')

@section('title', 'الملف التعريفي للشركة')
@section('page-title', 'الملف التعريفي للشركة')

@section('content')
<div class="container-fluid px-2 px-md-3 py-3">
    <!-- الشريط الترحيبي المعتمد بنمط المنصة الموحد -->
    <x-page-hero
        title="الملف التعريفي للشركة"
        subtitle="إدارة بيانات الشركة ومجال العمل، والشعار، وأمان الحساب وكلمة المرور"
        icon="fas fa-building"
        :breadcrumbs="[
            ['label' => 'لوحة تحكم الشركة', 'url' => route('company.dashboard')],
            ['label' => 'الملف التعريفي للشركة']
        ]"
    >
        <a href="{{ route('company.dashboard') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-home fs-6"></i>
            <span>لوحة التحكم</span>
        </a>
        <a href="{{ route('job-opportunities.index') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-briefcase fs-6"></i>
            <span>إدارة الشواغر</span>
        </a>
    </x-page-hero>

    <div class="profile-container">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-2 py-3 px-4">
                <i class="fas fa-check-circle fs-4 text-success"></i>
                <div class="fw-bold">{{ session('success') }}</div>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 py-3 px-4">
                <div class="fw-bold mb-2 d-flex align-items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-danger fs-5"></i>
                    <span>يرجى مراجعة وتصحيح الملاحظات التالية:</span>
                </div>
                <ul class="mb-0 small pe-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $partnershipLabels = [
                'employment' => 'شراكة توظيف',
                'training' => 'شراكة تدريب ميداني',
                'logistic_support' => 'دعم لوجستي ورعاية',
                'academic' => 'تعاون أكاديمي',
                'training_employment' => 'تدريب وتوظيف معتمد',
            ];
            $partnershipLabel = $partnershipLabels[$company->partnership_type] ?? 'شراكة معتمدة';
            $logoUrl = $company->logo_path ? \Illuminate\Support\Facades\Storage::url($company->logo_path) : null;
        @endphp

        <!-- نموذج تعديل بيانات الشركة والشعار -->
        <form action="{{ route('company.profile.update') }}" method="POST" enctype="multipart/form-data" id="company-profile-form">
            @csrf
            @method('PUT')

            <!-- كرت الهوية والترويسة العلوية للشركة -->
            <div class="company-header-card mb-4">
                <div class="cover-banner">
                    <div class="cover-overlay-pattern"></div>
                </div>

                <div class="profile-meta-bar">
                    <div class="d-flex gap-3 align-items-end flex-wrap">
                        <div class="logo-wrapper">
                            @if($logoUrl)
                                <img src="{{ $logoUrl }}" alt="{{ $company->name }}" class="logo-img" id="company-logo-preview">
                            @else
                                <div class="logo-img logo-placeholder d-flex align-items-center justify-content-center" id="company-logo-placeholder">
                                    <span>{{ mb_substr($company->name, 0, 1) }}</span>
                                </div>
                                <img src="" alt="{{ $company->name }}" class="logo-img d-none" id="company-logo-preview">
                            @endif

                            <label for="upload-logo" class="upload-btn" title="تغيير الشعار">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" name="logo" id="upload-logo" accept="image/png, image/jpeg, image/jpg, image/webp" style="display: none;">
                        </div>

                        <div class="company-info">
                            <h2>{{ $company->name }}</h2>
                            <p>
                                <i class="fas fa-building text-warning"></i>
                                <span>{{ $company->industry ?? 'قطاع الأعمال والخدمات' }}</span>
                                @if($company->city || $company->address)
                                    <span class="mx-1">•</span>
                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                    <span>{{ $company->city ?? Str::limit($company->address, 25) }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="partnership-badges">
                        <span class="badge badge-primary">
                            <i class="fas fa-handshake"></i>
                            <span>{{ $partnershipLabel }}</span>
                        </span>

                        @if($company->partnership_status === 'active')
                            <span class="badge badge-success">
                                <i class="fas fa-circle-check"></i>
                                <span>نشطة ومعتمدة</span>
                            </span>
                        @elseif($company->partnership_status === 'expired')
                            <span class="badge badge-danger">
                                <i class="fas fa-clock-rotate-left"></i>
                                <span>منتهية</span>
                            </span>
                        @else
                            <span class="badge badge-warning">
                                <i class="fas fa-hourglass-half"></i>
                                <span>قيد المراجعة</span>
                            </span>
                        @endif
                    </div>
                </div>

                <div id="new-logo-notice" class="px-4 pb-3 text-success small fw-bold d-none">
                    <i class="fas fa-check-circle me-1"></i> تم اختيار شعار جديد للمعاينة، اضغط "حفظ التغييرات" لتطبيقه نهائياً.
                </div>
            </div>

            <!-- كرت النموذج وتحديث البيانات -->
            <div class="form-card mb-4">
                <div class="form-header">
                    <h3><i class="fas fa-pen-to-square text-primary"></i> تحديث بيانات الشركة</h3>
                    <p>قم بتحديث معلومات شركتك لتظهر بشكل احترافي للطلبة والخريجين ومسؤولي التوظيف.</p>
                </div>

                <div class="grid-fields">
                    <div class="field-group">
                        <label for="name">اسم الشركة <span>*</span></label>
                        <input type="text" class="input-box @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $company->name) }}" required>
                        @error('name') <div class="invalid-feedback d-block text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="field-group">
                        <label for="industry">مجال العمل / النشاط <span>*</span></label>
                        <input type="text" class="input-box @error('industry') is-invalid @enderror" id="industry" name="industry" value="{{ old('industry', $company->industry) }}" required placeholder="مثل: البرمجيات، الاتصالات، المقاولات...">
                        @error('industry') <div class="invalid-feedback d-block text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="field-group">
                        <label for="email">البريد الإلكتروني للشركة <span>*</span></label>
                        <input type="email" class="input-box @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $company->user->email ?? $company->email) }}" required>
                        @error('email') <div class="invalid-feedback d-block text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="field-group">
                        <label for="phone">رقم الهاتف <span>*</span></label>
                        <input type="tel" class="input-box @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $company->phone) }}" required placeholder="09XXXXXXXX">
                        @error('phone') <div class="invalid-feedback d-block text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="field-group full-width">
                        <label for="address">عنوان المقر الرئيسي <span>*</span></label>
                        <input type="text" class="input-box @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $company->address) }}" required placeholder="المدينة، الشارع، المعلم البارز...">
                        @error('address') <div class="invalid-feedback d-block text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="field-group full-width">
                        <label for="website">الموقع الإلكتروني</label>
                        <input type="url" class="input-box @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website', $company->website) }}" placeholder="https://example.com">
                        @error('website') <div class="invalid-feedback d-block text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="field-group full-width">
                        <label for="description">نبذة عن الشركة</label>
                        <textarea class="input-box @error('description') is-invalid @enderror" id="description" name="description" placeholder="اكتب نبذة مختصرة عن نشاط الشركة وخدماتها وخبراتها في سوق العمل...">{{ old('description', $company->description) }}</textarea>
                        @error('description') <div class="invalid-feedback d-block text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="actions-bar">
                    <a href="{{ route('company.dashboard') }}" class="btn btn-cancel">
                        <i class="fas fa-times me-1"></i> إلغاء
                    </a>
                    <button type="submit" class="btn btn-save">
                        <i class="fas fa-check me-1"></i> حفظ التغييرات
                    </button>
                </div>
            </div>
        </form>

        <!-- كرت أمان الحساب وتغيير كلمة المرور المعتمد -->
        <div class="form-card mb-4">
            <div class="form-header">
                <h3><i class="fas fa-shield-alt text-warning"></i> أمان الحساب وتغيير كلمة المرور</h3>
                <p>تحديث كلمة المرور لحساب الشركة لتأمين الوصول وحماية الصلاحيات وسجلات المتقدمين.</p>
            </div>

            <form action="{{ route('company.password.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid-fields">
                    <div class="field-group full-width">
                        <label for="current_password">كلمة المرور الحالية <span class="text-danger">*</span></label>
                        <div class="input-with-icon">
                            <input type="password" class="input-box @error('current_password') is-invalid @enderror" 
                                id="current_password" name="current_password" placeholder="أدخل كلمة المرور الحالية للتأكيد..." required>
                            <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility('current_password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password') <div class="invalid-feedback d-block text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="field-group">
                        <label for="new_password">كلمة المرور الجديدة <span class="text-danger">*</span></label>
                        <div class="input-with-icon">
                            <input type="password" class="input-box @error('password') is-invalid @enderror" 
                                id="new_password" name="password" placeholder="8 خانات على الأقل..." required minlength="8">
                            <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility('new_password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password') <div class="invalid-feedback d-block text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="field-group">
                        <label for="new_password_confirmation">تأكيد كلمة المرور الجديدة <span class="text-danger">*</span></label>
                        <div class="input-with-icon">
                            <input type="password" class="input-box" 
                                id="new_password_confirmation" name="password_confirmation" placeholder="أعد إدخال كلمة المرور..." required minlength="8">
                            <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility('new_password_confirmation', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="actions-bar">
                    <button type="submit" class="btn btn-warning-modern">
                        <i class="fas fa-key me-1"></i> تحديث كلمة المرور
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
  :root {
    --primary: #0d3882;
    --primary-hover: #1565c0;
    --bg-surface: #ffffff;
    --bg-page: #f8fafc;
    --border-color: #e2e8f0;
    --text-main: #0f172a;
    --text-muted: #64748b;
    --accent: #2563eb;
  }

  .profile-container {
    max-width: 1000px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  /* كرت الهوية والترويسة العلوية */
  .company-header-card {
    background: var(--bg-surface);
    border-radius: 20px;
    border: 1px solid var(--border-color);
    overflow: hidden;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
  }

  .cover-banner {
    height: 120px;
    background: linear-gradient(135deg, #0d3882 0%, #1e40af 50%, #3b82f6 100%);
    position: relative;
  }

  .cover-overlay-pattern {
    position: absolute;
    inset: 0;
    background-image: radial-gradient(rgba(255, 255, 255, 0.15) 1px, transparent 1px);
    background-size: 16px 16px;
  }

  .profile-meta-bar {
    padding: 0 28px 20px;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    position: relative;
  }

  .logo-wrapper {
    position: relative;
    margin-top: -55px;
    z-index: 3;
  }

  .logo-img {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    border: 4px solid #ffffff;
    background: #ffffff;
    object-fit: cover;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
  }

  .logo-placeholder {
    font-size: 2.8rem;
    font-weight: 800;
    color: var(--primary);
    background: #eff6ff;
  }

  .upload-btn {
    position: absolute;
    bottom: 4px;
    left: 4px;
    background: var(--primary);
    color: #ffffff;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 13px;
    border: 2.5px solid #ffffff;
    transition: all 0.2s ease;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
  }

  .upload-btn:hover {
    background: #f59e0b;
    color: #0b1f3a;
    transform: scale(1.1);
  }

  .company-info h2 {
    font-size: 1.35rem;
    font-weight: 800;
    color: var(--text-main);
    margin: 0 0 4px 0;
  }

  .company-info p {
    font-size: 0.88rem;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 0;
  }

  .partnership-badges {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  .badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 16px;
    border-radius: 20px;
    font-size: 0.82rem;
    font-weight: 700;
    font-family: 'Tajawal', sans-serif;
  }

  .badge-primary { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
  .badge-success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
  .badge-warning { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
  .badge-danger { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

  /* كرت النموذج وتحديث البيانات */
  .form-card {
    background: var(--bg-surface);
    border-radius: 20px;
    border: 1px solid var(--border-color);
    padding: 28px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
  }

  .form-header {
    margin-bottom: 24px;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 16px;
  }

  .form-header h3 {
    font-size: 1.2rem;
    font-weight: 800;
    color: var(--text-main);
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
  }

  .form-header p {
    font-size: 0.88rem;
    color: var(--text-muted);
    margin-top: 5px;
    margin-bottom: 0;
  }

  .grid-fields {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
  }

  .full-width {
    grid-column: span 2;
  }

  .field-group {
    display: flex;
    flex-direction: column;
    gap: 7px;
  }

  .field-group label {
    font-size: 0.88rem;
    font-weight: 700;
    color: #334155;
  }

  .field-group label span {
    color: #ef4444;
  }

  .input-box {
    width: 100%;
    padding: 11px 16px;
    border: 1px solid var(--border-color);
    border-radius: 10px;
    background-color: #f8fafc;
    font-size: 0.92rem;
    color: #0f172a;
    transition: all 0.2s ease;
    outline: none;
    font-family: inherit;
  }

  .input-box:focus {
    border-color: var(--primary);
    background-color: #ffffff;
    box-shadow: 0 0 0 3px rgba(13, 56, 130, 0.12);
  }

  textarea.input-box {
    min-height: 110px;
    resize: vertical;
    line-height: 1.6;
  }

  .input-with-icon {
    position: relative;
    display: flex;
    align-items: center;
  }

  .input-with-icon .input-box {
    padding-left: 42px;
  }

  .toggle-password-btn {
    position: absolute;
    left: 12px;
    background: transparent;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    font-size: 0.95rem;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.2s ease;
  }

  .toggle-password-btn:hover {
    color: var(--primary);
  }

  .actions-bar {
    margin-top: 26px;
    padding-top: 18px;
    border-top: 1px solid var(--border-color);
    display: flex;
    justify-content: flex-end;
    gap: 12px;
  }

  .btn {
    padding: 11px 24px;
    border-radius: 50px;
    font-size: 0.92rem;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: 0.25s ease;
    border: none;
    text-decoration: none;
  }

  .btn-save {
    background: linear-gradient(135deg, #0d3882 0%, #1565c0 100%);
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(13, 56, 130, 0.25);
  }

  .btn-save:hover {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: #0b1f3a;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(245, 158, 11, 0.35);
  }

  .btn-warning-modern {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: #0b1f3a;
    box-shadow: 0 4px 14px rgba(245, 158, 11, 0.25);
  }

  .btn-warning-modern:hover {
    background: linear-gradient(135deg, #0d3882 0%, #1565c0 100%);
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(13, 56, 130, 0.3);
  }

  .btn-cancel {
    background: #f1f5f9;
    color: var(--text-muted);
  }

  .btn-cancel:hover {
    background: #e2e8f0;
    color: var(--text-main);
  }

  @media (max-width: 768px) {
    .grid-fields { grid-template-columns: 1fr; }
    .full-width { grid-column: span 1; }
    .profile-meta-bar { flex-direction: column; align-items: flex-start; }
    .actions-bar { justify-content: stretch; }
    .actions-bar .btn { flex: 1; }
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // معاينة الشعار الفورية عند اختيار ملف
    const logoInput = document.getElementById('upload-logo');
    const previewImg = document.getElementById('company-logo-preview');
    const placeholder = document.getElementById('company-logo-placeholder');
    const notice = document.getElementById('new-logo-notice');

    if (logoInput) {
        logoInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    if (previewImg) {
                        previewImg.src = event.target.result;
                        previewImg.classList.remove('d-none');
                    }
                    if (placeholder) {
                        placeholder.classList.add('d-none');
                    }
                    if (notice) {
                        notice.classList.remove('d-none');
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
});

function togglePasswordVisibility(fieldId, btn) {
    const input = document.getElementById(fieldId);
    if (!input) return;
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        if (icon) {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    } else {
        input.type = 'password';
        if (icon) {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
}
</script>
@endsection
