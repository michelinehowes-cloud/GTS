@extends('layouts.app')

@section('title', 'محادثة مع ' . $otherUser->name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('messages.index') }}" class="btn btn-sm btn-outline-secondary me-3">
                <i class="fas fa-arrow-right"></i>
            </a>
            <h1 class="h4 mb-0 text-gray-800">
                محادثة مع: <strong>{{ $otherUser->name }}</strong>
            </h1>
        </div>
    </div>

    <div class="card shadow mb-4 border-0">
        <div class="card-body p-0 d-flex flex-column" style="height: 60vh;">
            
            <!-- منطقة الرسائل -->
            <div class="flex-grow-1 p-4 overflow-auto" id="chat-box" style="background-color: #f8f9fc;">
                @if($messages->isEmpty())
                    <div class="text-center text-muted my-5">
                        <i class="fas fa-comments fa-3x mb-3"></i>
                        <p>لا توجد رسائل سابقة. ابدأ المحادثة الآن!</p>
                    </div>
                @else
                    @foreach($messages as $msg)
                        @php
                            $isMe = $msg->sender_id == auth()->id();
                        @endphp
                        <div class="d-flex mb-4 {{ $isMe ? 'justify-content-end' : 'justify-content-start' }}">
                            <div class="d-flex flex-column {{ $isMe ? 'align-items-end' : 'align-items-start' }}" style="max-width: 70%;">
                                <div class="p-3 rounded-3 shadow-sm {{ $isMe ? 'bg-primary text-white' : 'bg-white border' }}" style="border-radius: {{ $isMe ? '15px 15px 0 15px' : '15px 15px 15px 0' }} !important;">
                                    {{ $msg->content }}
                                </div>
                                <div class="small text-muted mt-1 px-1">
                                    {{ $msg->created_at->format('Y-m-d H:i') }}
                                    @if($isMe)
                                        <i class="fas {{ $msg->read_at ? 'fa-check-double text-primary' : 'fa-check' }} ms-1" style="font-size: 0.7rem;"></i>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- حقل الإدخال -->
            <div class="p-3 bg-white border-top">
                <form action="{{ route('messages.store', $otherUser->id) }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <textarea name="content" class="form-control" rows="1" placeholder="اكتب رسالتك هنا..." required style="resize: none;"></textarea>
                        <button class="btn btn-primary px-4" type="submit">
                            <i class="fas fa-paper-plane"></i> إرسال
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Scroll to bottom of chat
    const chatBox = document.getElementById('chat-box');
    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }
</script>
@endpush
