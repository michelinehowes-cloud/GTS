@extends('layouts.app')

@php
    $isCompany = $otherUser->role === 'company';
    $isGraduate = $otherUser->role === 'graduate';
    $displayName = ($isCompany && $otherUser->company) ? $otherUser->company->name : $otherUser->name;
    $initial = mb_substr($displayName, 0, 1, 'UTF-8');
    $subInfo = $isCompany 
        ? ($otherUser->company->industry ?? 'شريك توظيف وتدريب')
        : ($otherUser->graduateData->specialization ?? $otherUser->specialization ?? ($otherUser->role === 'career_guidance_officer' ? 'مسؤول الإرشاد المهني وشؤون الخريجين' : 'عضو بالمنظومة'));
@endphp

@section('title', 'محادثة مع ' . $displayName)

@push('styles')
<style>
    .msg-main-card {
        border-radius: 20px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.06), 0 4px 10px -2px rgba(15, 23, 42, 0.03);
        background: #ffffff;
        overflow: hidden;
        height: calc(100vh - 170px);
        min-height: 600px;
    }

    .msg-conversations-pane {
        width: 380px;
        max-width: 100%;
        flex-shrink: 0;
    }

    .msg-conv-item {
        transition: all 0.2s ease;
        border-right: 3.5px solid transparent !important;
    }
    .msg-conv-item:hover {
        background: #f8fafc;
    }
    .msg-conv-item.active {
        background: #f0f7ff !important;
        border-right-color: #2b82d8 !important;
    }
    .msg-conv-item.has-unread {
        background: #fafcff;
    }

    .msg-scrollbar::-webkit-scrollbar {
        width: 5px;
    }
    .msg-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .msg-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .msg-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* فقاعات المحادثة العصرية */
    .chat-bubble-me {
        background: linear-gradient(135deg, #2073c8 0%, #2b82d8 100%);
        color: #ffffff;
        border-radius: 18px 18px 4px 18px;
        box-shadow: 0 4px 14px rgba(43, 130, 216, 0.2);
        padding: 0.85rem 1.15rem;
        font-size: 0.92rem;
        line-height: 1.55;
        word-break: break-word;
    }

    .chat-bubble-other {
        background: #ffffff;
        color: #0f172a;
        border: 1px solid #e2e8f0;
        border-radius: 18px 18px 18px 4px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        padding: 0.85rem 1.15rem;
        font-size: 0.92rem;
        line-height: 1.55;
        word-break: break-word;
    }

    .filter-pill.active {
        background: #2b82d8 !important;
        border-color: #2b82d8 !important;
        color: #ffffff !important;
    }

    @media (max-width: 991.98px) {
        .msg-main-card {
            height: calc(100vh - 150px);
        }
        .msg-conversations-pane {
            display: none !important;
        }
        .msg-chat-pane {
            width: 100% !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">

    {{-- Breadcrumbs --}}
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'صندوق الرسائل', 'url' => route('messages.index')],
            ['label' => 'محادثة مع ' . $displayName, 'active' => true],
        ]
    ])

    @if(session('success'))
    <div class="alert alert-success rounded-3 border-0 shadow-sm mb-3 d-flex align-items-center gap-2">
        <i class="fas fa-check-circle fs-5"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger rounded-3 border-0 shadow-sm mb-3 d-flex align-items-center gap-2">
        <i class="fas fa-exclamation-circle fs-5"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- بطاقة المحادثات المتكاملة (Split-Pane Messenger) -->
    <div class="msg-main-card d-flex flex-column flex-lg-row">
        
        <!-- اللوحة اليمنى: قائمة المحادثات (تظهر بالديسكتوب) -->
        @include('messages.partials.conversations-pane', [
            'conversations' => $conversations,
            'activeUserId' => $otherUser->id
        ])

        <!-- اللوحة اليسرى: نافذة المحادثة النشطة (Active Chat Pane) -->
        <div class="msg-chat-pane flex-grow-1 d-flex flex-column bg-white h-100">
            
            <!-- شريط رأس المحادثة النشطة -->
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between gap-2 flex-wrap" 
                 style="border-color: #f1f5f9 !important; background: #ffffff; z-index: 10;">
                
                <div class="d-flex align-items-center gap-3">
                    <!-- زر رجوع في الموبايل -->
                    <a href="{{ route('messages.index') }}" class="btn btn-sm btn-outline-secondary rounded-circle d-lg-none d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="العودة لقائمة المحادثات">
                        <i class="fas fa-arrow-right"></i>
                    </a>

                    <!-- صورة المستخدم / الشعار -->
                    <div class="position-relative flex-shrink-0">
                        @if($isCompany && $otherUser->company && !empty($otherUser->company->logo))
                            <img src="{{ asset('storage/' . $otherUser->company->logo) }}" alt="{{ $displayName }}" class="rounded-circle object-fit-cover shadow-xs" style="width: 46px; height: 46px; border: 1.5px solid #e2e8f0;">
                        @elseif($isGraduate && $otherUser->graduateData && !empty($otherUser->graduateData->profile_image))
                            <img src="{{ asset('storage/' . $otherUser->graduateData->profile_image) }}" alt="{{ $displayName }}" class="rounded-circle object-fit-cover shadow-xs" style="width: 46px; height: 46px; border: 1.5px solid #e2e8f0;">
                        @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs"
                                 style="width: 46px; height: 46px; font-size: 1.15rem; background: {{ $isCompany ? 'linear-gradient(135deg, #1e40af, #2563eb)' : ($isGraduate ? 'linear-gradient(135deg, #059669, #10b981)' : 'linear-gradient(135deg, #2073c8, #2b82d8)') }};">
                                @if($isCompany)
                                    <i class="fas fa-building" style="font-size: 1rem;"></i>
                                @else
                                    {{ $initial }}
                                @endif
                            </div>
                        @endif
                        <span class="position-absolute bottom-0 start-0 p-1 bg-success border border-white rounded-circle" title="نشط الآن"></span>
                    </div>

                    <!-- الاسم والتصنيف -->
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="mb-0 fw-bold text-dark fs-6">{{ $displayName }}</h5>
                            @if($isCompany)
                                <span class="badge rounded-pill" style="font-size: 0.65rem; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">
                                    <i class="fas fa-building me-1"></i>شركة شريكة
                                </span>
                            @elseif($isGraduate)
                                <span class="badge rounded-pill" style="font-size: 0.65rem; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;">
                                    <i class="fas fa-user-graduate me-1"></i>خريج
                                </span>
                            @elseif($otherUser->role === 'career_guidance_officer' || $otherUser->canManageGraduates())
                                <span class="badge rounded-pill" style="font-size: 0.65rem; background: #fefce8; color: #a16207; border: 1px solid #fef08a;">
                                    <i class="fas fa-user-tie me-1"></i>إدارة شؤون الخريجين
                                </span>
                            @else
                                <span class="badge rounded-pill" style="font-size: 0.65rem; background: #f8fafc; color: #475569; border: 1px solid #e2e8f0;">
                                    <i class="fas fa-user-shield me-1"></i>إدارة المنظومة
                                </span>
                            @endif
                        </div>
                        <small class="text-muted d-block text-truncate" style="max-width: 280px; font-size: 0.76rem;">{{ $subInfo }}</small>
                    </div>
                </div>

                <!-- أزرار الإجراءات السريعة في الهيدر -->
                <div class="d-flex align-items-center gap-2">
                    @if($isGraduate)
                        <a href="{{ route('graduate.profile.public', $otherUser->id) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-xs fw-semibold" style="font-size: 0.78rem;">
                            <i class="fas fa-user-tie me-1"></i>السيرة الذاتية
                        </a>
                    @endif
                    <!-- زر حذف المحادثة بالكامل -->
                    <button type="button" 
                            class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-xs fw-semibold d-inline-flex align-items-center gap-1.5"
                            style="font-size: 0.78rem;"
                            onclick="openDeleteConvModal({{ $otherUser->id }}, '{{ addslashes($displayName) }}')">
                        <i class="fas fa-trash-alt"></i>
                        <span>حذف المحادثة</span>
                    </button>
                    <a href="{{ route('messages.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 shadow-xs text-secondary d-none d-lg-inline-flex align-items-center gap-1" style="font-size: 0.78rem;">
                        <i class="fas fa-inbox text-primary"></i>
                        <span>كل المحادثات</span>
                    </a>
                </div>
            </div>

            <!-- مجرى تدفق الرسائل (Message Stream) -->
            <div class="flex-grow-1 p-3 p-md-4 overflow-y-auto msg-scrollbar" id="chat-box" style="background: #f8fafc;">
                @if($messages->isEmpty())
                    <div class="text-center text-muted my-auto py-5" id="chat-empty-state">
                        <div class="rounded-circle bg-white shadow-xs d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 64px; height: 64px; border: 1px solid #e2e8f0;">
                            <i class="fas fa-comments fs-3 text-primary opacity-60"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">لا توجد رسائل سابقة في هذه المحادثة</h6>
                        <p class="small text-muted mb-0">ابدأ التواصل الآن واكتب أول رسالة في الحقل أدناه.</p>
                    </div>
                @else
                    <div class="text-center my-2">
                        <span class="badge bg-white text-muted border px-3 py-1.5 rounded-pill shadow-xs small" style="font-size: 0.72rem;">
                            <i class="fas fa-lock text-warning me-1"></i>المحادثة مشفرة وآمنة عبر النظام
                        </span>
                    </div>

                    @foreach($messages as $msg)
                        @php
                            $isMe = $msg->sender_id == auth()->id();
                        @endphp
                        <div class="d-flex mb-3 {{ $isMe ? 'justify-content-start' : 'justify-content-end' }}" dir="rtl" id="msg-row-{{ $msg->id }}">
                            <div class="d-flex flex-column {{ $isMe ? 'align-items-start' : 'align-items-end' }}" style="max-width: 78%; min-width: 140px;">
                                <div class="{{ $isMe ? 'chat-bubble-me' : 'chat-bubble-other' }}">
                                    {!! nl2br(e($msg->content)) !!}
                                </div>
                                <div class="small text-muted mt-1 px-1.5 d-flex align-items-center gap-1.5 font-monospace" style="font-size: 0.7rem;">
                                    <span>{{ $msg->created_at->format('H:i') }}</span>
                                    <span class="opacity-40">•</span>
                                    <span>{{ $msg->created_at->format('Y-m-d') }}</span>
                                    @if($isMe)
                                        <i class="fas {{ $msg->read_at ? 'fa-check-double text-primary' : 'fa-check text-muted' }}" 
                                           style="font-size: 0.72rem;" 
                                           title="{{ $msg->read_at ? 'تمت القراءة (' . $msg->read_at->format('H:i') . ')' : 'تم الإرسال' }}"></i>
                                    @endif
                                    @if($isMe || auth()->user()->isAdmin())
                                        <button type="button" 
                                                class="btn btn-link text-danger p-0 ms-1 delete-single-msg-btn text-decoration-none" 
                                                data-msg-id="{{ $msg->id }}" 
                                                title="حذف الرسالة"
                                                style="font-size: 0.72rem; line-height: 1; opacity: 0.45; transition: opacity 0.2s;"
                                                onmouseover="this.style.opacity='1'"
                                                onmouseout="this.style.opacity='0.45'">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- منطقة كتابة الرسالة (Modern Typing Bar) -->
            <div class="p-3 bg-white border-top" style="border-color: #f1f5f9 !important;">
                <form action="{{ route('messages.store', $otherUser->id) }}" method="POST" id="message-form">
                    @csrf
                    <div class="d-flex align-items-end gap-2">
                        <div class="flex-grow-1 position-relative">
                            <textarea name="content" 
                                      id="message-content" 
                                      class="form-control rounded-4 border p-3" 
                                      rows="2" 
                                      placeholder="اكتب رسالتك هنا... (اضغط Enter للإرسال، Shift + Enter لسطر جديد)" 
                                      required 
                                      style="resize: none; font-size: 0.9rem; background: #f8fafc; border-color: #cbd5e1 !important; line-height: 1.5;"></textarea>
                        </div>
                        <button class="btn rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm text-white" 
                                type="submit" 
                                id="send-btn"
                                style="width: 48px; height: 48px; background: linear-gradient(135deg, #2073c8 0%, #2b82d8 100%); border: none;" 
                                title="إرسال الرسالة">
                            <i class="fas fa-paper-plane" style="font-size: 1.05rem; transform: rotate(-25deg); margin-left: 2px;"></i>
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>
</div>

