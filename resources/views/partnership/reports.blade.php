@extends('layouts.app')

@section('title', 'تقارير الشراكات')

@section('page-title', 'تقارير الشراكات')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>تقارير الشراكات
                </h5>
            </div>
            <div class="card-body">
                <p>هذه صفحة تقارير الشراكات. سيتم عرض الإحصائيات والمخططات هنا.</p>
                {{-- Placeholder for partnership statistics and charts --}}
                @if(isset($partnershipStats))
                    <h6>إحصائيات الشراكات:</h6>
                    <ul>
                        @foreach($partnershipStats['byType'] as $stat)
                            <li>{{ $stat->partnership_type }}: {{ $stat->count }}</li>
                        @endforeach
                    </ul>
                @endif
                @if(isset($opportunityStats))
                    <h6>إحصائيات فرص العمل:</h6>
                    <ul>
                        @foreach($opportunityStats['byType'] as $stat)
                            <li>{{ $stat->type }}: {{ $stat->count }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
