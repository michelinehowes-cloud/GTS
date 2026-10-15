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

            <!-- كرت النموذج وتحديث البيانات -->
            <div class="form-card mb-4">
                <div class="form-header d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h3><i class="fas fa-pen-to-square text-primary"></i> تحديث بيانات الشركة</h3>
                        <p>قم بتحديث معلومات شركتك لتظهر بشكل احترافي للطلبة والخريجين ومسؤولي التوظيف.</p>
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

                <!-- قسم شعار الشركة مع المعاينة الفورية والتصميم المرتب المتناسق -->
                <div class="logo-upload-card mb-4">
                    <div class="logo-card-inner">
                        <!-- المعاينة الفورية للشعار مع شارة الكاميرا التفاعلية -->
                        <div class="logo-preview-wrapper">
                            <label for="upload-logo" class="logo-avatar-frame" title="اضغط لتغيير الشعار">
                                @if($logoUrl)
                                    <img src="{{ $logoUrl }}" alt="{{ $company->name }}" id="company-logo-preview">
                                @else
                                    <div id="company-logo-placeholder" class="logo-avatar-placeholder">
                                        <span>{{ mb_substr($company->name, 0, 1) }}</span>
                                    </div>
                                    <img src="" alt="{{ $company->name }}" id="company-logo-preview" class="d-none">
                                @endif
                                <div class="logo-edit-badge" title="تغيير الشعار">
                                    <i class="fas fa-camera"></i>
                                </div>
                            </label>
                        </div>

                        <!-- تفاصيل الهوية وإرشادات الملف -->
                        <div class="logo-content-wrapper">
                            <div class="logo-title-group">
                                <label class="logo-main-label">
                                    <i class="fas fa-image text-primary"></i>
                                    <span>شعار الشركة (Logo)</span>
                                </label>
                                <span class="logo-sub-label">يظهر الشعار في الملف التعريفي وبطاقات المعرض وملصقات الأجنحة</span>
                            </div>

                            <div class="logo-meta-tags">
                                <span class="meta-tag"><i class="fas fa-file-image text-primary"></i> صيغ PNG, JPG, WEBP</span>
                                <span class="meta-tag"><i class="fas fa-weight-hanging text-muted"></i> أقصى حجم 2MB</span>
                                <span class="meta-tag"><i class="fas fa-wand-magic-sparkles text-warning"></i> يفضل خلفية شفافة أو بيضاء</span>
                            </div>

                            <div id="new-logo-notice" class="logo-success-notice d-none">
                                <i class="fas fa-check-circle"></i>
                                <span>تم اختيار الشعار الجديد بنجاح، اضغط "حفظ التغييرات" بالأسفل لتطبيقه.</span>
                            </div>
                            @error('logo') <div class="invalid-feedback d-block text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- زر رفع الشعار العصري -->
                        <div class="logo-action-wrapper">
                            <label for="upload-logo" class="btn-upload-trigger">
                                <i class="fas fa-cloud-arrow-up"></i>
                                <span>اختيار شعار جديد</span>
                            </label>
                            <input type="file" name="logo" id="upload-logo" accept="image/png, image/jpeg, image/jpg, image/webp" class="d-none">
                        </div>
                    </div>
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

  /* قسم الشعار والوسوم المرتب والمريح بصرياً */
  .logo-upload-card {
    width: 100%;
  }

  .logo-card-inner {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 1.5px solid var(--border-color);
    border-radius: 18px;
    padding: 18px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    transition: all 0.25s ease;
  }

  .logo-card-inner:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
  }

  .logo-preview-wrapper {
    position: relative;
    flex-shrink: 0;
  }

  .logo-avatar-frame {
    position: relative;
    width: 84px;
    height: 84px;
    border-radius: 18px;
    background: #ffffff;
    border: 2px solid #e2e8f0;
    padding: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    cursor: pointer;
    transition: all 0.2s ease;
    margin: 0;
  }

  .logo-avatar-frame:hover {
    border-color: var(--primary);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(13, 56, 130, 0.12);
  }

  .logo-avatar-frame img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 12px;
  }

  .logo-avatar-placeholder {
    width: 100%;
    height: 100%;
    border-radius: 12px;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    color: var(--primary);
    font-size: 2rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .logo-edit-badge {
    position: absolute;
    bottom: -4px;
    left: -4px;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--primary);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    box-shadow: 0 2px 6px rgba(13, 56, 130, 0.35);
    border: 2px solid #ffffff;
    transition: all 0.2s ease;
  }

  .logo-avatar-frame:hover .logo-edit-badge {
    background: var(--primary-hover);
    transform: scale(1.1);
  }

  .logo-content-wrapper {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    gap: 6px;
  }

  .logo-title-group {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  .logo-main-label {
    font-size: 1rem;
    font-weight: 800;
    color: var(--text-main);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .logo-sub-label {
    font-size: 0.82rem;
    color: var(--text-muted);
    margin: 0;
  }

  .logo-meta-tags {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 2px;
  }

  .meta-tag {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 20px;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    font-size: 0.74rem;
    font-weight: 700;
    color: #475569;
  }

  .logo-action-wrapper {
    flex-shrink: 0;
  }

  .btn-upload-trigger {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 22px;
    border-radius: 12px;
    background: linear-gradient(135deg, #0d3882 0%, #1565c0 100%);
    color: #ffffff;
    font-size: 0.88rem;
    font-weight: 700;
    cursor: pointer;
    border: none;
    box-shadow: 0 4px 12px rgba(13, 56, 130, 0.2);
    transition: all 0.2s ease;
    white-space: nowrap;
    margin: 0;
  }

  .btn-upload-trigger:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(13, 56, 130, 0.3);
    color: #ffffff;
    background: linear-gradient(135deg, #1565c0 0%, #1d4ed8 100%);
  }

  .logo-success-notice {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #15803d;
    padding: 4px 12px;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 700;
    margin-top: 4px;
    width: fit-content;
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
    .logo-card-inner {
      flex-direction: column;
      align-items: center;
      text-align: center;
      padding: 20px 16px;
      gap: 16px;
    }
    .logo-content-wrapper {
      align-items: center;
    }
    .logo-meta-tags {
      justify-content: center;
    }
    .logo-action-wrapper {
      width: 100%;
    }
    .btn-upload-trigger {
      width: 100%;
      justify-content: center;
    }
    .grid-fields { grid-template-columns: 1fr; }
    .full-width { grid-column: span 1; }
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
