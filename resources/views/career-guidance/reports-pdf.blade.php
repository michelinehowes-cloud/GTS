@extends('layouts.app')

@section('title', 'تقرير الإرشاد المهني المتقدم')

@section('content')
<div class="container-fluid py-4">
    <h1 class="text-center text-primary mb-4">تقرير الإرشاد المهني المتقدم</h1>
    <p class="text-center text-muted">تاريخ التقرير: {{ date('Y-m-d') }}</p>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">الإحصائيات الرئيسية</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3"><strong>إجمالي الخريجين:</strong> {{ $stats['totalGraduates'] ?? 0 }}</div>
                <div class="col-md-4 mb-3"><strong>خريجين موظفين:</strong> {{ $stats['employedGraduates'] ?? 0 }}</div>
                <div class="col-md-4 mb-3"><strong>باحثين عن عمل:</strong> {{ $stats['seekingOpportunities'] ?? 0 }}</div>
                <div class="col-md-4 mb-3"><strong>معدل التوظيف:</strong> {{ $stats['employmentRate'] ?? 0 }}%</div>
                <div class="col-md-4 mb-3"><strong>الترشيحات النشطة:</strong> {{ $stats['activeNominations'] ?? 0 }}</div>
                <div class="col-md-4 mb-3"><strong>نسبة النجاح:</strong> {{ $stats['successRate'] ?? 0 }}%</div>
            </div>
        </div>
    </div>

    <div class="card insight-card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">استنتاجات وتحليلات ذكية</h5>
        </div>
        <div class="card-body">
            @forelse($insights as $insight)
                <div class="mb-2">
                    <strong>{{ $insight['title'] }}</strong>: {{ $insight['description'] }}
                </div>
            @empty
                <p>لا توجد استنتاجات متاحة حالياً.</p>
            @endforelse
        </div>
    </div>

    <div class="text-center text-muted mt-5">
        نظام تدريب الخريجين - جامعة طرابلس &copy; {{ date('Y') }}
    </div>
</div>
@endsection

@section('styles')
<style>
    body {
        font-family: 'Tajawal', sans-serif;
        direction: rtl;
        text-align: right;
    }
    .card-header.bg-primary {
        background-color: #1e3a8a !important;
    }
    .card-header.bg-info {
        background-color: #36b9cc !important;
    }
    .insight-card .card-body {
        background-color: #f0f4f8;
    }
</style>
@endsection
