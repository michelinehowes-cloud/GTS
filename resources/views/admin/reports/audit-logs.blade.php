@extends('layouts.app')

@section('title', 'سجل الحركات')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">سجل الحركات في النظام</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">جميع الحركات</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="auditLogsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>المستخدم</th>
                            <th>الحدث</th>
                            <th>الوصف</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($auditLogs as $log)
                        <tr>
                            <td>{{ $log->user->name ?? 'N/A' }}</td>
                            <td>{{ $log->event }}</td>
                            <td>{{ $log->description }}</td>
                            <td>{{ $log->ip_address ?? 'N/A' }}</td>
                            <td>{{ $log->user_agent ?? 'N/A' }}</td>
                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">لا توجد حركات في النظام لعرضها.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $auditLogs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
