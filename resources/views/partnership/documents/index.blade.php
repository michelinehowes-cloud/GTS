@extends('layouts.app')

@section('title', 'إدارة وثائق الشراكة')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">وثائق الشراكة</h1>
        <a href="{{ route('partnership.documents.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> إضافة وثيقة جديدة
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($documents->isEmpty())
        <div class="alert alert-info" role="alert">
            لا توجد وثائق شراكة متاحة حالياً.
        </div>
    @else
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">قائمة وثائق الشراكة</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="documentsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم الوثيقة</th>
                                <th>الشركة</th>
                                <th>النوع</th>
                                <th>تاريخ الرفع</th>
                                <th>تاريخ الانتهاء</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $document)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $document->document_name }}</td>
                                <td>{{ $document->company->name ?? 'N/A' }}</td>
                                <td>{{ $document->document_type_text }}</td>
                                <td>{{ $document->created_at->format('Y-m-d') }}</td>
                                <td>{{ $document->expiry_date ? $document->expiry_date->format('Y-m-d') : 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $document->document_status == 'active' ? 'success' : ($document->document_status == 'expired' ? 'danger' : 'warning') }}">
                                        {{ $document->document_status_text }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ Storage::url($document->file_path) }}" class="btn btn-info btn-sm" target="_blank" title="عرض الوثيقة">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('partnership.documents.edit', $document->id) }}" class="btn btn-warning btn-sm" title="تعديل الوثيقة">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('partnership.documents.delete', $document->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذه الوثيقة؟')" title="حذف الوثيقة">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