{{-- نافذة منبثقة: بدء محادثة جديدة --}}
@include('messages.partials.new-modal', ['availableContacts' => $availableContacts])

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chatBox = document.getElementById('chat-box');
    const msgInput = document.getElementById('message-content');
    const msgForm = document.getElementById('message-form');
    const sendBtn = document.getElementById('send-btn');

    function scrollToBottom() {
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    }

    scrollToBottom();

    if (msgInput) {
        msgInput.focus();

        // التوسيع التلقائي لارتفاع النص
        msgInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (Math.min(this.scrollHeight, 120)) + 'px';
        });

        // الإرسال عند الضغط على Enter (مع استثناء Shift+Enter)
        msgInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (this.value.trim().length > 0) {
                    msgForm.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                }
            }
        });
    }

    // إرسال الرسائل عبر AJAX لتجربة مستخدم تفاعلية فائقة السرعة
    if (msgForm) {
        msgForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const content = msgInput.value.trim();
            if (!content) return;

            // تعطيل مؤقت للزر لمنع التكرار
            sendBtn.disabled = true;
            const originalBtnHtml = sendBtn.innerHTML;
            sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

            const formData = new FormData(msgForm);

            fetch(msgForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(data => { throw new Error(data.message || 'فشل في إرسال الرسالة.'); });
                }
                return res.json();
            })
            .then(data => {
                // إزالة الشاشة الفارغة إن وجدت
                const emptyState = document.getElementById('chat-empty-state');
                if (emptyState) emptyState.remove();

                // إضافة الرسالة الجديدة فورياً إلى الشات
                const timeNow = new Date().toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit' });
                const dateNow = new Date().toISOString().split('T')[0];
                const newMsgId = data.message ? data.message.id : null;

                const bubbleHtml = `
                    <div class="d-flex mb-3 justify-content-start" dir="rtl" ${newMsgId ? `id="msg-row-${newMsgId}"` : ''}>
                        <div class="d-flex flex-column align-items-start" style="max-width: 78%; min-width: 140px;">
                            <div class="chat-bubble-me">
                                ${content.replace(/\n/g, '<br>')}
                            </div>
                            <div class="small text-muted mt-1 px-1.5 d-flex align-items-center gap-1.5 font-monospace" style="font-size: 0.7rem;">
                                <span>${timeNow}</span>
                                <span class="opacity-40">•</span>
                                <span>${dateNow}</span>
                                <i class="fas fa-check text-muted" style="font-size: 0.72rem;" title="تم الإرسال"></i>
                                ${newMsgId ? `
                                    <button type="button" 
                                            class="btn btn-link text-danger p-0 ms-1 delete-single-msg-btn text-decoration-none" 
                                            data-msg-id="${newMsgId}" 
                                            title="حذف الرسالة"
                                            style="font-size: 0.72rem; line-height: 1; opacity: 0.45; transition: opacity 0.2s;"
                                            onmouseover="this.style.opacity='1'"
                                            onmouseout="this.style.opacity='0.45'">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;

                chatBox.insertAdjacentHTML('beforeend', bubbleHtml);
                scrollToBottom();

                // إعادة تهيئة حقل الإدخال
                msgInput.value = '';
                msgInput.style.height = 'auto';
                msgInput.focus();
            })
            .catch(err => {
                alert(err.message || 'حدث خطأ أثناء محاولة إرسال الرسالة.');
            })
            .finally(() => {
                sendBtn.disabled = false;
                sendBtn.innerHTML = originalBtnHtml;
            });
        });
    }

    // حذف رسالة مفردة عبر AJAX
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.delete-single-msg-btn');
        if (!btn) return;

        e.preventDefault();
        const msgId = btn.getAttribute('data-msg-id');
        if (!msgId) return;

        if (!confirm('هل أنت متأكد من رغبتك في حذف هذه الرسالة؟')) {
            return;
        }

        const msgRow = document.getElementById(`msg-row-${msgId}`) || btn.closest('.d-flex.mb-3');
        btn.disabled = true;

        const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}';

        fetch("{{ url('/messages') }}/" + msgId, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                _method: 'DELETE'
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                if (msgRow) {
                    msgRow.style.transition = 'all 0.3s ease';
                    msgRow.style.opacity = '0';
                    msgRow.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        msgRow.remove();
                        const remaining = document.querySelectorAll('#chat-box .d-flex.mb-3');
                        if (remaining.length === 0) {
                            chatBox.innerHTML = `
                                <div class="text-center text-muted my-auto py-5" id="chat-empty-state">
                                    <div class="rounded-circle bg-white shadow-xs d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 64px; height: 64px; border: 1px solid #e2e8f0;">
                                        <i class="fas fa-comments fs-3 text-primary opacity-60"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">لا توجد رسائل سابقة في هذه المحادثة</h6>
                                    <p class="small text-muted mb-0">ابدأ التواصل الآن واكتب أول رسالة في الحقل أدناه.</p>
                                </div>
                            `;
                        }
                    }, 300);
                }
            } else {
                alert(data.message || 'حدث خطأ أثناء حذف الرسالة.');
                btn.disabled = false;
            }
        })
        .catch(err => {
            console.error(err);
            alert('تعذر إتمام عملية الحذف، يرجى المحاولة لاحقاً.');
            btn.disabled = false;
        });
    });
});
</script>
@endpush
