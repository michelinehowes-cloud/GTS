@extends('layouts.app')

@section('title', 'إدارة ردود الاستبيانات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list mr-2"></i>
                        إدارة ردود الاستبيانات
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('evaluation-followup.surveys.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-poll"></i> الاستبيانات
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- فلترة الاستبيانات -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <form method="GET" action="{{ route('evaluation-followup.survey-responses.index') }}">
                                <div class="form-group">
                                    <label for="survey_filter">فلترة حسب الاستبيان:</label>
                                    <select class="form-control" id="survey_filter" name="survey_id"
                                            onchange="this.form.submit()">
                                        <option value="">جميع الاستبيانات</option>
                                        @foreach($surveys as $survey)
                                            <option value="{{ $survey->id }}"
                                                    {{ request('survey_id') == $survey->id ? 'selected' : '' }}>
                                                {{ $survey->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاستبيان</th>
                                    <th>المستجيب</th>
                                    <th>تاريخ الرد</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($responses as $response)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $response->survey->title }}</strong>
                                            <br>
                                            <small class="text-muted">{{ Str::limit($response->survey->description, 30) }}</small>
                                        </td>
                                        <td>
                                            @if($response->user)
                                                <div>
                                                    <strong>{{ $response->user->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $response->user->email }}</small>
                                                    <br>
                                                    <span class="badge badge-light">{{ ucfirst($response->user->role) }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted">مستخدم مجهول</span>
                                            @endif
                                        </td>
                                        <td>{{ $response->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <span class="badge badge-success">مكتمل</span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('evaluation-followup.survey-responses.show', $response) }}"
                                                   class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> عرض التفاصيل
                                                </a>
                                                <form action="{{ route('evaluation-followup.survey-responses.destroy', $response) }}"
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا الرد؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i> حذف
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-list fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">لا توجد ردود استبيانات</h5>
                                                <p class="text-muted">
                                                    @if(request('survey_id'))
                                                        لا توجد ردود لهذا الاستبيان بعد
                                                    @else
                                                        لم يتم الرد على أي استبيان بعد
                                                    @endif
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($responses->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $responses->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // تأكيد الحذف
    $('.btn-danger').on('click', function(e) {
        if (!confirm('هل أنت متأكد من حذف هذا الرد؟ سيتم حذف الرد نهائياً.')) {
            e.preventDefault();
        }
    });
});
</script>
@endsection
