@extends('layouts.app')

@section('title', 'إحصائيات فرص العمل')

@section('page-title', 'إحصائيات فرص العمل')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>إحصائيات فرص العمل
                </h5>
            </div>
            <div class="card-body">
                <p>هذه صفحة إحصائيات فرص العمل. سيتم عرض الإحصائيات والمخططات هنا.</p>
                {{-- Placeholder for job opportunity statistics and charts --}}
                @if(isset($stats))
                    <h6>إحصائيات حسب النوع:</h6>
                    <ul>
                        @foreach($stats['byType'] as $stat)
                            <li>{{ $stat->type }}: {{ $stat->count }}</li>
                        @endforeach
                    </ul>
                    <h6>إحصائيات حسب الحالة:</h6>
                    <ul>
                        @foreach($stats['byStatus'] as $stat)
                            <li>{{ $stat->status }}: {{ $stat->count }}</li>
                        @endforeach
                    </ul>
                    <h6>إحصائيات حسب الشركة:</h6>
                    <ul>
                        @foreach($stats['byCompany'] as $stat)
                            <li>{{ $stat->name }}: {{ $stat->count }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
