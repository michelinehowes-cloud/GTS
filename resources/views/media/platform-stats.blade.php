@extends('layouts.app')

@section('title', 'التحكم في إحصائيات المنصة والصفحة الرئيسية')

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">

    {{-- الهيرو المعتمد --}}
    <x-page-hero
        title="التحكم في إحصائيات المنصة والصفحة الرئيسية"
        description="إدارة الأرقام والإحصائيات المعروضة للجمهور والزوار في الصفحة الرئيسية، مع خيار الربط المباشر بقاعدة البيانات لمنع الأرقام الوهمية أو تحديد أرقام رسمية معتمدة."
        icon="fas fa-sliders-h"
        category="وحدة الإعلام والبث الذكي"
        :breadcrumb="[
            ['text' => 'لوحة الميديا', 'url' => route('media.dashboard')],
            ['text' => 'إحصائيات المنصة', 'active' => true]
        ]"
    >
        <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-light d-flex align-items-center gap-2 rounded-pill px-3 py-2">
            <i class="fas fa-external-link-alt"></i>
            <span>معاينة الصفحة الرئيسية كزائر</span>
        </a>
        <a href="{{ route('media.dashboard') }}" class="btn btn-light bg-white text-primary fw-bold d-flex align-items-center gap-2 rounded-pill px-3 py-2 shadow-sm">
            <i class="fas fa-arrow-right"></i>
            <span>العودة للميديا</span>
        </a>
    </x-page-hero>

    {{-- شريط تنبيه النجاح --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-3 rounded-4 shadow-sm border-0 mb-4 p-3.5" role="alert" style="background: #ecfdf5; border-right: 5px solid #10b981 !important;">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; background: #10b981; color: white;">
                <i class="fas fa-check"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-0 text-success" style="font-size: 0.95rem;">تم الحفظ بنجاح</h6>
                <p class="mb-0 text-muted small">{{ session('success') }}</p>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- بطاقة المعاينة الحية المباشرة --}}
    <div class="card card-modern shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill bg-primary px-3 py-1.5 fw-bold" style="font-size: 0.78rem;">
                    <i class="fas fa-eye me-1"></i> معاينة مباشرة
                </span>
                <h6 class="fw-bold mb-0 text-dark">هكذا تظهر الإحصائيات حالياً في الصفحة الرئيسية للزوار</h6>
            </div>
            <span class="text-muted small">
                حالة الشريط: 
                @if($setting->is_ribbon_visible)
                    <strong class="text-success"><i class="fas fa-check-circle"></i> ظاهر للجمهور</strong>
                @else
                    <strong class="text-danger"><i class="fas fa-eye-slash"></i> مخفي تماماً</strong>
                @endif
            </span>
        </div>
        <div class="card-body p-4" style="background: #f8fafc;">
            @if(!$setting->is_ribbon_visible)
                <div class="alert alert-warning mb-0 rounded-3 text-center py-3">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    <strong>شريط الإحصائيات معطل ومخفي حالياً من الصفحة الرئيسية</strong>، ولن يظهر للزوار حتى تقوم بتفعيله أدناه.
                </div>
            @else
                <div class="row g-3">
                    @foreach($homepageData['cards'] as $card)
                        @if($card['is_visible'])
                            <div class="col-6 col-md-3">
                                <div class="p-3.5 bg-white rounded-4 border text-center shadow-xs position-relative h-100 d-flex flex-column justify-content-center align-items-center" style="border-color: #e2e8f0;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 44px; height: 44px; background: {{ $card['bg_color'] }}; color: {{ $card['color'] }}; font-size: 1.2rem;">
                                        <i class="{{ $card['icon'] }}"></i>
                                    </div>
                                    <h3 class="fw-bolder text-dark mb-1 fs-4 font-monospace">
                                        {{ $card['prefix'] }}{{ number_format($card['value']) }}
                                    </h3>
                                    <p class="text-muted small mb-0 fw-semibold" style="font-size: 0.82rem;">{{ $card['label'] }}</p>
                                    <div class="mt-2">
                                        @if($card['is_custom'])
                                            <span class="badge rounded-pill" style="background: #fef3c7; color: #92400e; font-size: 0.68rem;">رقم معتمد إعلامياً</span>
                                        @else
                                            <span class="badge rounded-pill" style="background: #ecfdf5; color: #065f46; font-size: 0.68rem;">مباشر من القاعدة</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-6 col-md-3">
                                <div class="p-3 bg-light rounded-4 border border-dashed text-center h-100 d-flex flex-column justify-content-center align-items-center opacity-50" style="border-color: #cbd5e1;">
                                    <i class="fas fa-eye-slash text-muted mb-1"></i>
                                    <div class="small fw-bold text-muted">{{ $card['title'] }}</div>
                                    <span class="badge bg-secondary rounded-pill mt-1" style="font-size: 0.65rem;">مخفية</span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- نموذج التحكم والتحديث --}}
    <form action="{{ route('media.platform-stats.update') }}" method="POST">
        @csrf

        {{-- 1. الإعدادات العامة --}}
        <div class="card card-modern shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; background: #0d3882; font-size: 0.85rem;">
                    1
                </div>
                <h5 class="fw-bold mb-0 text-dark">الإعدادات العامة لشريط الإحصائيات</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4 align-items-center">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border bg-light d-flex align-items-center justify-content-between">
                            <div>
                                <label class="form-label fw-bold mb-0 d-block text-dark">
                                    <i class="fas fa-toggle-on text-primary me-1"></i>
                                    إظهار شريط الإحصائيات في الصفحة الرئيسية
                                </label>
                                <small class="text-muted">عند إيقاف هذا الخيار، يتم إخفاء شريط الإحصائيات بالكامل من الواجهة العامة فوراً.</small>
                            </div>
                            <div class="form-check form-switch fs-4 m-0 ms-3">
                                <input class="form-check-input" type="checkbox" name="is_ribbon_visible" id="is_ribbon_visible" value="1" {{ $setting->is_ribbon_visible ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border bg-light">
                            <label class="form-label fw-bold mb-1 d-block text-dark">
                                <i class="fas fa-sliders-h text-primary me-1"></i>
                                الوضع العام للمنظومة
                            </label>
                            <div class="d-flex gap-3 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="global_mode" id="global_mode_auto" value="auto" {{ $setting->global_mode == 'auto' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="global_mode_auto">
                                        تلقائي حقيقي (من قاعدة البيانات)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="global_mode" id="global_mode_manual" value="manual" {{ $setting->global_mode == 'manual' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="global_mode_manual">
                                        تخصيص يدوي معتمد من وحدة الإعلام
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. إعدادات كل بطاقة تفصيلياً --}}
        <div class="card card-modern shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; background: #0d3882; font-size: 0.85rem;">
                    2
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">التحكم الدقيق في الأرقام والإحصائيات لكل مجال</h5>
                    <small class="text-muted">يمكنك اختيار مصدر كل رقم، إدخال رقم رسمي، تعديل العنوان، أو إخفاء أي بطاقة لا ترغب في عرضها حالياً.</small>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">

                    {{-- بطاقة 1: الخريجون --}}
                    <div class="col-lg-6">
                        <div class="p-4 rounded-4 border h-100 bg-white shadow-xs" style="border-color: #bfdbfe !important;">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(37,99,235,0.1); color: #2563eb;">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-0">إحصائية الخريجين</h6>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="graduates_visible" id="graduates_visible" value="1" {{ $setting->graduates_visible ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-bold" for="graduates_visible">إظهار</label>
                                </div>
                            </div>

                            <div class="p-2.5 rounded-3 mb-3 d-flex align-items-center justify-content-between" style="background: #eff6ff; border: 1px solid #dbeafe;">
                                <span class="small text-muted"><i class="fas fa-database text-primary me-1"></i> المسجلون الفعليون في النظام الآن:</span>
                                <span class="badge bg-primary rounded-pill font-monospace fs-6 px-2.5">{{ $realCounts['graduates'] }} خريج</span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">مصدر الرقم المعروض:</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="graduates_mode" id="grad_auto" value="auto" {{ $setting->graduates_mode == 'auto' ? 'checked' : '' }} onchange="toggleManualInput('graduates', false)">
                                        <label class="form-check-label small fw-semibold" for="grad_auto">تلقائي من قاعدة البيانات ({{ $realCounts['graduates'] }})</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="graduates_mode" id="grad_manual" value="manual" {{ $setting->graduates_mode == 'manual' ? 'checked' : '' }} onchange="toggleManualInput('graduates', true)">
                                        <label class="form-check-label small fw-semibold" for="grad_manual">رقم رسمي معتمد</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-8">
                                    <label class="form-label small fw-bold">الرقم المعتمد (في الوضع المخصص):</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control font-monospace" name="graduates_custom_value" id="graduates_custom_value" value="{{ $setting->graduates_custom_value }}" placeholder="{{ $realCounts['graduates'] }}" min="0">
                                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="document.getElementById('graduates_custom_value').value = '{{ $realCounts['graduates'] }}'" title="نسخ الرقم الحقيقي">
                                            الفعلي
                                        </button>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <label class="form-label small fw-bold">البادئة:</label>
                                    <input type="text" class="form-control text-center font-monospace" name="graduates_prefix" value="{{ $setting->graduates_prefix }}" placeholder="+" maxlength="5">
                                </div>
                            </div>

                            <div>
                                <label class="form-label small fw-bold">نص التسمية أسفل الرقم:</label>
                                <input type="text" class="form-control" name="graduates_label" value="{{ $setting->graduates_label }}" placeholder="خريج مسجل ومعتمد">
                            </div>
                        </div>
                    </div>

                    {{-- بطاقة 2: الشركات --}}
                    <div class="col-lg-6">
                        <div class="p-4 rounded-4 border h-100 bg-white shadow-xs" style="border-color: #a7f3d0 !important;">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(16,185,129,0.1); color: #10b981;">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-0">إحصائية الشركات الشريكة</h6>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="companies_visible" id="companies_visible" value="1" {{ $setting->companies_visible ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-bold" for="companies_visible">إظهار</label>
                                </div>
                            </div>

                            <div class="p-2.5 rounded-3 mb-3 d-flex align-items-center justify-content-between" style="background: #ecfdf5; border: 1px solid #d1fae5;">
                                <span class="small text-muted"><i class="fas fa-database text-success me-1"></i> الشركات المسجلة فعلياً في النظام:</span>
                                <span class="badge bg-success rounded-pill font-monospace fs-6 px-2.5">{{ $realCounts['companies'] }} شركة</span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">مصدر الرقم المعروض:</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="companies_mode" id="comp_auto" value="auto" {{ $setting->companies_mode == 'auto' ? 'checked' : '' }} onchange="toggleManualInput('companies', false)">
                                        <label class="form-check-label small fw-semibold" for="comp_auto">تلقائي من قاعدة البيانات ({{ $realCounts['companies'] }})</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="companies_mode" id="comp_manual" value="manual" {{ $setting->companies_mode == 'manual' ? 'checked' : '' }} onchange="toggleManualInput('companies', true)">
                                        <label class="form-check-label small fw-semibold" for="comp_manual">رقم رسمي معتمد</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-8">
                                    <label class="form-label small fw-bold">الرقم المعتمد (في الوضع المخصص):</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control font-monospace" name="companies_custom_value" id="companies_custom_value" value="{{ $setting->companies_custom_value }}" placeholder="{{ $realCounts['companies'] }}" min="0">
                                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="document.getElementById('companies_custom_value').value = '{{ $realCounts['companies'] }}'" title="نسخ الرقم الحقيقي">
                                            الفعلي
                                        </button>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <label class="form-label small fw-bold">البادئة:</label>
                                    <input type="text" class="form-control text-center font-monospace" name="companies_prefix" value="{{ $setting->companies_prefix }}" placeholder="+" maxlength="5">
                                </div>
                            </div>

                            <div>
                                <label class="form-label small fw-bold">نص التسمية أسفل الرقم:</label>
                                <input type="text" class="form-control" name="companies_label" value="{{ $setting->companies_label }}" placeholder="شركة ومؤسسة شريكة">
                            </div>
                        </div>
                    </div>

                    {{-- بطاقة 3: التدريبات --}}
                    <div class="col-lg-6">
                        <div class="p-4 rounded-4 border h-100 bg-white shadow-xs" style="border-color: #fde68a !important;">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(245,158,11,0.1); color: #f59e0b;">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-0">إحصائية البرامج التدريبية</h6>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="trainings_visible" id="trainings_visible" value="1" {{ $setting->trainings_visible ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-bold" for="trainings_visible">إظهار</label>
                                </div>
                            </div>

                            <div class="p-2.5 rounded-3 mb-3 d-flex align-items-center justify-content-between" style="background: #fffbeb; border: 1px solid #fef3c7;">
                                <span class="small text-muted"><i class="fas fa-database text-warning me-1"></i> البرامج التدريبية المسجلة فعلياً:</span>
                                <span class="badge bg-warning text-dark rounded-pill font-monospace fs-6 px-2.5">{{ $realCounts['trainings'] }} برنامج</span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">مصدر الرقم المعروض:</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="trainings_mode" id="train_auto" value="auto" {{ $setting->trainings_mode == 'auto' ? 'checked' : '' }} onchange="toggleManualInput('trainings', false)">
                                        <label class="form-check-label small fw-semibold" for="train_auto">تلقائي من قاعدة البيانات ({{ $realCounts['trainings'] }})</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="trainings_mode" id="train_manual" value="manual" {{ $setting->trainings_mode == 'manual' ? 'checked' : '' }} onchange="toggleManualInput('trainings', true)">
                                        <label class="form-check-label small fw-semibold" for="train_manual">رقم رسمي معتمد</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-8">
                                    <label class="form-label small fw-bold">الرقم المعتمد (في الوضع المخصص):</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control font-monospace" name="trainings_custom_value" id="trainings_custom_value" value="{{ $setting->trainings_custom_value }}" placeholder="{{ $realCounts['trainings'] }}" min="0">
                                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="document.getElementById('trainings_custom_value').value = '{{ $realCounts['trainings'] }}'" title="نسخ الرقم الحقيقي">
                                            الفعلي
                                        </button>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <label class="form-label small fw-bold">البادئة:</label>
                                    <input type="text" class="form-control text-center font-monospace" name="trainings_prefix" value="{{ $setting->trainings_prefix }}" placeholder="+" maxlength="5">
                                </div>
                            </div>

                            <div>
                                <label class="form-label small fw-bold">نص التسمية أسفل الرقم:</label>
                                <input type="text" class="form-control" name="trainings_label" value="{{ $setting->trainings_label }}" placeholder="برنامج تدريبي وتأهيلي">
                            </div>
                        </div>
                    </div>

                    {{-- بطاقة 4: فرص العمل --}}
                    <div class="col-lg-6">
                        <div class="p-4 rounded-4 border h-100 bg-white shadow-xs" style="border-color: #ddd6fe !important;">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(139,92,246,0.1); color: #8b5cf6;">
                                        <i class="fas fa-briefcase"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-0">إحصائية فرص العمل والترشيحات</h6>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="opportunities_visible" id="opportunities_visible" value="1" {{ $setting->opportunities_visible ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-bold" for="opportunities_visible">إظهار</label>
                                </div>
                            </div>

                            <div class="p-2.5 rounded-3 mb-3 d-flex align-items-center justify-content-between" style="background: #f5f3ff; border: 1px solid #ede9fe;">
                                <span class="small text-muted"><i class="fas fa-database text-purple me-1" style="color: #8b5cf6;"></i> فرص العمل المسجلة في النظام:</span>
                                <span class="badge text-white rounded-pill font-monospace fs-6 px-2.5" style="background: #8b5cf6;">{{ $realCounts['opportunities'] }} فرصة</span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">مصدر الرقم المعروض:</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="opportunities_mode" id="opp_auto" value="auto" {{ $setting->opportunities_mode == 'auto' ? 'checked' : '' }} onchange="toggleManualInput('opportunities', false)">
                                        <label class="form-check-label small fw-semibold" for="opp_auto">تلقائي من قاعدة البيانات ({{ $realCounts['opportunities'] }})</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="opportunities_mode" id="opp_manual" value="manual" {{ $setting->opportunities_mode == 'manual' ? 'checked' : '' }} onchange="toggleManualInput('opportunities', true)">
                                        <label class="form-check-label small fw-semibold" for="opp_manual">رقم رسمي معتمد</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-8">
                                    <label class="form-label small fw-bold">الرقم المعتمد (في الوضع المخصص):</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control font-monospace" name="opportunities_custom_value" id="opportunities_custom_value" value="{{ $setting->opportunities_custom_value }}" placeholder="{{ $realCounts['opportunities'] }}" min="0">
                                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="document.getElementById('opportunities_custom_value').value = '{{ $realCounts['opportunities'] }}'" title="نسخ الرقم الحقيقي">
                                            الفعلي
                                        </button>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <label class="form-label small fw-bold">البادئة:</label>
                                    <input type="text" class="form-control text-center font-monospace" name="opportunities_prefix" value="{{ $setting->opportunities_prefix }}" placeholder="+" maxlength="5">
                                </div>
                            </div>

                            <div>
                                <label class="form-label small fw-bold">نص التسمية أسفل الرقم:</label>
                                <input type="text" class="form-control" name="opportunities_label" value="{{ $setting->opportunities_label }}" placeholder="فرصة عمل وترشيح">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-footer bg-light py-3 px-4 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                <span class="text-muted small">
                    <i class="fas fa-shield-alt text-success me-1"></i>
                    كل تغيير يتم اعتماده سينعكس فوراً وتلقائياً على زوار الصفحة الرئيسية للمنظومة.
                </span>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('media.platform-stats') }}" class="btn btn-outline-secondary rounded-pill px-4">
                        إلغاء
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm d-flex align-items-center gap-2" style="background: #0d3882; border-color: #0d3882;">
                        <i class="fas fa-save"></i>
                        <span>حفظ واعتماد الإحصائيات فوراً</span>
                    </button>
                </div>
            </div>
        </div>

    </form>

</div>
@endsection
