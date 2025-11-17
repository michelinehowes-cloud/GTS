@extends('layouts.app')

@section('title', 'تقارير التدريبات')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">تقارير التدريبات</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">قائمة التدريبات</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="trainingsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>اسم التدريب</th>
                            <th>الشركة</th>
                            <th>تاريخ البدء</th>
                            <th>تاريخ الانتهاء</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($trainings as $training)
                        <tr>
                            <td>{{ $training->name }}</td>
                            <td>{{ $training->company->name ?? 'N/A' }}</td>
                            <td>{{ $training->start_date->format('Y-m-d') }}</td>
                            <td>{{ $training->end_date->format('Y-m-d') }}</td>
                            <td>{{ $training->status }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">لا توجد تدريبات لعرضها.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Placeholder for Training Charts -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">رسوم بيانية للتدريبات</h6>
        </div>
        <div class="card-body">
            <p>هنا يمكن إضافة رسوم بيانية تفاعلية تعرض إحصائيات التدريبات (مثل عدد التدريبات حسب الحالة، الفئة، إلخ).</p>
            <!-- Example Chart Placeholder -->
            <canvas id="trainingsChart"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Example Chart Data (replace with actual data from your controller)
        const trainingStatuses = @json($trainings->groupBy('status')->map->count());
        const labels = Object.keys(trainingStatuses);
        const data = Object.values(trainingStatuses);

        const ctx = document.getElementById('trainingsChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    label: 'عدد التدريبات حسب الحالة',
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
                        text: 'توزيع التدريبات حسب الحالة'
                    }
                }
            }
        });
    });
</script>
@endpush
