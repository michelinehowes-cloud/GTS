@extends('layouts.app')

@section('title', 'إدارة مرشحي التوظيف')

@section('content')
<!-- ستايلات Bento المخصصة -->
<style>
    /* Bento Grid System */
    .bento-container {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        padding: 1rem 0;
    }
    
    .bento-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        overflow: hidden;
        position: relative;
    }
    
    .bento-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    }
    
    /* Header Styles */
    .bento-header {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        color: white;
        padding: 2rem;
        border-radius: 20px;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 30px rgba(28, 200, 138, 0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        overflow: hidden;
    }
    
    .bento-header::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: url('data:image/svg+xml;utf8,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><path d="M10 10h10v10H10z" fill="rgba(255,255,255,0.05)"/></svg>') repeat;
        opacity: 0.5;
        z-index: 1;
    }
    
    .bento-header-content {
        position: relative;
        z-index: 2;
    }
    
    .bento-header h3 {
        margin: 0;
        font-weight: 700;
        font-size: 1.8rem;
    }
    
    .bento-header p {
        margin: 0.5rem 0 0;
        opacity: 0.8;
        font-size: 1rem;
    }
    
    .bento-stat-badge {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 0.8rem 1.5rem;
        border-radius: 15px;
        font-weight: 600;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        position: relative;
        z-index: 2;
    }
    
    /* Filter Section */
    .bento-filter-card {
        background: #f8f9fc;
        border-radius: 15px;
        padding: 1.2rem;
        border: 1px solid #e3e6f0;
        margin-bottom: 1.5rem;
    }
    
    .filter-label {
        font-weight: 600;
        color: #1cc88a;
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.9rem;
    }
    
    .bento-input {
        border-radius: 10px;
        border: 1px solid #d1d3e2;
        padding: 0.6rem 1rem;
        transition: all 0.2s;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);
    }
    
    .bento-input:focus {
        border-color: #1cc88a;
        box-shadow: 0 0 0 0.2rem rgba(28, 200, 138, 0.25);
    }
    
    /* Table Styles */
    .bento-table-container {
        border-radius: 15px;
        overflow: hidden;
        border: 1px solid #e3e6f0;
    }
    
    .bento-table {
        margin-bottom: 0;
    }
    
    .bento-table thead {
        background-color: #f8f9fc;
    }
    
    .bento-table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        color: #5a5c69;
        padding: 1rem;
        border-bottom: 2px solid #e3e6f0;
    }
    
    .bento-table td {
        padding: 1rem;
        vertical-align: middle;
        color: #3a3b45;
        border-bottom: 1px solid #e3e6f0;
    }
    
    .bento-table tbody tr {
        transition: background-color 0.2s;
    }
    
    .bento-table tbody tr:hover {
        background-color: #f8f9fc;
    }
    
    /* Status Badges */
    .bento-badge {
        padding: 0.5rem 0.8rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-block;
    }
    
    .badge-pending { background-color: #fff3cd; color: #856404; }
    .badge-sent { background-color: #cce5ff; color: #004085; }
    .badge-review { background-color: #e2e3e5; color: #383d41; }
    .badge-interview { background-color: #d1ecf1; color: #0c5460; }
    .badge-accepted { background-color: #d4edda; color: #155724; }
    .badge-rejected { background-color: #f8d7da; color: #721c24; }
    .badge-withdrawn { background-color: #e2e3e5; color: #383d41; text-decoration: line-through; }
    
    .badge-hired { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .badge-not_hired { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .badge-in_progress { background-color: #cce5ff; color: #004085; border: 1px solid #b8daff; }
    
    /* Action Buttons */
    .bento-btn {
        padding: 0.5rem 1rem;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
        border: none;
        color: white;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
    }
    
    .btn-view {
        background: #1cc88a;
        box-shadow: 0 4px 10px rgba(28, 200, 138, 0.3);
    }
    
    .btn-view:hover {
        background: #13855c;
        transform: translateY(-2px);
        color: white;
    }
    
    /* Graduate Info */
    .grad-info {
        display: flex;
        flex-direction: column;
    }
    
    .grad-name {
        font-weight: 700;
        color: #1cc88a;
        font-size: 1rem;
    }
    
    .grad-major {
        font-size: 0.85rem;
        color: #858796;
    }
    
    /* Empty State */
    .bento-empty-state {
        padding: 4rem 2rem;
        text-align: center;
    }
    
    .bento-empty-icon {
        font-size: 4rem;
        color: #d1d3e2;
        margin-bottom: 1.5rem;
    }
    
    .bento-empty-title {
        font-weight: 700;
        color: #5a5c69;
        margin-bottom: 0.5rem;
    }
</style>

<div class="container-fluid py-4">
    <div class="bento-container">
        
        <!-- Header -->
        <div class="bento-header">
            <div class="bento-header-content">
                <h3>إدارة مرشحي التوظيف (ATS)</h3>
                <p>متابعة وتحديث حالات المتقدمين للفرص الوظيفية الخاصة بشركة {{ $company->name }}</p>
            </div>
            <div class="bento-stat-badge">
                <i class="fas fa-users"></i> {{ $nominations->count() }} مرشح
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 15px; border: none; box-shadow: 0 4px 10px rgba(28, 200, 138, 0.2);">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 15px; border: none;">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="bento-card">
            
            <!-- Filters -->
            <div class="bento-filter-card">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="filter-label"><i class="fas fa-briefcase me-1"></i> الفرصة الوظيفية</label>
                        <select class="form-select bento-input" onchange="window.location.href = this.value">
                            <option value="{{ route('company.nominations') }}">جميع الفرص</option>
                            @foreach($opportunities as $opportunity)
                                <option value="{{ route('company.nominations', ['opportunity_id' => $opportunity->id]) }}" 
                                    {{ request('opportunity_id') == $opportunity->id ? 'selected' : '' }}>
                                    {{ $opportunity->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="filter-label"><i class="fas fa-tasks me-1"></i> حالة الترشيح</label>
                        <select class="form-select bento-input" onchange="window.location.href = this.value">
                            <option value="{{ route('company.nominations') }}">جميع الحالات</option>
                            @php
                                $statuses = [
                                    'pending' => 'قيد المراجعة',
                                    'sent_to_company' => 'مرسل للشركة',
                                    'under_review' => 'قيد الدراسة',
                                    'interview_scheduled' => 'مقابلة مجدولة',
                                    'accepted' => 'مقبول',
                                    'rejected' => 'مرفوض',
                                    'withdrawn' => 'ملغي'
                                ];
                            @endphp
                            @foreach($statuses as $value => $text)
                                <option value="{{ route('company.nominations', ['status' => $value]) }}" 
                                    {{ request('status') == $value ? 'selected' : '' }}>
                                    {{ $text }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="filter-label" for="from_date"><i class="fas fa-calendar-alt me-1"></i> من تاريخ</label>
                        <input type="date" id="from_date" name="from_date" class="form-control bento-input" 
                               value="{{ request('from_date') }}" 
                               onchange="applyDateFilter()">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="filter-label" for="to_date"><i class="fas fa-calendar-check me-1"></i> إلى تاريخ</label>
                        <input type="date" id="to_date" name="to_date" class="form-control bento-input" 
                               value="{{ request('to_date') }}" 
                               onchange="applyDateFilter()">
                    </div>
                </div>
            </div>

            <!-- Content -->
            @if($nominations->count() > 0)
                <div class="bento-table-container table-responsive">
                    <table class="table bento-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>المرشح (الخريج)</th>
                                <th>الفرصة الوظيفية</th>
                                <th>تاريخ التقدم/الترشيح</th>
                                <th>الحالة</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($nominations as $nomination)
                                @php
                                    $statusClass = 'badge-review';
                                    switch($nomination->status) {
                                        case 'pending': $statusClass = 'badge-pending'; break;
                                        case 'sent_to_company': $statusClass = 'badge-sent'; break;
                                        case 'under_review': $statusClass = 'badge-review'; break;
                                        case 'interview_scheduled': $statusClass = 'badge-interview'; break;
                                        case 'accepted': $statusClass = 'badge-accepted'; break;
                                        case 'rejected': $statusClass = 'badge-rejected'; break;
                                        case 'withdrawn': $statusClass = 'badge-withdrawn'; break;
                                    }
                                @endphp
                            <tr>
                                <td><span class="text-muted fw-bold">{{ $loop->iteration }}</span></td>
                                <td>
                                    <div class="grad-info">
                                        <span class="grad-name">{{ $nomination->graduate->name }}</span>
                                        <span class="grad-major"><i class="fas fa-graduation-cap me-1"></i>{{ $nomination->graduate->major }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-dark fw-bold">{{ $nomination->jobOpportunity->title }}</span>
                                </td>
                                <td>
                                    <span class="text-muted"><i class="far fa-clock me-1"></i>{{ $nomination->nominated_at->format('Y-m-d') }}</span>
                                </td>
                                <td>
                                    <span class="bento-badge {{ $statusClass }}">
                                        {{ $nomination->status_text }}
                                    </span>
                                    
                                    @if($nomination->final_status)
                                        @php
                                            $finalClass = 'badge-in_progress';
                                            if($nomination->final_status == 'hired') $finalClass = 'badge-hired';
                                            if($nomination->final_status == 'not_hired') $finalClass = 'badge-not_hired';
                                        @endphp
                                        <br>
                                        <span class="bento-badge {{ $finalClass }} mt-1" style="font-size: 0.75rem;">
                                            @if($nomination->final_status == 'hired') <i class="fas fa-check-circle me-1"></i>
                                            @elseif($nomination->final_status == 'not_hired') <i class="fas fa-times-circle me-1"></i>
                                            @else <i class="fas fa-spinner fa-spin me-1"></i> @endif
                                            {{ $nomination->final_status_text }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <!-- زر عرض الملف وتحديث الحالة -->
                                    <a href="{{ route('company.nominations.show', $nomination->id) }}" 
                                       class="bento-btn btn-view" title="عرض ملف المرشح وتحديث حالته">
                                        <i class="fas fa-user-check"></i> معاينة وتحديث
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="bento-empty-state">
                    <i class="fas fa-user-tie bento-empty-icon"></i>
                    <h4 class="bento-empty-title">لا يوجد مرشحين بعد</h4>
                    <p class="text-muted">لم يتم العثور على أي مرشحين للفرص الوظيفية الخاصة بكم في الوقت الحالي.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function applyDateFilter() {
    const fromDate = document.getElementById('from_date').value;
    const toDate = document.getElementById('to_date').value;
    let url = "{{ route('company.nominations') }}";
    const params = new URLSearchParams(window.location.search);

    if (params.has('status')) {
        url += `?status=${params.get('status')}`;
    } else if (params.has('opportunity_id')) {
        url += `?opportunity_id=${params.get('opportunity_id')}`;
    } else {
        url += `?`;
    }

    if (fromDate) {
        url += `${url.includes('?') ? '&' : '?'}from_date=${fromDate}`;
    }
    if (toDate) {
        url += `${url.includes('?') ? '&' : '?'}to_date=${toDate}`;
    }
    window.location.href = url;
}
</script>
@endsection
