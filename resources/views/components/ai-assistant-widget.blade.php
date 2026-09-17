@php
    $currentUser = auth()->user();
    $userRole = $currentUser->role ?? 'user';
    $userName = $currentUser->name ?? 'مستخدم';
    
    // Quick prompt chips customized by role
    $quickPrompts = [];
    if ($userRole === 'graduate') {
        $quickPrompts = [
            'سجلني في دورة الذكاء الاصطناعي',
            'ما هي أحدث البرامج التدريبية المتاحة لي؟',
            'ابحث لي عن فرص توظيف نشطة',
            'قدم لي على فرصة توظيف مناسبة',
            'ما هي حالة طلباتي للتدريب؟',
        ];
    } elseif ($userRole === 'evaluation_followup') {
        $quickPrompts = [
            'صغ استبيان تقييم دورة تدريبية',
            'اكتب لي تقرير التقييم والمتابعة ومؤشرات الجودة',
            'عرض ملخص الاستبيانات النشطة ونسب الاستجابة',
            'ما هي البرامج التدريبية المتاحة للتقييم؟',
        ];
    } elseif ($userRole === 'career_guidance_officer') {
        $quickPrompts = [
            'ابحث عن خريجي تقنية المعلومات بمعدل 85% فما فوق',
            'أضف وظيفة جديدة لخريجي الهندسة',
            'اكتب لي تقرير الإرشاد المهني والجاهزية',
            'ابحث عن خريجين لديهم سيرة ذاتية وباحثين عن عمل',
            'عرض أحدث الفرص الوظيفية المتاحة للترشيح',
        ];
    } elseif ($userRole === 'media_officer') {
        $quickPrompts = [
            'صغ لي مسودة خبر صحفي عن افتتاح ورشة تدريب خريجين',
            'صغ إعلان رسمي جديد للمنظومة',
            'اكتب لي تقرير الإعلام ونسب التغطية الصحفية',
            'ما هي إحصائيات التغطية والأخبار في المنظومة؟',
        ];
    } elseif ($userRole === 'training_coordinator') {
        $quickPrompts = [
            'اقترح وصغ مسودة دورة تدريبية جديدة في الذكاء الاصطناعي',
            'اكتب لي تقرير التدريب ونسب شغل المقاعد',
            'عرض ملخص إحصائيات البرامج التدريبية',
            'ما هي البرامج التدريبية المتاحة حالياً؟',
        ];
    } elseif ($userRole === 'partnership_officer') {
        $quickPrompts = [
            'أضف شركة جديدة شريكة',
            'عرض قائمة الشركات الشريكة المعتمدة',
            'اكتب لي تقرير الشراكات وسوق العمل',
            'ما هي الفرص الوظيفية المعلنة حالياً؟',
            'اقترح خريجين متميزين لسوق العمل',
        ];
    } elseif ($userRole === 'company') {
        $quickPrompts = [
            'عرض المرشحين لوظائف شركتنا',
            'أضف وظيفة جديدة لشركتنا',
            'ابحث عن خريجين متميزين في تقنية المعلومات',
            'ملخص إحصائيات التوظيف والمرشحين',
            'عرض وتحديث ملف شركتنا',
        ];
    } elseif ($userRole === 'admin') {
        $quickPrompts = [
            'اكتب لي تقرير تنفيذي شامل للجامعة',
            'صغ استبيان تقييم ومتابعة جديد',
            'ابحث عن خريجي تقنية المعلومات بمعدل أعلى من 80%',
            'عرض ملخص إحصائيات المنظومة والمستخدمين',
            'صغ لي خبراً صحفياً رسمياً',
        ];
        $quickPrompts[] = 'تغيير كلمة المرور الخاصة بي';
    } else {
        $quickPrompts = [
            'ما هي البرامج التدريبية المتاحة؟',
            'عرض أحدث الأخبار والإعلانات',
            'عرض معلومات حسابي الشخصي',
            'تغيير كلمة المرور الخاصة بي',
        ];
    }

    if (!in_array('تغيير كلمة المرور الخاصة بي', $quickPrompts)) {
        $quickPrompts[] = 'تغيير كلمة المرور الخاصة بي';
    }

    $roleLabels = [
        'admin' => 'مدير النظام',
        'graduate' => 'خريج جامعة طرابلس',
        'media_officer' => 'مسؤول الإعلام والتغطيات',
        'training_coordinator' => 'منسق التدريب والتطوير',
        'partnership_officer' => 'مسؤول الشراكات',
        'company' => 'ممثل شركة شريكة',
        'career_guidance_officer' => 'مسؤول الإرشاد المهني',
        'evaluation_followup' => 'مسؤول المتابعة والتقييم',
    ];
    $roleLabel = $roleLabels[$userRole] ?? 'عضو المنظومة';
