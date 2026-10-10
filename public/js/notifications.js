/**
 * Notification Center Handler
 * Handles real-time polling, dropdown rendering, and instant dismissal on mark-as-read.
 */
document.addEventListener('DOMContentLoaded', function () {
    const dropdownToggle = document.getElementById('notificationsDropdown');
    const notifList = document.getElementById('notifications-list');
    const badge = document.getElementById('notification-badge');

    if (!notifList) return;

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    function updateBadge(unreadCount) {
        if (!badge) return;
        const count = parseInt(unreadCount, 10) || 0;
        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'inline-block';
        } else {
            badge.textContent = '0';
            badge.style.display = 'none';
        }
    }

    function fetchNotifications() {
        fetch('/notifications/api/notifications', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            const notifications = data.notifications || [];
            updateBadge(data.unread_count !== undefined ? data.unread_count : 0);

            if (notifications.length === 0) {
                notifList.innerHTML = `
                    <li class="text-center py-4 text-muted small">
                        <i class="fas fa-bell-slash fa-2x mb-2 text-secondary opacity-50 d-block"></i>
                        لا توجد إشعارات جديدة حالياً
                    </li>`;
                return;
            }

            let html = '';
            notifications.forEach(notif => {
                const isUnread = !notif.is_read;
                const bgClass = isUnread ? 'bg-light border-start border-3 border-primary' : '';
                const title = notif.title || 'إشعار جديد';
                const message = notif.message || '';
                const time = notif.time_ago || '';
                const id = notif.id;

                html += `
                    <li class="notification-dropdown-item ${bgClass} position-relative border-bottom p-2" id="coord-notif-${id}" style="transition: all 0.35s ease;">
                        <div class="d-flex align-items-start justify-content-between gap-2">
                            <a href="/notifications/${id}" class="text-decoration-none text-dark flex-grow-1" style="min-width:0;">
                                <div class="fw-bold small text-truncate ${isUnread ? 'text-primary' : ''}">${title}</div>
                                <div class="text-muted text-truncate" style="font-size:0.75rem;">${message}</div>
                                <div class="text-muted mt-1" style="font-size:0.7rem;"><i class="far fa-clock me-1"></i>${time}</div>
                            </a>
                            ${isUnread ? `
                                <button type="button" class="btn btn-sm btn-link text-success p-1 mark-read-coord-btn" data-id="${id}" title="تحديد كمقروء وإخفاء" style="font-size:0.85rem;">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                            ` : ''}
                        </div>
                    </li>
                `;
            });

            notifList.innerHTML = html;
            attachCoordDismissHandlers();
        })
        .catch(err => {
            console.error('Error fetching notifications:', err);
        });
    }

    function dismissCoordItem(item, unreadCount) {
        if (!item) return;
        item.style.transition = 'all 0.35s cubic-bezier(0.4, 0, 0.2, 1)';
        item.style.opacity = '0';
        item.style.transform = 'translateX(25px) scale(0.96)';
        item.style.maxHeight = item.offsetHeight + 'px';

        setTimeout(() => {
            item.style.maxHeight = '0px';
            item.style.paddingTop = '0px';
            item.style.paddingBottom = '0px';
            item.style.marginTop = '0px';
            item.style.marginBottom = '0px';
            item.style.overflow = 'hidden';
            item.style.border = 'none';

            setTimeout(() => {
                item.remove();
                if (unreadCount !== undefined) {
                    updateBadge(unreadCount);
                }
                const remaining = notifList.querySelectorAll('.notification-dropdown-item');
                if (remaining.length === 0) {
                    notifList.innerHTML = `
                        <li class="text-center py-4 text-muted small">
                            <i class="fas fa-check-double fa-2x mb-2 text-success opacity-50 d-block"></i>
                            تمت قراءة جميع الإشعارات بنجاح
                        </li>`;
                }
            }, 300);
        }, 150);
    }

    function attachCoordDismissHandlers() {
        notifList.querySelectorAll('.mark-read-coord-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                const id = this.getAttribute('data-id');
                const item = document.getElementById(`coord-notif-${id}`);
                this.disabled = true;

                fetch(`/notifications/${id}/read`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    dismissCoordItem(item, data.unread_count);
                })
                .catch(err => {
                    console.error('Failed to mark notification as read:', err);
                    if (item) item.style.opacity = '1';
                    btn.disabled = false;
                });
            });
        });
    }

    if (dropdownToggle) {
        dropdownToggle.addEventListener('show.bs.dropdown', fetchNotifications);
    }

    // Initial check
    fetchNotifications();
});
