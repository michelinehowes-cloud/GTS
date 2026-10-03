{{-- ══════════════════════════════════════════════════════════════════
     نافذة منبثقة: بدء محادثة جديدة
     ══════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="newConversationModal" tabindex="-1" aria-labelledby="newConversationModalLabel" aria-hidden="true" dir="rtl">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 540px;">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <!-- رأس النافذة -->
            <div class="modal-header text-white px-4 py-3 border-0" style="background: linear-gradient(135deg, #1e40af 0%, #2b82d8 100%);">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center text-white" style="width: 38px; height: 38px;">
                        <i class="fas fa-comment-medical text-warning fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold fs-6 mb-0 text-white" id="newConversationModalLabel">بدء محادثة جديدة</h5>
                        <small class="text-white-50" style="font-size: 0.74rem;">اختر جهة الاتصال التي ترغب في مراسلتها</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>

            <!-- إشعار توجيهي مخصص للخريجين -->
            @if(auth()->user()->role === 'graduate')
            <div class="p-3 bg-light border-bottom">
                <div class="p-2.5 rounded-3 d-flex align-items-center gap-2" style="background: #eff6ff; border: 1px solid #bfdbfe; font-size: 0.8rem; color: #1e40af;">
                    <i class="fas fa-shield-alt text-primary fs-6"></i>
                    <div>
                        التواصل المباشر متاح حصراً مع <strong>مسؤولي إدارة شؤون الخريجين</strong> لضمان متابعة استفساراتك وتوجيهك المهني.
                    </div>
                </div>
            </div>
            @else
            <!-- شريط البحث والتصنيفات للمستخدمين الآخرين -->
            <div class="p-3 bg-light border-bottom">
                <div class="position-relative mb-2">
                    <i class="fas fa-search position-absolute top-50 translate-middle-y text-muted" style="right: 14px; font-size: 0.85rem;"></i>
                    <input type="text" id="contactSearchInput" class="form-control rounded-pill pe-5 ps-3" placeholder="ابحث بالاسم، الشركة، أو التخصص..." style="font-size: 0.84rem; height: 40px; background: #ffffff; border-color: #cbd5e1;">
                </div>
                
                @if(auth()->user()->role !== 'graduate')
                <div class="d-flex gap-1.5 flex-wrap" id="contactFilterGroup">
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 filter-pill active" data-filter="all" style="font-size: 0.74rem;">
                        الكل ({{ count($availableContacts ?? []) }})
                    </button>
                    @if(auth()->user()->role !== 'company')
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 filter-pill" data-filter="graduate" style="font-size: 0.74rem;">
                        <i class="fas fa-user-graduate me-1 text-success"></i>الخريجون
                    </button>
                    @endif
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 filter-pill" data-filter="company" style="font-size: 0.74rem;">
                        <i class="fas fa-building me-1 text-primary"></i>الشركات
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 filter-pill" data-filter="staff" style="font-size: 0.74rem;">
                        <i class="fas fa-user-tie me-1 text-warning"></i>الإدارة
                    </button>
                </div>
                @endif
            </div>
            @endif

            <!-- قائمة جهات الاتصال المتاحة -->
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
                                    'career_guidance_officer' => 'مسؤول الإرشاد المهني وشؤون الخريجين',
                                    'partnership_officer' => 'مسؤول الشراكات وعلاقات العمل',
                                    'training_coordinator' => 'منسق التدريب والتطوير',
                                    'admin' => 'مدير النظام العام',
                                    'evaluation_followup' => 'مسؤول المتابعة والتقييم',
                                    'media_officer' => 'مسؤول الإعلام والتواصل',
                                    'staff' => 'موظف شؤون الخريجين'
                                ];
                                $subtitle = $roleMap[$contact->role] ?? 'إدارة شؤون الخريجين';
                            }

                            $initial = mb_substr($contactName, 0, 1, 'UTF-8');
                        @endphp

                        <div class="list-group-item list-group-item-action p-3 d-flex align-items-center justify-content-between gap-2 contact-item"
                             data-category="{{ $category }}"
                             data-search="{{ mb_strtolower($contactName . ' ' . $contact->email . ' ' . $subtitle, 'UTF-8') }}">
                            <div class="d-flex align-items-center gap-3 min-w-0">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0 shadow-xs"
                                     style="width: 42px; height: 42px; font-size: 1.05rem; background: {{ $isContactCompany ? 'linear-gradient(135deg, #1e40af, #2563eb)' : ($isContactGrad ? 'linear-gradient(135deg, #059669, #10b981)' : 'linear-gradient(135deg, #2073c8, #2b82d8)') }};">
                                    @if($isContactCompany)
                                        <i class="fas fa-building" style="font-size: 0.95rem;"></i>
                                    @else
                                        {{ $initial }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="d-flex align-items-center gap-2 mb-0.5">
                                        <h6 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 0.9rem;">{{ $contactName }}</h6>
                                        @if($isContactCompany)
                                            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size: 0.65rem;">شركة</span>
                                        @elseif($isContactGrad)
                                            <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size: 0.65rem;">خريج</span>
                                        @else
                                            <span class="badge rounded-pill" style="font-size: 0.65rem; background: #fefce8; color: #a16207; border: 1px solid #fef08a;">إدارة الخريجين</span>
                                        @endif
                                    </div>
                                    <div class="small text-muted text-truncate" style="font-size: 0.77rem;">
                                        {{ $subtitle }}
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('messages.show', $contact->id) }}" class="btn btn-sm rounded-pill px-3 py-1 flex-shrink-0 fw-bold text-white d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem; background: linear-gradient(135deg, #2073c8, #2b82d8); border: none;">
                                <span>مراسلة</span>
                                <i class="fas fa-paper-plane" style="font-size: 0.7rem;"></i>
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-user-slash fa-2x mb-2 opacity-40"></i>
                            <p class="small mb-0">لا توجد جهات اتصال متاحة للمراسلة حالياً.</p>
                        </div>
                    @endforelse

                    <div id="noSearchResults" class="text-center py-5 text-muted d-none">
                        <i class="fas fa-search fa-2x mb-2 opacity-40"></i>
                        <p class="small mb-0">لم يتم العثور على أي نتائج مطابقة للبحث.</p>
                    </div>
                </div>
            </div>

            <!-- أسفل النافذة -->
            <div class="modal-footer bg-light px-4 py-2.5 border-top d-flex justify-content-between align-items-center">
                <small class="text-muted" style="font-size: 0.74rem;">
                    <i class="fas fa-lock text-warning me-1"></i>جميع المراسلات سرية وآمنة عبر النظام
                </small>
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>
