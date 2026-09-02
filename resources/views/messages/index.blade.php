@extends('layouts.app')

@section('title', 'صندوق الرسائل')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 fw-bold text-dark">
            <i class="fas fa-envelope text-primary me-2"></i> صندوق الرسائل
        </h1>
    </div>

    <div class="bento-card p-0 overflow-hidden">
        @if(empty($conversations))
            <div class="empty-state py-5">
                <div class="empty-state-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h4>لا توجد رسائل</h4>
                <p class="text-muted">صندوق الوارد الخاص بك فارغ حالياً.</p>
            </div>
        @else
            <div class="list-group list-group-flush">
                @foreach($conversations as $userId => $conv)
                    <a href="{{ route('messages.show', $userId) }}" 
                       class="list-group-item list-group-item-action p-4 border-bottom {{ $conv['unread_count'] > 0 ? 'bg-light' : '' }}"
                       style="transition: all 0.2s ease;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-3" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                    {{ mb_substr($conv['user']->name, 0, 1) }}
                                </div>
                                <div>
                                    <h5 class="mb-1 fw-bold {{ $conv['unread_count'] > 0 ? 'text-dark' : 'text-secondary' }}">
                                        {{ $conv['user']->name }}
                                    </h5>
                                    <p class="mb-0 text-truncate" style="max-width: 400px; color: {{ $conv['unread_count'] > 0 ? '#1e293b' : '#64748b' }};">
                                        @if($conv['last_message']->sender_id == auth()->id())
                                            <i class="fas fa-reply text-muted ms-1" style="font-size: 0.8rem"></i>
                                        @endif
                                        {{ $conv['last_message']->content }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="small text-muted fw-medium mb-2">
                                    {{ $conv['last_message']->created_at->diffForHumans() }}
                                </div>
                                @if($conv['unread_count'] > 0)
                                    <span class="badge rounded-pill bg-danger px-2 py-1">{{ $conv['unread_count'] }} جديدة</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
