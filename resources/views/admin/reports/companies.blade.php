@extends('layouts.app')

@section('title', 'تقارير الشركات')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">تقارير الشركات</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">قائمة الشركات</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="companiesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>اسم الشركة</th>
                            <th>البريد الإلكتروني</th>
                            <th>حالة الشراكة</th>
                            <th>تاريخ الإنشاء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($companies as $company)
                        <tr>
                            <td>{{ $company->name }}</td>
                            <td>{{ $company->email }}</td>
                            <td>{{ $company->partnership_status ?? 'N/A' }}</td>
                            <td>{{ $company->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4">لا توجد شركات لعرضها.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Placeholder for Company Charts -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">رسوم بيانية للشركات</h6>
        </div>
        <div class="card-body">
            <p>هنا يمكن إضافة رسوم بيانية تفاعلية تعرض إحصائيات الشركات (مثل عدد الشركات حسب حالة الشراكة، القطاع، إلخ).</p>
            <!-- Example Chart Placeholder -->
            <canvas id="companiesChart"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Example Chart Data (replace with actual data from your controller)
        const companyStatuses = @json($companies->groupBy('partnership_status')->map->count());
        const labels = Object.keys(companyStatuses);
        const data = Object.values(companyStatuses);

        const ctx = document.getElementById('companiesChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'عدد الشركات حسب حالة الشراكة',
                    data: data,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'توزيع الشركات حسب حالة الشراكة'
                    }
                }
            }
        });
    });
</script>
@endpush
