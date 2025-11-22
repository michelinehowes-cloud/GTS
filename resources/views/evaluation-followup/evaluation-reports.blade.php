@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Evaluation Reports</h1>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Total Evaluations</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $stats['total_evaluations'] ?? 0 }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Average Score</div>
                <div class="card-body">
                    <h5 class="card-title">{{ number_format($stats['average_scores'] ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Evaluations by Type</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach ($stats['evaluations_by_type'] ?? [] as $type => $count)
                            <li class="list-group-item">{{ ucfirst($type) }}: {{ $count }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <h2 class="mt-4">Recent Evaluations</h2>
    @if(($evaluations ?? false) && $evaluations->count() > 0)
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Evaluatable Type</th>
                    <th>Evaluator</th>
                    <th>User (if applicable)</th>
                    <th>Training (if applicable)</th>
                    <th>Score</th>
                    <th>Type</th>
                    <th>Comments</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($evaluations as $evaluation)
                    <tr>
                        <td>{{ $evaluation->id }}</td>
                        <td>{{ $evaluation->evaluatable_type }}</td>
                        <td>{{ $evaluation->evaluator->name ?? 'N/A' }}</td>
                        <td>{{ $evaluation->user->name ?? 'N/A' }}</td>
                        <td>{{ $evaluation->training->name ?? 'N/A' }}</td>
                        <td>{{ $evaluation->average_score }}</td>
                        <td>{{ $evaluation->type }}</td>
                        <td>{{ Str::limit($evaluation->comments, 50) }}</td>
                        <td>{{ $evaluation->evaluation_date ? $evaluation->evaluation_date->format('Y-m-d') : 'N/A' }}</td>
                        <td>{{ $evaluation->status ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No evaluations found.</p>
    @endif
</div>
@endsection
