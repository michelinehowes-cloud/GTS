@extends('layouts.app')

@section('title', 'تفاصيل الإشعار')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">تفاصيل الإشعار</h5>
                        <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-right me-1"></i> العودة للقائمة
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-4">
                            <i class="fas fa-{{ $notification->icon }} fs-1 text-{{ $notification->color }} me-3"></i>
                            <div>
                                <h3 class="card-title">{{ $notification->title }}</h3>
                                <p class="text-muted mb-2">
                                    <i class="far fa-clock me-1"></i> {{ $notification->created_at->format('Y-m-d H:i') }}
                                    <span class="mx-2">|</span>
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                                @if($notification->sender)
                                    <p class="text-muted mb-2">
                                        <i class="far fa-user me-1"></i> من: {{ $notification->sender->name }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="alert alert-light border">
                            <p class="card-text fs-5">{{ $notification->message }}</p>
                        </div>

                        @if($notification->data)
                            <div class="mt-4">
                                <h6>بيانات إضافية:</h6>
                                <pre
                                    class="bg-light p-3 rounded border">{{ json_encode($notification->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        @endif

                        @if($notification->model_type && $notification->model_id)
                            @php
                                $modelRoute = '#';
                                // Try to determine a route based on model type
                                // This is a basic example and might need adjustment based on your specific routes
                                if (str_contains($notification->model_type, 'Training')) {
                                    $modelRoute = route('graduate.trainings.show', $notification->model_id);
                                } elseif (str_contains($notification->model_type, 'JobOpportunity')) {
                                    $modelRoute = route('job-opportunities.show', $notification->model_id);
                                }
                            @endphp

                            @if($modelRoute !== '#')
                                <div class="mt-4">
                                    <a href="{{ $modelRoute }}" class="btn btn-primary">
                                        <i class="fas fa-external-link-alt me-1"></i> عرض التفاصيل المرتبطة
                                    </a>
                                </div>
                            @endif
                        @endif

                    </div>
                    <div class="card-footer text-end">
                        <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST"
                            class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الإشعار؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-1"></i> حذف الإشعار
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection