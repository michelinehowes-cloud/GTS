@extends('layouts.app')

@section('title', 'إدارة الفعاليات - ' . $fair->title)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">إدارة فعاليات المعرض</h1>
            <p class="text-muted mt-1">{{ $fair->title }}</p>
        </div>
        <div>
            <a href="{{ route('job-fair.admin.show', $fair->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right"></i> العودة للمعرض
            </a>
            <button class="btn btn-primary" data-toggle="modal" data-target="#addEventModal">
                <i class="fas fa-plus"></i> إضافة فعالية جديدة
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">الفعالية</th>
                            <th class="border-0">المتحدث</th>
                            <th class="border-0">التوقيت</th>
                            <th class="border-0">المكان</th>
                            <th class="border-0 text-center">التسجيلات</th>
                            <th class="border-0 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                            <tr>
                                <td class="align-middle">
                                    <h6 class="mb-0 font-weight-bold">{{ $event->title }}</h6>
                                    <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;">
                                        {{ $event->description }}
                                    </small>
                                </td>
                                <td class="align-middle">{{ $event->speaker_name ?? '-' }}</td>
                                <td class="align-middle">
                                    <div><i class="far fa-calendar-alt text-primary mr-1"></i> {{ $event->start_time->format('Y-m-d') }}</div>
                                    <div class="small text-muted"><i class="far fa-clock text-info mr-1"></i> {{ $event->start_time->format('H:i') }} - {{ $event->end_time->format('H:i') }}</div>
                                </td>
                                <td class="align-middle">{{ $event->location ?? '-' }}</td>
                                <td class="align-middle text-center">
                                    <span class="badge badge-info px-3 py-2">
                                        {{ $event->attendees()->count() }} {{ $event->capacity ? '/ ' . $event->capacity : '' }}
                                    </span>
                                </td>
                                <td class="align-middle text-center">
                                    <form action="{{ route('job-fair.admin.events.destroy', $event->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد من حذف هذه الفعالية؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">لا توجد فعاليات مضافة لهذا المعرض حتى الآن.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Event -->
<div class="modal fade" id="addEventModal" tabindex="-1" role="dialog" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="{{ route('job-fair.admin.events.store', $fair->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addEventModalLabel">إضافة فعالية جديدة</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>عنوان الفعالية <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required placeholder="مثال: ورشة عمل كتابة السيرة الذاتية">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>اسم المتحدث / المدرب</label>
                            <input type="text" name="speaker_name" class="form-control" placeholder="اسم المدرب أو الجهة">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>وصف مختصر</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="نبذة عن الفعالية ومحاورها..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>وقت البداية <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="start_time" class="form-control" required value="{{ \Carbon\Carbon::parse($fair->event_date)->format('Y-m-d\T09:00') }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>وقت النهاية <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="end_time" class="form-control" required value="{{ \Carbon\Carbon::parse($fair->event_date)->format('Y-m-d\T11:00') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>المكان / القاعة</label>
                            <input type="text" name="location" class="form-control" placeholder="مثال: القاعة الرئيسية أ">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>السعة الاستيعابية (اختياري)</label>
                            <input type="number" name="capacity" class="form-control" min="1" placeholder="مثال: 50">
                            <small class="text-muted">اتركه فارغاً إذا كان العدد مفتوحاً</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ الفعالية</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
