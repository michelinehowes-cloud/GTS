@extends('layouts.app')

@section('title', 'إضافة تدريب جديد')

@section('page-title', 'إضافة تدريب جديد')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-plus me-2"></i>إضافة تدريب جديد
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('trainings.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label">عنوان التدريب *</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="company_id" class="form-label">الشركة *</label>
                            <select class="form-control" id="company_id" name="company_id" required>
                                <option value="">اختر الشركة</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">وصف التدريب</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">تاريخ البدء *</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">تاريخ الانتهاء *</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="capacity" class="form-label">عدد المقاعد *</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">المكان *</label>
                            <input type="text" class="form-control" id="location" name="location" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">رجوع</a>
                        <button type="submit" class="btn btn-primary">حفظ التدريب</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection