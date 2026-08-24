@extends('layouts.app')

@section('title', $fair->title . ' - تفاصيل المعرض')

@section('content')
<div class="container-fluid py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('job-fair.admin.index') }}" class="btn btn-light rounded-circle" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center">
                <i class="fas fa-arrow-right"></i>
            </a>
            <div>
                <h2 class="fw-bold mb-0" style="color: #0A1628">{{ $fair->title }}</h2>
                <small class="text-muted">
                    <i class="fas fa-calendar me-1"></i>{{ $fair->event_date->format('d/m/Y') }}
                    &nbsp;—&nbsp;
                    <i class="fas fa-map-marker-alt me-1"></i>{{ $fair->location }}
                </small>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <!-- تغيير الحالة -->
            <form action="{{ route('job-fair.admin.status', $fair->id) }}" method="POST" class="d-inline">
                @csrf
                <select name="status" class="form-select form-select-sm rounded-pill d-inline-block" style="width:auto" onchange="this.form.submit()">
                    @foreach(['draft'=>'مسودة','published'=>'منشور','ongoing'=>'جارٍ الآن','completed'=>'منتهي'] as $val => $label)
                    <option value="{{ $val }}" {{ $fair->status == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('job-fair.admin.attendance', $fair->id) }}" class="btn btn-success rounded-pill">
                <i class="fas fa-qrcode me-2"></i>ماسح QR
            </a>
            <a href="{{ route('job-fair.admin.export', $fair->id) }}" class="btn btn-outline-info rounded-pill">
                <i class="fas fa-file-export me-2"></i>تصدير
            </a>
            <a href="{{ route('job-fair.public') }}" class="btn btn-outline-primary rounded-pill" target="_blank">
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
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3" style="background: linear-gradient(135deg, #EFF6FF, #DBEAFE)">
                <div style="font-size: 2rem; font-weight: 900; color: #2563EB">{{ $stats['total_registered'] }}</div>
                <small class="text-muted">خريج مسجل</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3" style="background: linear-gradient(135deg, #F0FDF4, #D1FAE5)">
                <div style="font-size: 2rem; font-weight: 900; color: #059669">{{ $stats['total_attended'] }}</div>
                <small class="text-muted">حضر</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3" style="background: linear-gradient(135deg, #FFF7ED, #FED7AA)">
                <div style="font-size: 2rem; font-weight: 900; color: #EA580C">{{ $stats['total_companies'] }}</div>
                <small class="text-muted">شركة</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3" style="background: linear-gradient(135deg, #FEF3C7, #FDE68A)">
                <div style="font-size: 2rem; font-weight: 900; color: #D97706">{{ $stats['days_remaining'] }}</div>
                <small class="text-muted">يوم متبقي</small>
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
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: #f8fafc">
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
                                <tr>
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
