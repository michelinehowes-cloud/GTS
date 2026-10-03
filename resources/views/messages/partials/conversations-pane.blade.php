{{-- ══════════════════════════════════════════════════════════════════
     قائمة المحادثات النشطة (اللوحة الجانبية للمراسلات)
     ══════════════════════════════════════════════════════════════════ --}}
@php
    $activeId = $activeUserId ?? null;
@endphp

<div class="msg-conversations-pane d-flex flex-column h-100 bg-white border-start" style="border-color: #e2e8f0 !important;">
    <!-- شريط البحث السريع والزر العلوي -->
    <div class="p-3 border-bottom" style="border-color: #f1f5f9 !important; background: #ffffff;">
        <div class="d-flex align-items-center justify-content-between gap-2 mb-2.5">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; background: linear-gradient(135deg, #2073c8, #2b82d8);">
                    <i class="fas fa-comments" style="font-size: 0.85rem;"></i>
                </div>
                <h6 class="mb-0 fw-bold text-dark fs-6">المحادثات</h6>
                <span class="badge rounded-pill bg-light text-muted border px-2 py-0.5" style="font-size: 0.72rem;">
                    {{ count($conversations) }}
                </span>
            </div>

            <button type="button" class="btn btn-sm text-white rounded-pill px-3 py-1 shadow-xs fw-bold d-inline-flex align-items-center gap-1.5"
                    data-bs-toggle="modal" data-bs-target="#newConversationModal"
                    style="background: linear-gradient(135deg, #2073c8 0%, #2b82d8 100%); font-size: 0.78rem; border: none;">
                <i class="fas fa-plus"></i>
                <span>محادثة جديدة</span>
            </button>
        </div>

        <!-- حقل البحث الفوري في المحادثات -->
        <div class="position-relative">
            <i class="fas fa-search position-absolute top-50 translate-middle-y text-muted" style="right: 12px; font-size: 0.85rem;"></i>
            <input type="text" id="conversationFilterInput" 
                   class="form-control rounded-pill pe-5 ps-3" 
                   placeholder="بحث في أسماء المحادثات..." 
                   style="font-size: 0.82rem; height: 38px; background: #f8fafc; border-color: #e2e8f0;">
        </div>
    </div>

    <!-- قائمة المحادثات القابلة للتمرير -->
    <div class="flex-grow-1 overflow-y-auto msg-scrollbar" id="conversationsListContainer" style="max-height: calc(100vh - 270px); min-height: 480px;">
        @if(empty($conversations))
            <div class="text-center py-5 px-3 text-muted">
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 58px; height: 58px;">
                    <i class="fas fa-inbox text-muted opacity-50 fs-4"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1 fs-6">لا توجد محادثات نشطة</h6>
                <p class="small text-muted mb-3" style="font-size: 0.8rem;">
                    @if(auth()->user()->role === 'graduate')
                        يمكنك بدء محادثة مع إدارة شؤون الخريجين لأي استفسار.
                    @else
                        ابدأ أول محادثة الآن من خلال زر محادثة جديدة.
                    @endif
                </p>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#newConversationModal">
                    <i class="fas fa-pen-to-square me-1"></i> بدء محادثة
                </button>
            </div>
        @else
            @foreach($conversations as $cUserId => $conv)
                @php
                    $other = $conv['user'];
                    $isCompany = $other && $other->role === 'company';
                    $isGraduate = $other && $other->role === 'graduate';
                    $displayName = ($isCompany && $other->company) ? $other->company->name : ($other ? $other->name : 'مستخدم');
                    $hasUnread = $conv['unread_count'] > 0;
                    $isActive = $activeId && $activeId == $cUserId;
                    $initial = mb_substr($displayName, 0, 1, 'UTF-8');
                @endphp

                <a href="{{ route('messages.show', $cUserId) }}" 
                   class="msg-conv-item d-flex align-items-center justify-content-between p-3 border-bottom text-decoration-none transition-all {{ $isActive ? 'active' : '' }} {{ $hasUnread ? 'has-unread' : '' }}"
                   data-name="{{ mb_strtolower($displayName, 'UTF-8') }}"
                   style="border-color: #f1f5f9 !important;">
                    
                    <div class="d-flex align-items-center gap-3 min-w-0 flex-grow-1">
                        <!-- صورة أو رمز المستخدم -->
                        <div class="position-relative flex-shrink-0">
                            @if($isCompany && $other->company && !empty($other->company->logo))
                                <img src="{{ asset('storage/' . $other->company->logo) }}" alt="{{ $displayName }}" class="rounded-circle object-fit-cover shadow-xs" style="width: 44px; height: 44px; border: 1.5px solid #e2e8f0;">
                            @elseif($isGraduate && $other->graduateData && !empty($other->graduateData->profile_image))
                                <img src="{{ asset('storage/' . $other->graduateData->profile_image) }}" alt="{{ $displayName }}" class="rounded-circle object-fit-cover shadow-xs" style="width: 44px; height: 44px; border: 1.5px solid #e2e8f0;">
                            @else
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs"
                                     style="width: 44px; height: 44px; font-size: 1.05rem; background: {{ $isCompany ? 'linear-gradient(135deg, #1e40af, #2563eb)' : ($isGraduate ? 'linear-gradient(135deg, #059669, #10b981)' : 'linear-gradient(135deg, #2073c8, #2b82d8)') }};">
                                    @if($isCompany)
                                        <i class="fas fa-building" style="font-size: 0.95rem;"></i>
                                    @else
                                        {{ $initial }}
                                    @endif
                                </div>
                            @endif

                            @if($hasUnread)
                                <span class="position-absolute top-0 start-0 translate-middle p-1.5 bg-danger border border-white rounded-circle"></span>
                            @endif
                        </div>

                        <!-- تفاصيل جهة الاتصال والرسالة الأخيرة -->
                        <div class="min-w-0 flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between mb-0.5">
                                <h6 class="mb-0 text-truncate fw-bold {{ $isActive ? 'text-primary' : 'text-dark' }}" style="font-size: 0.9rem; max-width: 170px;">
                                    {{ $displayName }}
                                </h6>
                                <span class="small text-muted font-monospace" style="font-size: 0.7rem;">
                                    {{ $conv['last_message']->created_at->diffForHumans(null, true, true) }}
                                </span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between gap-1">
                                <p class="mb-0 text-truncate text-muted small" style="font-size: 0.78rem; max-width: 190px; color: {{ $hasUnread ? '#0f172a' : '#64748b' }} !important; font-weight: {{ $hasUnread ? '700' : '400' }};">
                                    @if($conv['last_message']->sender_id == auth()->id())
                                        <span class="text-primary fw-semibold">أنت: </span>
                                    @endif
                                    {{ $conv['last_message']->content }}
                                </p>

                                @if($hasUnread)
                                    <span class="badge rounded-pill bg-danger px-2 py-0.5 text-white" style="font-size: 0.65rem;">
                                        {{ $conv['unread_count'] }}
                                    </span>
                                @endif
                            </div>

                            <!-- تصنيف الدور -->
                            <div class="mt-1">
                                @if($isCompany)
                                    <span class="badge rounded-pill" style="font-size: 0.65rem; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">
                                        <i class="fas fa-building me-1"></i>شركة
                                    </span>
                                @elseif($isGraduate)
                                    <span class="badge rounded-pill" style="font-size: 0.65rem; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;">
                                        <i class="fas fa-user-graduate me-1"></i>خريج
                                    </span>
                                @elseif($other && ($other->role === 'career_guidance_officer' || $other->canManageGraduates()))
                                    <span class="badge rounded-pill" style="font-size: 0.65rem; background: #fefce8; color: #a16207; border: 1px solid #fef08a;">
                                        <i class="fas fa-user-tie me-1"></i>إدارة شؤون الخريجين
                                    </span>
                                @else
                                    <span class="badge rounded-pill" style="font-size: 0.65rem; background: #f8fafc; color: #475569; border: 1px solid #e2e8f0;">
                                        <i class="fas fa-user-shield me-1"></i>إدارة المنظومة
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        @endif
    </div>
</div>
