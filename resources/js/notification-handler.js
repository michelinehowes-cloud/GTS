// Notifications Dropdown Handler
document.addEventListener('DOMContentLoaded', function () {
    const dropdownElement = document.getElementById('notificationsDropdown');
    if (dropdownElement) {
        dropdownElement.addEventListener('show.bs.dropdown', function () {
            console.log('Dropdown showing, fetching notifications...');

            // Get the notifications API route from data attribute
            const apiRoute = dropdownElement.dataset.apiRoute || '/notifications/api/notifications';

            fetch(apiRoute)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Notifications data:', data);
                    const list = document.getElementById('notifications-list');
                    list.innerHTML = '';

                    // تصفية الإشعارات لإظهار غير المقروءة فقط
                    const unreadNotifications = data.notifications.filter(n => !n.is_read);

                    if (unreadNotifications.length === 0) {
                        list.innerHTML = '<li class="dropdown-item text-center text-muted py-3">لا توجد إشعارات جديدة</li>';
                    } else {
                        unreadNotifications.forEach(notification => {
                            const item = `
                                <li>
                                    <a class="dropdown-item d-flex align-items-start py-2 border-bottom bg-light notification-item" 
                                       href="/notifications/${notification.id}" 
                                       data-notification-id="${notification.id}"
                                       style="cursor: pointer;">
                                        <div class="me-3 mt-1">
                                            <div class="icon-circle bg-${notification.type || 'primary'} rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                <i class="fas fa-${notification.icon || 'bell'} text-white small"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="small text-muted float-end" style="font-size: 0.7rem;">${new Date(notification.created_at).toLocaleDateString('ar-EG')}</div>
                                            <span class="fw-bold d-block text-dark" style="font-size: 0.9rem;">${notification.title}</span>
                                            <div class="small text-muted text-truncate" style="max-width: 200px;">${notification.message}</div>
                                        </div>
                                    </a>
                                </li>
                            `;
                            list.innerHTML += item;
                        });

                        // إضافة مستمع للنقر على الإشعارات لتحديدها كمقروءة
                        document.querySelectorAll('.notification-item').forEach(item => {
                            item.addEventListener('click', function (e) {
                                e.preventDefault();
                                const notificationId = this.dataset.notificationId;
                                const href = this.getAttribute('href');

                                // تحديد الإشعار كمقروء
                                fetch(`/notifications/${notificationId}/mark-as-read`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    }
                                })
                                    .then(() => {
                                        // إخفاء الإشعار من القائمة
                                        this.closest('li').style.opacity = '0';
                                        setTimeout(() => {
                                            this.closest('li').remove();

                                            // تحديث العداد
                                            const badge = document.getElementById('notification-badge');
                                            if (badge) {
                                                const currentCount = parseInt(badge.innerText);
                                                if (currentCount > 1) {
                                                    badge.innerText = currentCount - 1;
                                                } else {
                                                    badge.remove();
                                                }
                                            }

                                            // إذا لم يتبق إشعارات، أظهر رسالة
                                            const remainingNotifications = document.querySelectorAll('.notification-item');
                                            if (remainingNotifications.length === 0) {
                                                list.innerHTML = '<li class="dropdown-item text-center text-muted py-3">لا توجد إشعارات جديدة</li>';
                                            }
                                        }, 300);

                                        // الانتقال إلى الصفحة
                                        window.location.href = href;
                                    })
                                    .catch(error => {
                                        console.error('Error marking notification as read:', error);
                                        // الانتقال إلى الصفحة حتى لو فشل التحديد
                                        window.location.href = href;
                                    });
                            });
                        });
                    }

                    // Update badge - عرض عدد الإشعارات غير المقروءة فقط
                    const badge = document.getElementById('notification-badge');
                    if (unreadNotifications.length > 0) {
                        if (badge) {
                            badge.innerText = unreadNotifications.length;
                        } else {
                            const badgeSpan = document.createElement('span');
                            badgeSpan.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                            badgeSpan.id = 'notification-badge';
                            badgeSpan.innerText = unreadNotifications.length;
                            dropdownElement.appendChild(badgeSpan);
                        }
                    } else if (badge) {
                        badge.remove();
                    }
                })
                .catch(error => {
                    console.error('Error fetching notifications:', error);
                    document.getElementById('notifications-list').innerHTML = '<li class="dropdown-item text-center text-danger py-3">حدث خطأ في تحميل الإشعارات</li>';
                });
        });
    }
});