@endphp

<!-- AI Assistant Floating Trigger Button -->
<div id="ai-assistant-fab-container" class="position-fixed" style="bottom: 24px; left: 24px; z-index: 10000; direction: rtl;">
    <button id="ai-assistant-fab" type="button" class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center"
            style="width: 58px; height: 58px; background: linear-gradient(135deg, #0d3882 0%, #1e5ab8 100%); border: 2px solid #eeca3e; box-shadow: 0 8px 25px rgba(13, 56, 130, 0.45); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
            title="المساعد الذكي (AI Copilot)">
        <i class="fas fa-wand-magic-sparkles text-warning fs-4" id="ai-fab-icon"></i>
        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-success border border-light rounded-circle" style="margin-top: 5px; margin-right: 5px;">
            <span class="visually-hidden">متصل</span>
        </span>
    </button>
</div>

<!-- AI Assistant Floating Chat Panel -->
<div id="ai-assistant-panel" class="position-fixed d-none shadow-lg"
     style="bottom: 92px; left: 24px; width: 420px; max-width: calc(100vw - 32px); height: 600px; max-height: calc(100vh - 120px); z-index: 10001; direction: rtl; border-radius: 20px; overflow: hidden; background: #ffffff; border: 1px solid rgba(13, 56, 130, 0.15); box-shadow: 0 20px 45px rgba(13, 56, 130, 0.25); display: flex; flex-direction: column;">

    <!-- Panel Header -->
    <div class="ai-panel-header px-3 py-2 text-white d-flex align-items-center justify-content-between"
         style="background: linear-gradient(135deg, #0d3882 0%, #13469e 100%); border-bottom: 2px solid #eeca3e;">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-white bg-opacity-15 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                <i class="fas fa-robot text-warning fs-5"></i>
            </div>
            <div>
                <div class="fw-bold d-flex align-items-center gap-2" style="font-size: 0.95rem;">
                    <span>المساعد الذكي</span>
                    <span class="badge" style="background-color: rgba(238, 202, 62, 0.25); color: #ffe682; font-size: 0.7rem; border: 1px solid rgba(238, 202, 62, 0.4);">
                        Copilot
                    </span>
                </div>
                <div class="text-white-50" style="font-size: 0.75rem;">
                    <i class="fas fa-shield-alt text-warning me-1"></i>
                    صلاحية: {{ $roleLabel }}
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-1">
            <button id="ai-assistant-clear-btn" type="button" class="btn btn-sm text-white-50 p-1" title="مسح المحادثة" style="font-size: 0.85rem;">
                <i class="fas fa-trash-can"></i>
            </button>
            <button id="ai-assistant-close-btn" type="button" class="btn btn-sm text-white p-1" title="إغلاق">
                <i class="fas fa-times fs-5"></i>
            </button>
        </div>
    </div>

    <!-- Quick Prompts Ribbon (Chips) -->
    <div class="ai-quick-prompts px-3 py-2 bg-light border-bottom" style="overflow-x: auto; white-space: nowrap; -webkit-overflow-scrolling: touch;">
        <div class="d-flex gap-1">
            @foreach($quickPrompts as $prompt)
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill ai-prompt-chip py-1 px-2 text-nowrap"
                        style="font-size: 0.72rem; border-color: rgba(13, 56, 130, 0.25); color: #0d3882; background: #fff;"
                        data-prompt="{{ $prompt }}">
                    <i class="fas fa-sparkles text-warning me-1"></i>{{ $prompt }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Messages Container -->
    <div id="ai-messages-container" class="p-3 flex-grow-1" style="overflow-y: auto; background-color: #f8fafc; display: flex; flex-direction: column; gap: 12px;">
        <!-- Welcome message -->
        <div class="ai-msg ai-msg-assistant d-flex gap-2">
            <div class="ai-avatar rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0"
                 style="width: 30px; height: 30px; background: #0d3882; font-size: 0.8rem;">
                <i class="fas fa-robot text-warning"></i>
            </div>
            <div class="ai-bubble p-2 rounded-3 shadow-sm" style="max-width: 85%; background: #ffffff; border: 1px solid #e2e8f0; font-size: 0.86rem; color: #1e293b; line-height: 1.6;">
                مرحباً بك يا <strong>{{ $userName }}</strong> 👋!
                <br>
                @if($userRole === 'company')
                    أنا مساعدك الذكي في منظومة جامعة طرابلس. يمكنني مساعدتك في استعراض الخريجين المرشحين، جدولة المقابلات، نشر الوظائف، واقتراح أفضل الكفاءات الأكاديمية بحسب صلاحياتك كـ <em>{{ $roleLabel }}</em>.
                @elseif($userRole === 'partnership_officer')
                    أنا مساعدك الذكي في منظومة تدريب وتشغيل الخريجين. يمكنني مساعدتك في إدارة واعتماد الشركات الشريكة، نشر فرص العمل، وترشيح الخريجين بحسب صلاحياتك كـ <em>{{ $roleLabel }}</em>.
                @else
                    أنا مساعدك الذكي في منظومة تدريب وتشغيل الخريجين. يمكنني مساعدتك في استعراض التدريبات، متابعة الطلبات، صياغة الأخبار والبرامج التدريبية بحسب صلاحياتك كـ <em>{{ $roleLabel }}</em>.
                @endif
                <br>
                <span class="text-muted" style="font-size: 0.78rem;">💡 يمكنك كتابة أي استفسار أو اختيار أحد المقترحات السريعة بالأعلى.</span>
            </div>
        </div>
    </div>

    <!-- Typing Indicator (Hidden by default) -->
    <div id="ai-typing-indicator" class="px-3 py-1 d-none" style="background-color: #f8fafc;">
        <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 0.8rem;">
            <div class="spinner-grow spinner-grow-sm text-primary" role="status" style="width: 0.7rem; height: 0.7rem;"></div>
            <span>جاري التفكير ومعالجة البيانات...</span>
        </div>
    </div>

    <!-- Input Box -->
    <div class="ai-panel-footer p-2 bg-white border-top">
        <form id="ai-chat-form" class="d-flex align-items-center gap-2 m-0" onsubmit="return false;">
            <input type="text" id="ai-chat-input" class="form-control form-control-sm rounded-pill px-3"
                   placeholder="اكتب استفسارك أو طلبك هنا..."
                   style="border: 1px solid #cbd5e1; font-size: 0.86rem;" autocomplete="off" />
            <button type="submit" id="ai-chat-send-btn" class="btn btn-primary btn-sm rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                    style="width: 38px; height: 38px; background: #0d3882; border-color: #0d3882;">
                <i class="fas fa-paper-plane text-white" style="font-size: 0.85rem; margin-right: -1px;"></i>
            </button>
        </form>
        <div class="text-center mt-1">
            <span class="text-muted" style="font-size: 0.65rem;">
                الذكاء الاصطناعي مقيد بنظام الصلاحيات (RBAC) • تدريب جامعة طرابلس
            </span>
        </div>
    </div>
</div>

<style>
    #ai-assistant-fab:hover {
        transform: scale(1.08);
    }
    .ai-prompt-chip:hover {
        background-color: #0d3882 !important;
        color: #ffffff !important;
        border-color: #0d3882 !important;
    }
    .ai-msg-user {
        justify-content: flex-start;
        flex-direction: row-reverse;
    }
    .ai-msg-user .ai-bubble {
        background: linear-gradient(135deg, #0d3882 0%, #1e5ab8 100%) !important;
        color: #ffffff !important;
        border-color: #0d3882 !important;
        border-bottom-left-radius: 4px !important;
    }
    .ai-msg-assistant .ai-bubble {
        border-bottom-right-radius: 4px !important;
    }
    .ai-action-card {
        background: #fffbeb;
        border: 1px solid #fef3c7;
        border-right: 4px solid #f59e0b;
        border-radius: 8px;
        padding: 10px;
        margin-top: 8px;
    }
    .ai-tool-badge {
        font-size: 0.7rem;
        background: #e0f2fe;
        color: #0369a1;
        padding: 2px 8px;
        border-radius: 12px;
        display: inline-block;
        margin-bottom: 4px;
        font-family: monospace;
    }
    .ai-inline-prompt-btn {
        font-size: 0.76rem !important;
        font-weight: 600;
        border: 1px solid #0d3882 !important;
        color: #0d3882 !important;
        background: #ffffff !important;
        transition: all 0.2s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        cursor: pointer !important;
    }
    .ai-inline-prompt-btn:hover {
        background: #0d3882 !important;
        color: #ffffff !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 2px 6px rgba(13, 56, 130, 0.25) !important;
    }
    /* Hide native horizontal scrollbars cleanly for prompt chips ribbon */
    .ai-quick-prompts {
        scrollbar-width: none !important;
        -ms-overflow-style: none !important;
    }
    .ai-quick-prompts::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }

    /* Modern subtle scrollbar for Messages Container */
    #ai-messages-container {
        scrollbar-width: thin;
        scrollbar-color: rgba(13, 56, 130, 0.25) transparent;
    }
    #ai-messages-container::-webkit-scrollbar {
        width: 6px;
    }
    #ai-messages-container::-webkit-scrollbar-track {
        background: transparent;
    }
    #ai-messages-container::-webkit-scrollbar-thumb {
        background-color: rgba(13, 56, 130, 0.25);
        border-radius: 10px;
    }
    #ai-messages-container::-webkit-scrollbar-thumb:hover {
        background-color: rgba(13, 56, 130, 0.45);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fabBtn = document.getElementById('ai-assistant-fab');
    const panel = document.getElementById('ai-assistant-panel');
    const closeBtn = document.getElementById('ai-assistant-close-btn');
    const clearBtn = document.getElementById('ai-assistant-clear-btn');
    const chatForm = document.getElementById('ai-chat-form');
    const chatInput = document.getElementById('ai-chat-input');
    const sendBtn = document.getElementById('ai-chat-send-btn');
    const messagesContainer = document.getElementById('ai-messages-container');
    const typingIndicator = document.getElementById('ai-typing-indicator');
    const promptChips = document.querySelectorAll('.ai-prompt-chip');

    let isPanelOpen = false;
    let isWaitingResponse = false;
    let sessionId = 'session_' + Math.random().toString(36).substring(2, 12);

    // Toggle Panel
    function togglePanel() {
        isPanelOpen = !isPanelOpen;
        if (isPanelOpen) {
            panel.classList.remove('d-none');
            panel.style.display = 'flex';
            chatInput.focus();
            scrollToBottom();
        } else {
            panel.classList.add('d-none');
            panel.style.display = 'none';
        }
    }

    if (fabBtn) fabBtn.addEventListener('click', togglePanel);
    if (closeBtn) closeBtn.addEventListener('click', togglePanel);

    // Quick prompts
    const quickPromptsRibbon = document.querySelector('.ai-quick-prompts');
    if (quickPromptsRibbon) {
        quickPromptsRibbon.addEventListener('wheel', function(e) {
            if (e.deltaY !== 0) {
                e.preventDefault();
                quickPromptsRibbon.scrollLeft += e.deltaY;
            }
        }, { passive: false });
    }

    promptChips.forEach(chip => {
        chip.addEventListener('click', function() {
            const promptText = this.getAttribute('data-prompt');
            if (promptText && !isWaitingResponse) {
                chatInput.value = promptText;
                sendMessage(promptText);
            }
        });
    });

    // Inline message prompt buttons delegation (e.g. one-click direct nomination)
    if (messagesContainer) {
        messagesContainer.addEventListener('click', function(e) {
            const promptBtn = e.target.closest('.ai-inline-prompt-btn');
            if (promptBtn) {
                const promptText = promptBtn.getAttribute('data-prompt');
                if (promptText && !isWaitingResponse) {
                    chatInput.value = promptText;
                    sendMessage(promptText);
                }
            }
        });
    }

    // Form submit
    if (chatForm) {
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const text = chatInput.value.trim();
            if (text && !isWaitingResponse) {
                sendMessage(text);
            }
        });
    }

    // Clear history
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            if (confirm('هل ترغب في مسح محادثة المساعد الذكي الحالية؟')) {
                fetch('{{ route("ai-assistant.clear-history") }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({ session_id: sessionId })
                }).then(() => {
                    messagesContainer.innerHTML = '';
                    appendMessage('assistant', 'تم مسح المحادثة بنجاح. كيف يمكنني مساعدتك الآن؟');
                });
            }
        });
    }

    // Helper: Scroll to bottom
    function scrollToBottom() {
        setTimeout(() => {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }, 50);
    }

    // Helper: CSRF Token
    function getCsrfToken() {
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        return tokenMeta ? tokenMeta.getAttribute('content') : '';
    }

    // Helper: Markdown-to-HTML parser (lightweight, zero external dependency)
    function parseMarkdown(text) {
        if (!text) return '';
        let html = text
            // Escape HTML entities to prevent XSS
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            // Headers
            .replace(/^### (.*$)/gim, '<h6 class="fw-bold mt-2 mb-1 text-primary">$1</h6>')
            .replace(/^## (.*$)/gim, '<h5 class="fw-bold mt-2 mb-1 text-primary">$1</h5>')
            .replace(/^# (.*$)/gim, '<h5 class="fw-bold mt-2 mb-1 text-primary">$1</h5>')
            // Bold
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            // Bullet points
            .replace(/^\s*[\-\*]\s+(.*$)/gim, '<li class="ms-3 mb-1">$1</li>')
            // Action Prompt Buttons (#prompt:...)
            .replace(/\[(.*?)\]\(#prompt:(.*?)\)/g, function(match, label, prompt) {
                const cleanPrompt = decodeURIComponent(prompt).replace(/"/g, '&quot;');
                return `<button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-0 px-2 my-1 ai-inline-prompt-btn" data-prompt="${cleanPrompt}"><i class="fas fa-hand-pointer me-1"></i>${label}</button>`;
            })
            // Links
            .replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" target="_blank" class="text-decoration-underline text-primary fw-bold">$1</a>');

        // Markdown Table Parser (convert tables into styled responsive HTML tables)
        html = html.replace(/((?:^[ \t]*\|?.*\|.*$[ \t]*(?:\r?\n|$))+)/gm, function(block) {
            const lines = block.trim().split(/\r?\n/).map(l => l.trim()).filter(l => l.length > 0);
            const sepIdx = lines.findIndex(l => /^\|?\s*[-:]+\s*\|[-:\|\s]*$/.test(l) || (/[-]{3,}/.test(l) && l.includes('|')));
            if (sepIdx < 1) return block; // Not a table

            const parseRow = (line) => {
                let cells = line.split('|');
                if (cells.length > 1 && cells[0].trim() === '') cells.shift();
                if (cells.length > 0 && cells[cells.length - 1].trim() === '') cells.pop();
                return cells.map(c => c.trim());
            };

            const headerCells = parseRow(lines[0]);
            let tableHtml = '<div class="table-responsive my-2 rounded-3 border bg-white shadow-sm" style="max-height: 280px; overflow-x: auto;"><table class="table table-sm table-striped table-hover mb-0 text-center align-middle" style="font-size: 0.74rem;">';
            tableHtml += '<thead class="table-light text-nowrap"><tr>';
            headerCells.forEach(cell => {
                tableHtml += `<th class="py-1.5 px-2 fw-bold text-dark border-bottom">${cell}</th>`;
            });
            tableHtml += '</tr></thead><tbody>';

            for (let i = 0; i < lines.length; i++) {
                if (i <= sepIdx) continue;
                const rowCells = parseRow(lines[i]);
                if (rowCells.length === 0 || rowCells.every(c => c === '')) continue;
                tableHtml += '<tr>';
                rowCells.forEach(c => {
                    tableHtml += `<td class="py-1.5 px-2 text-secondary">${c}</td>`;
                });
                tableHtml += '</tr>';
            }

            tableHtml += '</tbody></table></div>';
            return tableHtml;
        });

        // Line breaks
        html = html.replace(/\n/g, '<br>');

        return html;
    }

    // Append Message to UI
    function appendMessage(role, content, toolExecuted = null, actionProposal = null) {
        const msgDiv = document.createElement('div');
        msgDiv.className = `ai-msg ai-msg-${role} d-flex gap-2`;

        let avatarHtml = '';
        if (role === 'assistant') {
            avatarHtml = `
                <div class="ai-avatar rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0"
                     style="width: 30px; height: 30px; background: #0d3882; font-size: 0.8rem;">
                    <i class="fas fa-robot text-warning"></i>
                </div>`;
        } else {
            avatarHtml = `
                <div class="ai-avatar rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0"
                     style="width: 30px; height: 30px; background: #1e5ab8; font-size: 0.8rem;">
                    <i class="fas fa-user"></i>
                </div>`;
        }

        let bubbleHtml = `<div class="ai-bubble p-2 rounded-3 shadow-sm" style="max-width: 85%; ${role === 'assistant' ? 'background: #ffffff; border: 1px solid #e2e8f0; color: #1e293b;' : ''} font-size: 0.86rem; line-height: 1.6;">`;
        
        if (toolExecuted) {
            bubbleHtml += `<div><span class="ai-tool-badge"><i class="fas fa-bolt me-1"></i>${toolExecuted}</span></div>`;
        }

        bubbleHtml += `<div>${parseMarkdown(content)}</div>`;

        // Render Action Proposal Card if present
        if (actionProposal && actionProposal.type) {
            const propId = 'prop_' + Math.random().toString(36).substring(2, 9);
            let extraInputsHtml = '';
            if (actionProposal.type === 'change_password' && actionProposal.data && actionProposal.data.requires_input) {
                extraInputsHtml = `
                    <div class="my-2 p-2 bg-white rounded border">
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-secondary mb-1" style="font-size: 0.78rem;">
                                <i class="fas fa-key text-warning me-1"></i>كلمة المرور الجديدة (8 أحرف على الأقل):
                            </label>
                            <input type="password" class="form-control form-control-sm ai-new-pass" placeholder="أدخل كلمة المرور الجديدة" style="font-size: 0.85rem;" autocomplete="new-password">
                        </div>
                        <div class="mb-1">
                            <label class="form-label small fw-bold text-secondary mb-1" style="font-size: 0.78rem;">
                                <i class="fas fa-lock text-warning me-1"></i>تأكيد كلمة المرور الجديدة:
                            </label>
                            <input type="password" class="form-control form-control-sm ai-confirm-pass" placeholder="أعد إدخال كلمة المرور" style="font-size: 0.85rem;" autocomplete="new-password">
                        </div>
                        <div class="text-danger small mt-1 d-none ai-pass-error" style="font-size: 0.78rem;"></div>
                    </div>
                `;
            }

            bubbleHtml += `
                <div class="ai-action-card mt-2" id="${propId}">
                    <div class="fw-bold text-dark d-flex align-items-center gap-1 mb-1">
                        <i class="fas fa-circle-question text-warning"></i>
                        <span>${actionProposal.summary || 'إجراء مقترح يتطلب موافقتك:'}</span>
                    </div>
                    <div class="small text-muted mb-2">
                        ${actionProposal.details ? parseMarkdown(actionProposal.details) : ''}
                    </div>
                    ${extraInputsHtml}
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-success py-1 px-3 confirm-action-btn"
                                data-prop-id="${propId}"
                                data-action-type="${actionProposal.type}"
                                data-action-data='${JSON.stringify(actionProposal.data || {})}'>
                            <i class="fas fa-check me-1"></i>تأكيد وحفظ
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 cancel-action-btn"
                                data-prop-id="${propId}">
                            إلغاء
                        </button>
                    </div>
                </div>
            `;
        }

        bubbleHtml += `</div>`;

        msgDiv.innerHTML = avatarHtml + bubbleHtml;
        messagesContainer.appendChild(msgDiv);
        scrollToBottom();

        // Attach event listeners for action confirmation
        if (actionProposal) {
            attachProposalListeners();
        }
    }

    // Attach Proposal Action Listeners
    function attachProposalListeners() {
        document.querySelectorAll('.confirm-action-btn:not([data-bound])').forEach(btn => {
            btn.setAttribute('data-bound', 'true');
            btn.addEventListener('click', function() {
                const propId = this.getAttribute('data-prop-id');
                const actionType = this.getAttribute('data-action-type');
                const actionData = JSON.parse(this.getAttribute('data-action-data') || '{}');
                const card = document.getElementById(propId);

                // Handle interactive password input validation
                if (actionType === 'change_password' && actionData.requires_input) {
                    const newPassInput = card ? card.querySelector('.ai-new-pass') : null;
                    const confirmPassInput = card ? card.querySelector('.ai-confirm-pass') : null;
                    const errorBox = card ? card.querySelector('.ai-pass-error') : null;

                    const newPass = newPassInput ? newPassInput.value.trim() : '';
                    const confirmPass = confirmPassInput ? confirmPassInput.value.trim() : '';

                    if (!newPass || newPass.length < 8) {
                        if (errorBox) {
                            errorBox.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>يجب ألا تقل كلمة المرور عن 8 خانات.';
                            errorBox.classList.remove('d-none');
                        }
                        if (newPassInput) newPassInput.focus();
                        return;
                    }

                    if (newPass !== confirmPass) {
                        if (errorBox) {
                            errorBox.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>كلمتا المرور غير متطابقتين. يرجى التأكد وإعادة المحاولة.';
                            errorBox.classList.remove('d-none');
                        }
                        if (confirmPassInput) confirmPassInput.focus();
                        return;
                    }

                    if (errorBox) errorBox.classList.add('d-none');
                    actionData.new_password = newPass;
                }

                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>جاري التنفيذ...';

                fetch('{{ route("ai-assistant.confirm-action") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({
                        action_type: actionType,
                        action_data: actionData,
                        session_id: sessionId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        card.innerHTML = `<div class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i>${data.message || 'تم تأكيد وحفظ الإجراء بنجاح.'}</div>`;
                    } else {
                        card.innerHTML = `<div class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>${data.message || 'فشل تنفيذ الإجراء.'}</div>`;
                    }
                    scrollToBottom();
                })
                .catch(err => {
                    card.innerHTML = `<div class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>حدث خطأ أثناء الاتصال بالخادم.</div>`;
                });
            });
        });

        document.querySelectorAll('.cancel-action-btn:not([data-bound])').forEach(btn => {
            btn.setAttribute('data-bound', 'true');
            btn.addEventListener('click', function() {
                const propId = this.getAttribute('data-prop-id');
                const card = document.getElementById(propId);
                if (card) {
                    card.innerHTML = '<div class="text-muted small"><i class="fas fa-ban me-1"></i>تم إلغاء الإجراء المقترح.</div>';
                }
            });
        });
    }

    // Send message to server
    function sendMessage(messageText) {
        // Render user message immediately
        appendMessage('user', messageText);
        chatInput.value = '';
        chatInput.disabled = true;
        sendBtn.disabled = true;
        isWaitingResponse = true;
        typingIndicator.classList.remove('d-none');
        scrollToBottom();

        fetch('{{ route("ai-assistant.chat") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({
                message: messageText,
                session_id: sessionId
            })
        })
        .then(res => res.json())
        .then(data => {
            typingIndicator.classList.add('d-none');
            chatInput.disabled = false;
            sendBtn.disabled = false;
            isWaitingResponse = false;
            chatInput.focus();

            if (data.status === 'success' || data.content) {
                appendMessage('assistant', data.content, data.tool_executed, data.action_proposal);
            } else {
                appendMessage('assistant', data.message || 'عذراً، لم أتمكن من معالجة الطلب حالياً.');
            }
        })
        .catch(err => {
            typingIndicator.classList.add('d-none');
            chatInput.disabled = false;
            sendBtn.disabled = false;
            isWaitingResponse = false;
            appendMessage('assistant', 'عذراً، تعذر الاتصال بالخادم. يرجى المحاولة مرة أخرى.');
        });
    }
});
</script>
