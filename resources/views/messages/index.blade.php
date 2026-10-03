@extends('layouts.app')

@section('title', 'صندوق الرسائل والمحادثات')

@push('styles')
<style>
    /* تصميم بطاقة المراسلات الحديثة */
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

    .filter-pill.active {
        background: #2b82d8 !important;
        border-color: #2b82d8 !important;
        color: #ffffff !important;
    }

    @media (max-width: 991.98px) {
        .msg-main-card {
            height: auto;
            min-height: auto;
        }
        .msg-conversations-pane {
            width: 100%;
            border-start-width: 0 !important;
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
            ['label' => 'صندوق الرسائل والمحادثات', 'active' => true],
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

    <!-- بطاقة المراسلات المتكاملة (Split-Pane Messenger) -->
    <div class="msg-main-card d-flex flex-column flex-lg-row">
        
        <!-- اللوحة اليمنى: قائمة المحادثات النشطة والبحث -->
        @include('messages.partials.conversations-pane', [
            'conversations' => $conversations,
            'activeUserId' => null
        ])

        <!-- اللوحة اليسرى: شاشة الترحيب والاستعداد عند عدم تحديد محادثة -->
        <div class="flex-grow-1 d-none d-lg-flex flex-column align-items-center justify-content-center text-center p-5" style="background: #f8fafc;">
            <div class="rounded-circle d-flex align-items-center justify-content-center mb-4 shadow-sm" 
                 style="width: 90px; height: 90px; background: linear-gradient(135deg, rgba(35, 116, 201, 0.12), rgba(43, 130, 216, 0.2)); color: #2b82d8;">
                <i class="fas fa-paper-plane" style="font-size: 2.5rem; transform: rotate(-20deg); margin-left: 5px;"></i>
            </div>

            <h4 class="fw-bold text-dark mb-2">مركز المحادثات والتواصل المباشر</h4>
            
            <p class="text-muted mb-4 mx-auto" style="max-width: 500px; font-size: 0.95rem; line-height: 1.6;">
                @if(auth()->user()->role === 'graduate')
                    مرحباً بك! تتيح لك المنصة التواصل المباشر مع <strong>مسؤولي إدارة شؤون الخريجين</strong> لطرح استفساراتك وتلقي الدعم المهني والأكاديمي.
                @elseif(auth()->user()->role === 'company')
                    مرحباً بك! يمكنك التواصل المباشر مع الخريجين المرشحين ومسؤولي الشراكات بالجامعة.
                @else
                    مرحباً بك! يمكنك إدارة محادثات النظام والتواصل الفوري مع الأطراف المصرح بها بكل سهولة وسرية.
                @endif
            </p>

            <button type="button" class="btn text-white rounded-pill px-4 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center gap-2"
                    data-bs-toggle="modal" data-bs-target="#newConversationModal"
                    style="background: linear-gradient(135deg, #2073c8 0%, #2b82d8 100%); font-size: 0.92rem; border: none;">
                <i class="fas fa-pen-to-square"></i>
                <span>بدء محادثة جديدة الآن</span>
            </button>
        </div>

    </div>
</div>

{{-- نافذة منبثقة: بدء محادثة جديدة --}}
@include('messages.partials.new-modal', ['availableContacts' => $availableContacts])

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // تصفية المحادثات في القائمة الجانبية
    const convSearch = document.getElementById('conversationFilterInput');
    const convItems = document.querySelectorAll('.msg-conv-item');

    if (convSearch) {
        convSearch.addEventListener('input', function() {
            const q = this.value.trim().toLowerCase();
            convItems.forEach(item => {
                const name = item.getAttribute('data-name') || '';
                if (q === '' || name.includes(q)) {
                    item.style.setProperty('display', 'flex', 'important');
                } else {
                    item.style.setProperty('display', 'none', 'important');
                }
            });
        });
    }

    // تصفية جهات الاتصال داخل المودال
    const contactSearch = document.getElementById('contactSearchInput');
    const filterPills = document.querySelectorAll('#contactFilterGroup .filter-pill');
    const contactItems = document.querySelectorAll('.contact-item');
    const noResults = document.getElementById('noSearchResults');

    let currentFilter = 'all';

    function applyContactFilter() {
        const query = contactSearch ? contactSearch.value.trim().toLowerCase() : '';
        let visibleCount = 0;

        contactItems.forEach(item => {
            const category = item.getAttribute('data-category');
            const searchData = item.getAttribute('data-search') || '';

            const matchesCategory = (currentFilter === 'all' || category === currentFilter);
            const matchesQuery = query === '' || searchData.includes(query);

            if (matchesCategory && matchesQuery) {
                item.style.setProperty('display', 'flex', 'important');
                visibleCount++;
            } else {
                item.style.setProperty('display', 'none', 'important');
            }
        });

        if (noResults) {
            noResults.classList.toggle('d-none', !(visibleCount === 0 && contactItems.length > 0));
        }
    }

    if (contactSearch) {
        contactSearch.addEventListener('input', applyContactFilter);
    }

    filterPills.forEach(pill => {
        pill.addEventListener('click', function() {
            filterPills.forEach(p => {
                p.classList.remove('active', 'btn-primary');
                p.classList.add('btn-outline-secondary');
            });
            this.classList.add('active', 'btn-primary');
            this.classList.remove('btn-outline-secondary');

            currentFilter = this.getAttribute('data-filter');
            applyContactFilter();
        });
    });
});
</script>
@endpush
