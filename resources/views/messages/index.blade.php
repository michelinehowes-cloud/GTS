@extends('layouts.app')

@section('title', 'صندوق الرسائل والمحادثات')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">لوحة التحكم</a></li>
                    <li class="breadcrumb-item active" aria-current="page">صندوق الرسائل</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="fas fa-envelope text-primary"></i>
                <span>صندوق الرسائل والمحادثات</span>
            </h1>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-primary rounded-pill px-3.5 py-2 fw-bold text-nowrap shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#newConversationModal">
                <i class="fas fa-pen-to-square"></i>
                <span>بدء محادثة جديدة</span>
            </button>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-bold text-nowrap shadow-sm">
                <i class="fas fa-arrow-right me-1"></i> العودة للوحة التحكم
            </a>
        </div>
    </div>

    <!-- Conversations Container -->
    <div class="card border-0 rounded-4 shadow-sm overflow-hidden" style="background: #ffffff;">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="card-title mb-0 fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                <i class="fas fa-comments text-primary"></i>
                <span>جميع المحادثات النشطة</span>
            </h5>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 shadow-none" data-bs-toggle="modal" data-bs-target="#newConversationModal">
                    <i class="fas fa-plus me-1"></i>رسالة جديدة
                </button>
                <span class="badge bg-light text-muted border px-2.5 py-1.5 rounded-pill font-monospace">
                    {{ count($conversations) }} محادثة
                </span>
            </div>
        </div>

        @if(empty($conversations))
            <div class="empty-state py-5 text-center my-4 px-3">
                <div class="rounded-circle bg-light text-muted d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px;">
                    <i class="fas fa-inbox fa-2x text-primary opacity-50"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">صندوق الوارد فارغ</h4>
                <p class="text-muted mb-4 mx-auto" style="max-width: 460px; font-size: 0.92rem;">
                    @if(auth()->user()->role === 'graduate')
                        لم تبدأ أي محادثة بعد. يمكنك مراسلة الشركات الشريكة للاستفسار عن فرص العمل والتدريب، أو مراسلة إدارة المنظومة للدعم والإرشاد.
                    @elseif(auth()->user()->role === 'company')
                        لا توجد محادثات نشطة حالياً. يمكنك بدء التواصل المباشر مع الخريجين المرشحين أو مراسلة مسؤولي الشراكات بالجامعة.
                    @else
                        لا توجد محادثات حالياً. يمكنك التواصل المباشر مع أي مستخدم أو شركة أو خريج بالمنظومة بالضغط على الزر أدناه.
                    @endif
                </p>
                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <button type="button" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#newConversationModal">
                        <i class="fas fa-pen-to-square"></i>
                        <span>بدء محادثة جديدة الآن</span>
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2.5 fw-bold shadow-sm">
                        <i class="fas fa-home me-1"></i> العودة للرئيسية
                    </a>
                </div>
            </div>
        @else
            <div class="list-group list-group-flush">
                @foreach($conversations as $userId => $conv)
                    @php
                        $other = $conv['user'];
                        $isCompany = $other && $other->role === 'company';
                        $displayName = ($isCompany && $other->company) ? $other->company->name : ($other ? $other->name : 'مستخدم');
                        $hasUnread = $conv['unread_count'] > 0;
                        $initial = mb_substr($displayName, 0, 1, 'UTF-8');
                    @endphp
                    <a href="{{ route('messages.show', $userId) }}" 
                       class="list-group-item list-group-item-action p-3.5 p-md-4 border-bottom text-decoration-none transition-hover {{ $hasUnread ? 'bg-light' : '' }}"
                       style="transition: all 0.2s ease; border-right: 4px solid {{ $hasUnread ? '#2563eb' : 'transparent' }} !important;">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3 min-w-0 flex-grow-1">
                                <!-- Avatar -->
                                <div class="position-relative flex-shrink-0">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm" 
                                         style="width: 52px; height: 52px; font-size: 1.25rem; font-weight: bold; background: {{ $isCompany ? 'linear-gradient(135deg, #0d3882 0%, #1e40af 100%)' : 'linear-gradient(135deg, #059669 0%, #10b981 100%)' }}; color: #ffffff;">
                                        @if($isCompany)
                                            <i class="fas fa-building" style="font-size: 1.15rem;"></i>
                                        @else
                                            {{ $initial }}
                                        @endif
                                    </div>
                                    @if($hasUnread)
                                        <span class="position-absolute top-0 start-0 translate-middle p-1.5 bg-danger border border-white rounded-circle"></span>
                                    @endif
                                </div>

                                <!-- User Details & Message Preview -->
                                <div class="min-w-0 flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <h5 class="mb-0 fw-bold {{ $hasUnread ? 'text-dark' : 'text-secondary' }}" style="font-size: 1.02rem;">
                                            {{ $displayName }}
                                        </h5>
                                        @if($isCompany)
                                            <span class="badge rounded-pill px-2 py-0.5" style="background: rgba(13, 56, 130, 0.08); color: #0d3882; font-size: 0.72rem; border: 1px solid rgba(13, 56, 130, 0.2);">
                                                <i class="fas fa-building me-1"></i>شركة شريكة
                                            </span>
                                        @elseif($other && $other->role === 'graduate')
                                            <span class="badge rounded-pill px-2 py-0.5" style="background: rgba(5, 150, 105, 0.08); color: #059669; font-size: 0.72rem; border: 1px solid rgba(5, 150, 105, 0.2);">
                                                <i class="fas fa-user-graduate me-1"></i>خريج
                                            </span>
                                        @elseif($other)
                                            <span class="badge rounded-pill px-2 py-0.5 bg-light text-primary border" style="font-size: 0.72rem;">
                                                <i class="fas fa-user-shield me-1"></i>إدارة المنظومة
                                            </span>
                                        @endif
                                    </div>
                                    <p class="mb-0 text-truncate text-muted" style="max-width: 550px; font-size: 0.88rem; color: {{ $hasUnread ? '#1e293b' : '#64748b' }} !important; font-weight: {{ $hasUnread ? '600' : '400' }};">
                                        @if($conv['last_message']->sender_id == auth()->id())
                                            <i class="fas fa-reply text-muted ms-1" style="font-size: 0.75rem;"></i>
                                            <span class="text-secondary fw-semibold">أنت: </span>
                                        @endif
                                        {{ $conv['last_message']->content }}
                                    </p>
                                </div>
                            </div>

                            <!-- Meta / Time / Badge -->
                            <div class="text-end flex-shrink-0 d-flex flex-column align-items-end gap-1.5">
                                <div class="small text-muted font-monospace" style="font-size: 0.76rem;">
                                    {{ $conv['last_message']->created_at->diffForHumans() }}
                                </div>
                                @if($hasUnread)
                                    <span class="badge rounded-pill bg-danger px-2.5 py-1 shadow-sm" style="font-size: 0.72rem;">
                                        {{ $conv['unread_count'] }} {{ $conv['unread_count'] == 1 ? 'جديدة' : 'رسائل جديدة' }}
                                    </span>
                                @else
                                    <i class="fas fa-chevron-left text-muted opacity-50" style="font-size: 0.8rem;"></i>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- مودال بدء محادثة جديدة (New Conversation Modal) -->
