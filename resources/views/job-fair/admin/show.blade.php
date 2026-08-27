@extends('layouts.app')

@section('title', $fair->title . ' - تفاصيل المعرض')

@section('focus_mode', true)

@section('content')
<div class="container-fluid py-4">

    <style>
        .stat-card-custom {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .stat-card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important;
        }
        .stat-card-custom:hover i {
            transform: scale(1.15) rotate(5deg);
        }

        .list-item-custom {
            transition: all 0.2s;
            border: 1px solid transparent;
        }
        .list-item-custom:hover {
            transform: translateX(-5px);
            background: #fff !important;
            border-color: rgba(4,93,176,0.1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
    </style>
    <!-- Hero Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center mb-4 p-4 rounded-4 shadow flex-wrap gap-4" style="background: linear-gradient(135deg, #03488a 0%, #045db0 100%); color: white; position: relative; overflow: hidden;">
        
        <!-- Decoration -->
        <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: radial-gradient(circle, rgba(238,202,62,0.2) 0%, transparent 70%); border-radius: 50%;"></div>

        <div class="d-flex align-items-center gap-4 position-relative z-1">
            <a href="{{ route('job-fair.admin.index') }}" class="btn rounded-circle" style="width:50px;height:50px;display:flex;align-items:center;justify-content:center; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2); transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                <i class="fas fa-arrow-right"></i>
            </a>
            <!-- Logos -->
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('images/job_fair_logo_white.png') }}" alt="شعار المعرض" style="height: 55px; width: auto;">
            </div>
            <div>
                <h2 class="fw-bold mb-1" style="color: #eeca3e; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">{{ $fair->title }}</h2>
                <div class="d-flex align-items-center gap-3" style="color: rgba(255,255,255,0.9); font-size: 0.95rem;">
                    <span><i class="fas fa-calendar-alt me-1 text-warning"></i>{{ $fair->event_date->format('d/m/Y') }}</span>
                    <span><i class="fas fa-map-marker-alt me-1 text-warning"></i>{{ $fair->location }}</span>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap position-relative z-1 align-items-center">
            <!-- تغيير الحالة -->
            <form action="{{ route('job-fair.admin.status', $fair->id) }}" method="POST" class="m-0">
                @csrf
                <select name="status" class="form-select form-select-sm rounded-pill border-0 px-3 py-2 shadow-sm" style="width:auto; font-weight: bold; background: white; color: #03488a; cursor: pointer;" onchange="this.form.submit()">
                    @foreach(['draft'=>'مسودة','published'=>'منشور','ongoing'=>'جارٍ الآن','completed'=>'منتهي'] as $val => $label)
                    <option value="{{ $val }}" {{ $fair->status == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('job-fair.admin.live', $fair->id) }}" class="btn rounded-pill text-white px-3 py-2" target="_blank" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                <i class="fas fa-tv me-2 text-danger"></i>بث مباشر
            </a>
            <a href="{{ route('job-fair.admin.attendance', $fair->id) }}" class="btn rounded-pill fw-bold px-4 py-2 shadow-sm" style="background: #eeca3e; color: #03488a; border: none; transition: 0.3s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <i class="fas fa-qrcode me-2"></i>ماسح QR
            </a>
            <a href="{{ route('job-fair.admin.export', $fair->id) }}" class="btn rounded-pill text-white px-3 py-2" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                <i class="fas fa-file-export me-2"></i>تصدير
            </a>
            <a href="{{ route('job-fair.public') }}" class="btn rounded-pill text-white px-3 py-2" target="_blank" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); transition: 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                <i class="fas fa-external-link-alt me-2"></i>صفحة عامة
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success rounded-3 border-0 shadow-sm mb-4">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <!-- Registered -->
        <div class="col-6 col-md-3">
            <div class="card stat-card-custom rounded-4 p-4 shadow-sm text-end h-100" style="background: white; border: none; border-right: 5px solid #03488a; box-shadow: 0 4px 15px rgba(0,0,0,0.04) !important;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div style="font-size: 2.2rem; font-weight: 900; color: #1e293b;">{{ $stats['total_registered'] }}</div>
                    <div style="width:55px;height:55px;background:rgba(3,72,138,0.08);border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.5rem; transition: transform 0.3s;">
                        <i class="fas fa-user-graduate" style="color: #03488a;"></i>
                    </div>
                </div>
                <h6 class="fw-bold mb-0 text-start text-muted">خريج مسجل</h6>
            </div>
        </div>
        <!-- Attended -->
        <div class="col-6 col-md-3">
            <div class="card stat-card-custom rounded-4 p-4 shadow-sm text-end h-100" style="background: white; border: none; border-right: 5px solid #045db0; box-shadow: 0 4px 15px rgba(0,0,0,0.04) !important;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div style="font-size: 2.2rem; font-weight: 900; color: #1e293b;">{{ $stats['total_attended'] }}</div>
                    <div style="width:55px;height:55px;background:rgba(4,93,176,0.08);border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.5rem; transition: transform 0.3s;">
                        <i class="fas fa-user-check" style="color: #045db0;"></i>
                    </div>
                </div>
                <h6 class="fw-bold mb-0 text-start text-muted">حضروا المعرض</h6>
            </div>
        </div>
        <!-- Companies -->
        <div class="col-6 col-md-3">
            <div class="card stat-card-custom rounded-4 p-4 shadow-sm text-end h-100" style="background: white; border: none; border-right: 5px solid #eeca3e; box-shadow: 0 4px 15px rgba(0,0,0,0.04) !important;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div style="font-size: 2.2rem; font-weight: 900; color: #1e293b;">{{ $stats['total_companies'] }}</div>
                    <div style="width:55px;height:55px;background:rgba(238,202,62,0.15);border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.5rem; transition: transform 0.3s;">
                        <i class="fas fa-building" style="color: #d97706;"></i>
                    </div>
                </div>
                <h6 class="fw-bold mb-0 text-start text-muted">شركة مشاركة</h6>
            </div>
        </div>
        <!-- Days Remaining -->
        <div class="col-6 col-md-3">
            <div class="card stat-card-custom rounded-4 p-4 shadow-sm text-end h-100" style="background: white; border: none; border-right: 5px solid #10b981; box-shadow: 0 4px 15px rgba(0,0,0,0.04) !important;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div style="font-size: 2.2rem; font-weight: 900; color: #1e293b;">{{ $stats['days_remaining'] }}</div>
                    <div style="width:55px;height:55px;background:rgba(16,185,129,0.1);border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.5rem; transition: transform 0.3s;">
                        <i class="fas fa-clock" style="color: #10b981;"></i>
                    </div>
                </div>
                <h6 class="fw-bold mb-0 text-start text-muted">يوم متبقي</h6>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- الشركات المشاركة -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header border-0 bg-transparent p-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-building me-2" style="color: #6366F1"></i>
                        الشركات المشاركة
                    </h5>
                    <button class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="collapse" data-bs-target="#addCompanyForm">
                        <i class="fas fa-plus me-1"></i>إضافة
                    </button>
                </div>
                <div class="card-body p-4">

                    <!-- Add Company Form -->
                    <div class="collapse mb-3" id="addCompanyForm">
                        <form action="{{ route('job-fair.admin.add-company', $fair->id) }}" method="POST"
                              class="p-3 rounded-3" style="background: #f8fafc; border: 1px dashed #cbd5e1">
                            @csrf
                            <div class="row g-2">
                                <div class="col-12">
                                    <select name="company_id" class="form-select form-select-sm rounded-3" required>
                                        <option value="">-- اختر شركة --</option>
                                        @foreach($fair->companies->pluck('company_id')->toArray() as $cid)
                                        @endforeach
                                        @php $existingIds = $fair->companies->pluck('company_id')->toArray(); @endphp
                                        @foreach(App\Models\Company::whereNotIn('id', $existingIds)->get() as $co)
                                        <option value="{{ $co->id }}">{{ $co->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <input type="text" name="booth_number" class="form-control form-control-sm rounded-3" placeholder="رقم الجناح (A1)">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="available_positions" class="form-control form-control-sm rounded-3" placeholder="عدد الوظائف" min="0">
                                </div>
                                <div class="col-12">
                                    <textarea name="requirements" class="form-control form-control-sm rounded-3" rows="2" placeholder="متطلبات التوظيف..."></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-sm btn-primary rounded-pill w-100">
                                        <i class="fas fa-check me-1"></i>إضافة الشركة
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Companies List -->
                    @if($fair->companies->isEmpty())
                    <p class="text-muted text-center py-3">لا توجد شركات مضافة بعد</p>
                    @else
                    <div class="d-flex flex-column gap-2">
                        @foreach($fair->companies as $fc)
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 list-item-custom" style="background: #f8fafc">
                            <div class="d-flex align-items-center justify-content-center rounded-3" style="width:40px;height:40px;background:#e8f0fe;font-size:1.2rem">🏢</div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold small">{{ $fc->company->name }}</div>
                                <div class="d-flex gap-2 mt-1">
                                    @if($fc->booth_number)
                                    <span class="badge bg-primary rounded-pill" style="font-size:0.7rem">جناح {{ $fc->booth_number }}</span>
                                    @endif
                                    @if($fc->available_positions)
                                    <span class="badge bg-success rounded-pill" style="font-size:0.7rem">{{ $fc->available_positions }} وظيفة</span>
                                    @endif
                                </div>
                            </div>
                            <form action="{{ route('job-fair.admin.remove-company', [$fair->id, $fc->company_id]) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle"
                                        onclick="return confirm('حذف الشركة من المعرض؟')" style="width:30px;height:30px;padding:0">
                                    <i class="fas fa-times" style="font-size:0.7rem"></i>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- قائمة الخريجين المسجلين -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header border-0 bg-transparent p-4 pb-0">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-user-graduate me-2" style="color: #0284C7"></i>
                        الخريجون المسجلون
                        <span class="badge bg-primary rounded-pill ms-2">{{ $stats['total_registered'] }}</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($registrations->isEmpty())
                    <p class="text-muted text-center py-3">لا يوجد تسجيلات بعد</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>الرقم</th>
                                    <th>الاسم</th>
                                    <th>التخصص</th>
                                    <th>الحضور</th>
                                    <th>البطاقة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($registrations as $reg)
                                <tr class="list-item-custom">
                                    <td><small class="fw-bold text-primary">{{ $reg->registration_number }}</small></td>
                                    <td>{{ $reg->graduate->name }}</td>
                                    <td><small class="text-muted">{{ $reg->graduate->major ?? '—' }}</small></td>
                                    <td>
                                        @if($reg->attended)
                                        <span class="badge bg-success rounded-pill">حضر</span>
                                        @else
                                        <span class="badge bg-secondary rounded-pill">غائب</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('job-fair.my-ticket', $reg->id) }}" class="btn btn-xs btn-outline-primary rounded-pill" target="_blank" style="font-size:0.7rem;padding:2px 8px">
                                            <i class="fas fa-qrcode"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $registrations->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
