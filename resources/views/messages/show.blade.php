@extends('layouts.app')

@php
    $isCompany = $otherUser->role === 'company';
    $displayName = ($isCompany && $otherUser->company) ? $otherUser->company->name : $otherUser->name;
    $initial = mb_substr($displayName, 0, 1);
    $subInfo = $isCompany 
        ? ($otherUser->company->industry ?? 'شريك توظيف وتدريب')
        : ($otherUser->graduateData->specialization ?? $otherUser->specialization ?? 'خريج جامعة طرابلس');
@endphp

@section('title', 'محادثة مع ' . $displayName)

@section('content')
<div class="container-fluid py-3">
    <!-- Chat Header Navigation -->
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('messages.index') }}" class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px;" title="العودة لصندوق الرسائل">
                <i class="fas fa-arrow-right"></i>
            </a>
            <div class="d-flex align-items-center gap-2.5">
                <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm flex-shrink-0"
                     style="width: 46px; height: 46px; font-size: 1.15rem; font-weight: bold; background: {{ $isCompany ? 'linear-gradient(135deg, #0d3882 0%, #1e40af 100%)' : 'linear-gradient(135deg, #059669 0%, #10b981 100%)' }}; color: #ffffff;">
                    @if($isCompany)
                        <i class="fas fa-building" style="font-size: 1.1rem;"></i>
                    @else
                        {{ $initial }}
                    @endif
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="mb-0 fw-bold text-dark fs-6">{{ $displayName }}</h5>
                        @if($isCompany)
                            <span class="badge rounded-pill px-2 py-0.5" style="background: rgba(13, 56, 130, 0.08); color: #0d3882; font-size: 0.7rem; border: 1px solid rgba(13, 56, 130, 0.2);">
                                <i class="fas fa-building me-1"></i>شركة شريكة
                            </span>
                        @else
                            <span class="badge rounded-pill px-2 py-0.5" style="background: rgba(5, 150, 105, 0.08); color: #059669; font-size: 0.7rem; border: 1px solid rgba(5, 150, 105, 0.2);">
                                <i class="fas fa-user-graduate me-1"></i>خريج
                            </span>
                        @endif
                    </div>
                    <small class="text-muted">{{ $subInfo }}</small>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if(!$isCompany && $otherUser->role === 'graduate')
                <a href="{{ route('graduate.profile.public', $otherUser->id) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
                    <i class="fas fa-user me-1"></i> عرض السيرة الذاتية
                </a>
            @endif
            <a href="{{ route('messages.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 shadow-sm text-secondary">
                <i class="fas fa-inbox me-1"></i> صندوق الرسائل
            </a>
        </div>
    </div>

    <!-- Chat Card Container -->
    <div class="card border-0 rounded-4 shadow-sm overflow-hidden" style="background: #ffffff;">
        <div class="card-body p-0 d-flex flex-column" style="height: 65vh; min-height: 480px;">
            
            <!-- Message Stream Area -->
            <div class="flex-grow-1 p-3 p-md-4 overflow-auto" id="chat-box" style="background: #f8fafc;">
                @if($messages->isEmpty())
                    <div class="text-center text-muted my-auto py-5">
                        <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 64px; height: 64px;">
                            <i class="fas fa-comments fa-2x text-primary opacity-60"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">لا توجد رسائل سابقة</h5>
                        <p class="small text-muted mb-0">ابدأ المحادثة الآن واكتب رسالتك في الأسفل.</p>
                    </div>
                @else
                    <div class="text-center my-2">
                        <span class="badge bg-white text-muted border px-3 py-1.5 rounded-pill shadow-xs small" style="font-size: 0.72rem;">
                            بداية المحادثة الآمنة بين الطرفين
                        </span>
                    </div>

                    @foreach($messages as $msg)
                        @php
                            $isMe = $msg->sender_id == auth()->id();
                        @endphp
                        <div class="d-flex mb-3 {{ $isMe ? 'justify-content-start' : 'justify-content-end' }}" dir="{{ $isMe ? 'rtl' : 'rtl' }}">
                            <div class="d-flex flex-column {{ $isMe ? 'align-items-start' : 'align-items-end' }}" style="max-width: 80%; min-width: 140px;">
                                <div class="p-3 shadow-xs position-relative" 
                                     style="font-size: 0.92rem; line-height: 1.55; border-radius: {{ $isMe ? '18px 18px 4px 18px' : '18px 18px 18px 4px' }} !important; background: {{ $isMe ? 'linear-gradient(135deg, #0d3882 0%, #1e40af 100%)' : '#ffffff' }}; color: {{ $isMe ? '#ffffff' : '#1e293b' }}; border: {{ $isMe ? 'none' : '1px solid #e2e8f0' }}; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                                    {!! nl2br(e($msg->content)) !!}
                                </div>
                                <div class="small text-muted mt-1 px-1.5 d-flex align-items-center gap-1.5 font-monospace" style="font-size: 0.72rem;">
                                    <span>{{ $msg->created_at->format('H:i') }}</span>
                                    <span class="text-muted opacity-50">•</span>
                                    <span>{{ $msg->created_at->format('Y-m-d') }}</span>
                                    @if($isMe)
                                        <i class="fas {{ $msg->read_at ? 'fa-check-double text-primary' : 'fa-check text-muted' }}" 
                                           style="font-size: 0.75rem;" 
                                           title="{{ $msg->read_at ? 'تمت القراءة (' . $msg->read_at->format('H:i') . ')' : 'تم الإرسال' }}"></i>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Message Input Area -->
            <div class="p-3 bg-white border-top">
                <form action="{{ route('messages.store', $otherUser->id) }}" method="POST" id="message-form">
                    @csrf
                    <div class="d-flex align-items-end gap-2">
                        <div class="flex-grow-1 position-relative">
                            <textarea name="content" 
                                      id="message-content" 
                                      class="form-control rounded-4 border p-3" 
                                      rows="2" 
                                      placeholder="اكتب رسالتك هنا... (اضغط Enter للإرسال، Shift+Enter لسطر جديد)" 
                                      required 
                                      style="resize: none; font-size: 0.92rem; background: #f8fafc;"></textarea>
                        </div>
                        <button class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" 
                                type="submit" 
                                style="width: 50px; height: 50px;" 
                                title="إرسال الرسالة">
                            <i class="fas fa-paper-plane" style="font-size: 1.1rem; transform: rotate(-25deg); margin-left: 3px;"></i>
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
    document.addEventListener('DOMContentLoaded', function () {
        const chatBox = document.getElementById('chat-box');
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        const msgInput = document.getElementById('message-content');
        const msgForm = document.getElementById('message-form');

        if (msgInput && msgForm) {
            msgInput.focus();
            msgInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    if (this.value.trim().length > 0) {
                        msgForm.submit();
                    }
                }
            });
        }
    });
</script>
@endpush