<div class="modal fade" id="newConversationModal" tabindex="-1" aria-labelledby="newConversationModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 560px;">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header text-white px-4 py-3 border-0" style="background: linear-gradient(135deg, #0d3882 0%, #1565c0 100%);">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center text-white" style="width: 38px; height: 38px;">
                        <i class="fas fa-comment-medical text-warning fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold fs-6 mb-0 text-white" id="newConversationModalLabel">بدء محادثة جديدة</h5>
                        <small class="text-white-50" style="font-size: 0.76rem;">اختر جهة الاتصال التي ترغب في مراسلتها</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>

            <!-- Modal Search & Filters -->
            <div class="p-3 bg-light border-bottom">
                <div class="position-relative mb-2">
                    <i class="fas fa-search position-absolute top-50 translate-middle-y text-muted" style="right: 15px;"></i>
                    <input type="text" id="contactSearchInput" class="form-control rounded-pill pe-5 ps-3" placeholder="ابحث بالاسم، اسم الشركة، أو التخصص..." style="font-size: 0.88rem; height: 42px;">
                </div>
                <!-- Filter Pills -->
                <div class="d-flex gap-1 flex-wrap" id="contactFilterGroup">
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 filter-pill active" data-filter="all" style="font-size: 0.76rem;">
                        الكل ({{ count($availableContacts ?? []) }})
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 filter-pill" data-filter="company" style="font-size: 0.76rem;">
                        <i class="fas fa-building me-1 text-primary"></i>الشركات
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 filter-pill" data-filter="graduate" style="font-size: 0.76rem;">
                        <i class="fas fa-user-graduate me-1 text-success"></i>الخريجون
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 filter-pill" data-filter="staff" style="font-size: 0.76rem;">
                        <i class="fas fa-user-shield me-1 text-warning"></i>الإدارة والمنسقون
                    </button>
                </div>
            </div>

            <!-- Modal Body (Contacts List) -->
            <div class="modal-body p-0" style="max-height: 380px; overflow-y: auto;">
                <div class="list-group list-group-flush" id="contactsListContainer">
                    @forelse($availableContacts ?? [] as $contact)
                        @php
                            $isContactCompany = $contact->role === 'company';
                            $isContactGrad = $contact->role === 'graduate';
                            $contactName = ($isContactCompany && $contact->company) ? $contact->company->name : $contact->name;
                            $category = $isContactCompany ? 'company' : ($isContactGrad ? 'graduate' : 'staff');
                            
                            $subtitle = 'عضو بالمنظومة';
                            if ($isContactCompany) {
                                $subtitle = $contact->company->industry ?? 'شركة شريكة معتمدة';
                            } elseif ($isContactGrad) {
                                $subtitle = $contact->graduateData->specialization ?? $contact->specialization ?? 'خريج جامعة طرابلس';
                            } else {
                                $roleMap = [
                                    'admin' => 'مدير النظام العام',
                                    'training_coordinator' => 'منسق التدريب والتطوير',
                                    'partnership_officer' => 'مسؤول الشراكات وسوق العمل',
                                    'career_guidance_officer' => 'مسؤول الإرشاد المهني',
                                    'evaluation_followup' => 'مسؤول المتابعة والتقييم',
                                    'media_officer' => 'مسؤول الإعلام',
                                    'staff' => 'موظف المنظومة'
                                ];
                                $subtitle = $roleMap[$contact->role] ?? 'إدارة المنظومة';
                            }

                            $initial = mb_substr($contactName, 0, 1, 'UTF-8');
                        @endphp
                        <div class="list-group-item list-group-item-action p-3 d-flex align-items-center justify-content-between gap-2 contact-item"
                             data-category="{{ $category }}"
                             data-search="{{ mb_strtolower($contactName . ' ' . $contact->email . ' ' . $subtitle, 'UTF-8') }}">
                            <div class="d-flex align-items-center gap-3 min-w-0">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0 shadow-xs"
                                     style="width: 44px; height: 44px; font-size: 1.1rem; background: {{ $isContactCompany ? 'linear-gradient(135deg, #0d3882, #1e40af)' : ($isContactGrad ? 'linear-gradient(135deg, #059669, #10b981)' : 'linear-gradient(135deg, #d97706, #f59e0b)') }};">
                                    @if($isContactCompany)
                                        <i class="fas fa-building" style="font-size: 1rem;"></i>
                                    @else
                                        {{ $initial }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="d-flex align-items-center gap-2 mb-0.5">
                                        <h6 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 0.92rem;">{{ $contactName }}</h6>
                                        @if($isContactCompany)
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill" style="font-size: 0.65rem;">شركة</span>
                                        @elseif($isContactGrad)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill" style="font-size: 0.65rem;">خريج</span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-50 rounded-pill" style="font-size: 0.65rem;">إدارة</span>
                                        @endif
                                    </div>
                                    <div class="small text-muted text-truncate" style="font-size: 0.78rem;">
                                        {{ $subtitle }}
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('messages.show', $contact->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1 flex-shrink-0 fw-bold d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                                <span>مراسلة</span>
                                <i class="fas fa-paper-plane" style="font-size: 0.7rem;"></i>
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-users-slash fa-2x mb-2 opacity-50"></i>
                            <p class="small mb-0">لا توجد جهات اتصال متاحة حالياً.</p>
                        </div>
                    @endforelse
                    <div id="noSearchResults" class="text-center py-5 text-muted d-none">
                        <i class="fas fa-search fa-2x mb-2 opacity-50"></i>
                        <p class="small mb-0">لم يتم العثور على أي نتائج مطابقة للبحث.</p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-light px-4 py-2.5 border-top d-flex justify-content-between align-items-center">
                <small class="text-muted" style="font-size: 0.76rem;">
                    <i class="fas fa-lock text-warning me-1"></i>كافة المحادثات داخل المنظومة آمنة وخاصة
                </small>
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('contactSearchInput');
    const filterPills = document.querySelectorAll('#contactFilterGroup .filter-pill');
    const contactItems = document.querySelectorAll('.contact-item');
    const noResults = document.getElementById('noSearchResults');

    let currentFilter = 'all';

    function applyFilter() {
        const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
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
            if (visibleCount === 0 && contactItems.length > 0) {
                noResults.classList.remove('d-none');
            } else {
                noResults.classList.add('d-none');
            }
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', applyFilter);
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
            applyFilter();
        });
    });
});
</script>
@endpush
@endsection
