@extends('layouts.app')

@section('title', 'استيراد بيانات الخريجين')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">استيراد بيانات الخريجين</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('import_errors'))
                        <div class="alert alert-warning">
                            <h5>أخطاء أثناء الاستيراد:</h5>
                            <ul>
                                @foreach (session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('career-guidance.import.graduates') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="excel_file" class="form-label">ملف Excel/CSV</label>
                            <input type="file" class="form-control" id="excel_file" name="excel_file" required>
                            <div class="form-text">
                                يرجى التأكد من أن الملف بصيغة XLSX, XLS, أو CSV.
                                <br>
                                <a href="{{ route('career-guidance.download.template') }}" class="btn btn-sm btn-outline-info mt-2">
                                    <i class="fas fa-download me-1"></i> تحميل قالب البيانات
                                </a>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i> استيراد البيانات
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
