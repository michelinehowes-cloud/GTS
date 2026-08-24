@extends('layouts.app')

@section('title', 'صندوق الرسائل')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-envelope text-primary"></i> صندوق الرسائل
        </h1>
    </div>

    <div class="card shadow mb-4 border-0">
        <div class="card-body p-0">
            @if(empty($conversations))
                <div class="p-5 text-center">
                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                    <h4>لا توجد رسائل</h4>
                    <p class="text-muted">صندوق الوارد الخاص بك فارغ حالياً.</p>
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach($conversations as $userId => $conv)
                        <a href="{{ route('messages.show', $userId) }}" class="list-group-item list-group-item-action p-4 {{ $conv['unread_count'] > 0 ? 'bg-light' : '' }}">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                        {{ mb_substr($conv['user']->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h5 class="mb-1 fw-bold {{ $conv['unread_count'] > 0 ? 'text-dark' : 'text-secondary' }}">
                                            {{ $conv['user']->name }}
                                        </h5>
                                        <p class="mb-0 text-truncate" style="max-width: 400px; color: {{ $conv['unread_count'] > 0 ? '#000' : '#6c757d' }}">
                                            @if($conv['last_message']->sender_id == auth()->id())
                                                <i class="fas fa-reply text-muted ms-1" style="font-size: 0.8rem"></i>
                                            @endif
                                            {{ $conv['last_message']->content }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="small text-muted mb-2">
                                        {{ $conv['last_message']->created_at->diffForHumans() }}
                                    </div>
                                    @if($conv['unread_count'] > 0)
                                        <span class="badge bg-danger rounded-pill">{{ $conv['unread_count'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
