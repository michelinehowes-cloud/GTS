
@extends('layouts.app')

@section('title', 'تقرير المستخدمين التفصيلي')

@section('page-title', 'تقرير المستخدمين التفصيلي')

@section('content')
<!-- إحصائيات التقرير -->
<div class="row mb-4">
    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <h3 class="text-primary">{{ $reportStats['total'] }}</h3>
                <small class="text-muted">إجمالي المستخدمين</small>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <h3 class="text-success">{{ $reportStats['active'] }}</h3>
                <small class="text-muted">نشطين</small>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <h3 class="text-danger">{{ $reportStats['admins'] }}</h3>
                <small class="text-muted">مديرين</small>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <h3 class="text-warning">{{ $reportStats['coordinators'] }}</h3>
                <small class="text-muted">منسقين</small>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <h3 class="text-info">{{ $reportStats['graduates'] }}</h3>
                <small class="text-muted">خريجين</small>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <h3 class="text-dark">{{ $users->count() }}</h3>
                <small class="text-muted">في التقرير</small>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="