{{-- ملف: resources/views/admin/companies/index.blade.php --}}
@extends('layouts.app')

@section('title', 'إدارة الشركات')

@section('page-title', 'إدارة الشركات')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-building me-2"></i>قائمة الشركات
        </h5>
        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'partnership_officer')
            <a href="{{ route('admin.companies.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>إضافة شركة جديدة
            </a>
        @endif
    </div>
    <div class="card-body">
        @if($companies->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم الشركة</th>
                        <th>البريد الإلكتروني</th>
                        <th>رقم الهاتف</th>
                        <th>المجال الصناعي</th>
                        <th>العنوان</th>
                        <th>تاريخ الإضافة</th>
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'partnership_officer')
                            <th>الإجراءات</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($companies as $company)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $company->name }}</td>
                        <td>{{ $company->email }}</td>
                        <td>{{ $company->phone }}</td>
                        <td>{{ $company->industry }}</td>
                        <td>{{ $company->address }}</td>
                        <td>{{ $company->created_at->format('Y-m-d') }}</td>
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'partnership_officer')
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.companies.edit', $company->id) }}" 
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.companies.destroy', $company->id) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('هل أنت متأكد من حذف هذه الشركة؟')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-4">
            <i class="fas fa-building fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">لا توجد شركات مسجلة</h5>
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'partnership_officer')
                <a href="{{ route('admin.companies.create') }}" class="btn btn-primary mt-2">
                    <i class="fas fa-plus me-2"></i>إضافة أول شركة
                </a>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection