/* Extracted from resources/views/layouts/app.blade.php so browsers can cache it. */
    // ==========================================
    // Notifications Drawer Functions
    // ==========================================
    function openNotificationsDrawer() {
        const drawer = document.getElementById('notificationsDrawer');
        const panel = document.getElementById('notifDrawerPanel');
        const backdrop = document.getElementById('notifDrawerBackdrop');
        if (!drawer || !panel || !backdrop) return;

        drawer.style.pointerEvents = 'all';
        backdrop.style.pointerEvents = 'all';
        backdrop.style.opacity = '1';
        panel.style.transform = 'translateX(0)';
        document.body.style.overflow = 'hidden';

        // جلب الإشعارات
        loadNotificationsInDrawer();
    }

    function closeNotificationsDrawer() {
        const drawer = document.getElementById('notificationsDrawer');
        const panel = document.getElementById('notifDrawerPanel');
        const backdrop = document.getElementById('notifDrawerBackdrop');
        if (!drawer || !panel || !backdrop) return;

        panel.style.transform = 'translateX(100%)';
        backdrop.style.opacity = '0';
        backdrop.style.pointerEvents = 'none';
        setTimeout(() => {
            drawer.style.pointerEvents = 'none';
        }, 350);
        document.body.style.overflow = '';
    }

    function loadNotificationsInDrawer() {
        const body = document.getElementById('notifDrawerBody');
        const subtitle = document.getElementById('notifDrawerSubtitle');
        if (!body) return;

        body.innerHTML = `<div class="text-center py-5 text-muted"><div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div><p class="small">جاري تحميل الإشعارات...</p></div>`;

        fetch('/notifications/api/notifications', { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(data => {
                const notifications = data.notifications || [];
                const unreadCount = data.unread_count ?? 0;
                if (subtitle) subtitle.textContent = unreadCount > 0 ? `${unreadCount} غير مقروء` : 'لا توجد إشعارات جديدة';

                if (notifications.length === 0) {
                    body.innerHTML = `<div class="text-center py-5 text-muted"><i class="fas fa-bell-slash fa-3x mb-3 opacity-25 d-block"></i><p class="small">لا توجد إشعارات حتى الآن</p></div>`;
                    return;
                }

                const iconConfig = {
                    success: { icon: 'check-circle', bg: '#ecfdf5', color: '#059669' },
                    danger:  { icon: 'exclamation-circle', bg: '#fef2f2', color: '#dc2626' },
                    warning: { icon: 'exclamation-triangle', bg: '#fffbeb', color: '#d97706' },
                    info:    { icon: 'info-circle', bg: '#eff6ff', color: '#2563eb' },
                    message: { icon: 'envelope', bg: '#f0fdf4', color: '#16a34a' }
                };

                let html = '';
                notifications.forEach(n => {
                    const isUnread = !n.is_read;
                    const ic = iconConfig[n.type] || iconConfig.info;
                    const timeStr = n.created_at ? new Date(n.created_at).toLocaleDateString('ar-LY', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : '';
                    html += `
                    <div class="notif-drawer-item ${isUnread ? 'is-unread' : 'is-read'}" data-notif-id="${n.id}">
                        <a href="/notifications/${n.id}" class="d-flex align-items-start text-decoration-none flex-grow-1 min-w-0" style="color:inherit;">
                            <div class="ms-3 mt-0.5 flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:${ic.bg};color:${ic.color};">
                                <i class="fas fa-${ic.icon} fs-6"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex justify-content-between align-items-baseline mb-1">
                                    <h6 class="notif-title text-truncate mb-0" style="max-width:180px;">${n.title || 'إشعار جديد'}</h6>
                                    <small class="notif-time ms-2">${timeStr}</small>
                                </div>
                                <p class="notif-message text-truncate mb-0" style="max-width:210px;">${n.message || ''}</p>
                            </div>
                            ${isUnread ? '<span class="rounded-circle bg-primary me-2 mt-1.5 flex-shrink-0" style="width:8px;height:8px;min-width:8px;" title="غير مقروء"></span>' : ''}
                        </a>
                        ${isUnread ? `
                            <button type="button" onclick="markDrawerNotificationAsRead(${n.id}, this)" class="notif-check-btn" title="تحديد كمقروء وإخفاء">
                                <i class="fas fa-check"></i>
                            </button>
                        ` : ''}
                    </div>`;
                });
                body.innerHTML = html;
            })
            .catch(() => {
                body.innerHTML = `<div class="text-center py-5 text-danger"><i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i><p class="small">تعذر تحميل الإشعارات</p></div>`;
            });
    }

    window.markDrawerNotificationAsRead = function(id, btn) {
        if (window.event) {
            window.event.preventDefault();
            window.event.stopPropagation();
        }
        const row = btn ? btn.closest('.notif-drawer-item') : document.querySelector(`.notif-drawer-item[data-notif-id="${id}"]`);
        if (row) {
            row.style.transition = 'all 0.35s cubic-bezier(0.4, 0, 0.2, 1)';
            row.style.opacity = '0';
            row.style.transform = 'translateX(40px)';
            row.style.maxHeight = '0px';
            row.style.paddingTop = '0';
            row.style.paddingBottom = '0';
            row.style.margin = '0';
            row.style.overflow = 'hidden';
        }
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (row) setTimeout(() => {
                row.remove();
                const remaining = document.querySelectorAll('#notifDrawerBody .notif-drawer-item');
                if (remaining.length === 0) {
                    const body = document.getElementById('notifDrawerBody');
                    if (body) body.innerHTML = `<div class="text-center py-5 text-muted"><i class="fas fa-bell-slash fa-3x mb-3 opacity-25 d-block"></i><p class="small">لا توجد إشعارات حتى الآن</p></div>`;
                }
            }, 350);
            const newCount = typeof data.unread_count !== 'undefined' ? data.unread_count : 0;
            updateUnreadBadge(newCount);
            const subtitle = document.getElementById('notifDrawerSubtitle');
            if (subtitle) subtitle.textContent = newCount > 0 ? `${newCount} غير مقروء` : 'لا توجد إشعارات جديدة';
        })
        .catch(err => {
            console.error(err);
            if (row) {
                row.style.opacity = '1';
                row.style.transform = 'none';
                row.style.maxHeight = '';
            }
        });
    };

    function markAllNotificationsRead() {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const unreadRows = document.querySelectorAll('#notifDrawerBody .notif-drawer-item.is-unread');
        unreadRows.forEach((row, idx) => {
            setTimeout(() => {
                row.style.transition = 'all 0.35s cubic-bezier(0.4, 0, 0.2, 1)';
                row.style.opacity = '0';
                row.style.transform = 'translateX(40px)';
                row.style.maxHeight = '0px';
                row.style.paddingTop = '0';
                row.style.paddingBottom = '0';
                row.style.overflow = 'hidden';
                setTimeout(() => row.remove(), 350);
            }, idx * 40);
        });

        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
        }).then(res => res.json()).then(data => {
            updateUnreadBadge(0);
            const subtitle = document.getElementById('notifDrawerSubtitle');
            if (subtitle) subtitle.textContent = 'لا توجد إشعارات جديدة';
            setTimeout(() => {
                const remaining = document.querySelectorAll('#notifDrawerBody .notif-drawer-item');
                if (remaining.length === 0) {
                    const body = document.getElementById('notifDrawerBody');
                    if (body) body.innerHTML = `<div class="text-center py-5 text-muted"><i class="fas fa-bell-slash fa-3x mb-3 opacity-25 d-block"></i><p class="small">لا توجد إشعارات جديدة</p></div>`;
                }
            }, 350);
        }).catch(() => {});
    }

    // ==========================================
    // Messages Drawer Functions
    // ==========================================
    function openMessagesDrawer() {
        const drawer = document.getElementById('messagesDrawer');
        const panel = document.getElementById('msgDrawerPanel');
        const backdrop = document.getElementById('msgDrawerBackdrop');
        if (!drawer || !panel || !backdrop) return;

        drawer.style.pointerEvents = 'all';
        backdrop.style.pointerEvents = 'all';
        backdrop.style.opacity = '1';
        panel.style.transform = 'translateX(0)';
        document.body.style.overflow = 'hidden';

        loadMessagesInDrawer();
    }

    function closeMessagesDrawer() {
        const drawer = document.getElementById('messagesDrawer');
        const panel = document.getElementById('msgDrawerPanel');
        const backdrop = document.getElementById('msgDrawerBackdrop');
        if (!drawer || !panel || !backdrop) return;

        panel.style.transform = 'translateX(100%)';
        backdrop.style.opacity = '0';
        backdrop.style.pointerEvents = 'none';
        setTimeout(() => {
            drawer.style.pointerEvents = 'none';
        }, 350);
        document.body.style.overflow = '';
    }

    function loadMessagesInDrawer() {
        const body = document.getElementById('msgDrawerBody');
        const subtitle = document.getElementById('msgDrawerSubtitle');
        if (!body) return;

        body.innerHTML = `<div class="text-center py-5 text-muted"><div class="spinner-border spinner-border-sm text-success mb-2" role="status"></div><p class="small">جاري تحميل الرسائل...</p></div>`;

        fetch('/messages/api/recent', { headers: { 'Accept': 'application/json' } })
            .then(res => res.ok ? res.json() : Promise.reject(res))
            .then(data => {
                // API returns: { status, unread_total, conversations: [{user_id, name, last_message, last_message_time, unread_count, initial, avatar}] }
                const conversations = data.conversations || [];
                const unreadTotal = data.unread_total ?? 0;
                if (subtitle) subtitle.textContent = unreadTotal > 0 ? `${unreadTotal} رسالة غير مقروءة` : 'لا توجد رسائل جديدة';

                if (conversations.length === 0) {
                    body.innerHTML = `<div class="text-center py-5 text-muted"><i class="fas fa-inbox fa-3x mb-3 opacity-25 d-block"></i><p class="small">لا توجد رسائل حتى الآن</p></div>`;
                    return;
                }

                let html = '';
                conversations.forEach(conv => {
                    const hasUnread = conv.unread_count > 0;
                    const initial = conv.initial || (conv.name || 'م').charAt(0);
                    const avatarHtml = conv.avatar
                        ? `<img src="${conv.avatar}" alt="${conv.name}" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;">`
                        : `<div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white" style="width:40px;height:40px;background:linear-gradient(135deg,#059669,#047857);font-size:1rem;">${initial}</div>`;
                    html += `
                    <a href="/messages/${conv.user_id}" class="d-flex align-items-start p-3 text-decoration-none border-bottom ${hasUnread ? 'bg-success bg-opacity-5' : ''}" style="color:inherit;transition:background 0.15s;">
                        <div class="me-3 mt-1 flex-shrink-0">${avatarHtml}</div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="mb-0 fw-bold text-truncate" style="max-width:190px;font-size:0.88rem;">${conv.name || 'مجهول'}</h6>
                                <small class="text-muted text-nowrap ms-2" style="font-size:0.7rem;">${conv.last_message_time || ''}</small>
                            </div>
                            <p class="mb-0 text-muted text-truncate small" style="max-width:240px;">${conv.last_message || '...'}</p>
                        </div>
                        ${hasUnread ? `<span class="badge bg-success rounded-pill ms-2 mt-2" style="font-size:0.65rem;">${conv.unread_count}</span>` : ''}
                    </a>`;
                });
                body.innerHTML = html;
            })
            .catch(() => {
                // الرسائل API غير متاحة، إعادة التوجيه لصفحة الرسائل
                body.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-envelope fa-3x mb-3 opacity-25 d-block"></i>
                    <p class="small mb-3">لعرض الرسائل، انقر على الزر أدناه</p>
                    <a href="/messages" class="btn btn-sm btn-outline-success rounded-pill px-4">
                        <i class="fas fa-inbox me-1"></i>فتح صندوق الرسائل
                    </a>
                </div>`;
                if (subtitle) subtitle.textContent = 'صندوق الرسائل';
            });
    }

    // إغلاق الدرجين عند الضغط على ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeNotificationsDrawer();
            closeMessagesDrawer();
        }
    });

    // ربط الدوال بالكائن العام window لضمان إمكانية استدعائها من أي مكان
    window.openNotificationsDrawer = openNotificationsDrawer;
    window.closeNotificationsDrawer = closeNotificationsDrawer;
    window.openMessagesDrawer = openMessagesDrawer;
    window.closeMessagesDrawer = closeMessagesDrawer;
    window.loadNotificationsInDrawer = loadNotificationsInDrawer;
    window.loadMessagesInDrawer = loadMessagesInDrawer;
