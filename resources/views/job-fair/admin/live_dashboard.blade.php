@extends('layouts.app')

@section('title', 'المتابعة اللحظية لإحصائيات المعرض - ' . $fair->title)

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa;">
    {{-- الشريط العلوي الموحد مع الشعارات الرسمية المتطابقة مع صفحة المعرض العامة --}}
    @include('job-fair.admin.partials.header', [
        'fair' => $fair,
        'page' => 'live',
        'title' => 'المتابعة اللحظية لإحصائيات وحضور المعرض',
        'subtitle' => $fair->title . ' — مؤشرات الأداء الحية ومعدلات الإقبال والزيارات الميدانية'
    ])

    <!-- بطاقات الإحصائيات الرئيسية -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">إجمالي الحضور (Check-ins)</div>
                            <div class="h1 mb-0 font-weight-bold text-gray-800">{{ $stats['attended'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">المسجلين الإجمالي</div>
                            <div class="h1 mb-0 font-weight-bold text-gray-800">{{ $stats['total_registrations'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">الشركات المشاركة</div>
                            <div class="h1 mb-0 font-weight-bold text-gray-800">{{ $stats['total_companies'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">سير ذاتية مستلمة (زيارات)</div>
                            <div class="h1 mb-0 font-weight-bold text-gray-800">{{ \App\Models\JobFairVisit::where('job_fair_id', $fair->id)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- أحدث الحضور -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4 h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-2"></i> أحدث الخريجين الحاضرين</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0">الخريج</th>
                                    <th class="border-0">التخصص</th>
                                    <th class="border-0">وقت الحضور</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentCheckins as $checkin)
                                    <tr>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
                                                    {{ mb_substr($checkin->graduate->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="font-weight-bold">{{ $checkin->graduate->name }}</div>
                                                    <div class="small text-muted">{{ $checkin->registration_number }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">{{ $checkin->graduate->major ?? 'غير محدد' }}</td>
                                        <td class="align-middle">
                                            <span class="badge badge-success px-2 py-1"><i class="far fa-clock mr-1"></i> {{ $checkin->updated_at->diffForHumans() }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">لم يتم تسجيل أي حضور حتى الآن.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- أكثر التخصصات حضوراً -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4 h-100">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-pie mr-2"></i> التخصصات الأكثر حضوراً</h6>
                </div>
                <div class="card-body">
                    @forelse($topMajors as $major)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="font-weight-bold">{{ $major->major }}</span>
                                <span class="text-muted">{{ $major->total }} خريج</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ ($major->total / max($stats['total_attended'], 1)) * 100 }}%" aria-valuenow="{{ $major->total }}" aria-valuemin="0" aria-valuemax="{{ $stats['total_attended'] }}"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-chart-bar fa-3x mb-3 text-gray-300"></i>
                            <p>لا توجد بيانات كافية بعد.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .blink {
        animation: blinker 1.5s linear infinite;
    }
    @keyframes blinker {
        50% { opacity: 0; }
    }
    .border-left-primary { border-left: 4px solid #4e73df !important; }
    .border-left-success { border-left: 4px solid #1cc88a !important; }
    .border-left-info { border-left: 4px solid #36b9cc !important; }
    .border-left-warning { border-left: 4px solid #f6c23e !important; }
    .text-gray-300 { color: #dddfeb !important; }
    .text-gray-800 { color: #5a5c69 !important; }
</style>
@endsection

@section('scripts')
<script>
    // Refresh page every 30 seconds for live updates
    setTimeout(function() {
        window.location.reload();
    }, 30000);
</script>
@endsection
