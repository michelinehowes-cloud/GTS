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
                            const li = document.createElement('li');

                            const a = document.createElement('a');
                            a.className = 'dropdown-item d-flex align-items-start py-2 border-bottom bg-light notification-item';
                            const safeId = encodeURIComponent(notification.id || '');
                            a.href = `/notifications/${safeId}`;
                            a.dataset.notificationId = String(notification.id || '');
                            a.style.cursor = 'pointer';

                            const iconWrapper = document.createElement('div');
                            iconWrapper.className = 'me-3 mt-1';

                            const allowedTypes = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];
                            const safeType = allowedTypes.includes(notification.type) ? notification.type : 'primary';
                            const circle = document.createElement('div');
                            circle.className = `icon-circle bg-${safeType} rounded-circle d-flex align-items-center justify-content-center`;
                            circle.style.width = '35px';
                            circle.style.height = '35px';

                            const safeIconClass = /^[a-zA-Z0-9_\-]+$/.test(notification.icon) ? notification.icon : 'bell';
                            const icon = document.createElement('i');
                            icon.className = `fas fa-${safeIconClass} text-white small`;
                            circle.appendChild(icon);
                            iconWrapper.appendChild(circle);

                            const contentDiv = document.createElement('div');
                            contentDiv.className = 'flex-grow-1';

                            const timeDiv = document.createElement('div');
                            timeDiv.className = 'small text-muted float-end';
                            timeDiv.style.fontSize = '0.7rem';
                            timeDiv.textContent = notification.created_at ? new Date(notification.created_at).toLocaleDateString('ar-EG') : '';

                            const titleSpan = document.createElement('span');
                            titleSpan.className = 'fw-bold d-block text-dark';
                            titleSpan.style.fontSize = '0.9rem';
                            titleSpan.textContent = notification.title || '';

                            const messageDiv = document.createElement('div');
                            messageDiv.className = 'small text-muted text-truncate';
                            messageDiv.style.maxWidth = '200px';
                            messageDiv.textContent = notification.message || '';

                            contentDiv.appendChild(timeDiv);
                            contentDiv.appendChild(titleSpan);
                            contentDiv.appendChild(messageDiv);

                            a.appendChild(iconWrapper);
                            a.appendChild(contentDiv);
                            li.appendChild(a);

                            list.appendChild(li);
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

    // وظيفة مسح كافة الإشعارات
    const clearAllBtn = document.getElementById('clearAllNotifications');
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (!confirm('هل أنت متأكد من مسح جميع الإشعارات؟')) {
                return;
            }

            // تعطيل الزر أثناء المعالجة
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري المسح...';

            // إرسال طلب مسح جميع الإشعارات
            fetch('/notifications/read/delete', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to delete notifications');
                    }
                    return response.json();
                })
                .then(data => {
                    // مسح القائمة
                    const list = document.getElementById('notifications-list');
                    list.innerHTML = '<li class="dropdown-item text-center text-muted py-3">لا توجد إشعارات جديدة</li>';

                    // إزالة الشارة
                    const badge = document.getElementById('notification-badge');
                    if (badge) {
                        badge.remove();
                    }

                    // إعادة تفعيل الزر
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-trash-alt"></i> مسح الكل';

                    // إظهار رسالة نجاح
                    alert('تم مسح جميع الإشعارات بنجاح');
                })
                .catch(error => {
                    console.error('Error deleting notifications:', error);

                    // إعادة تفعيل الزر
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-trash-alt"></i> مسح الكل';

                    alert('حدث خطأ أثناء مسح الإشعارات. يرجى المحاولة مرة أخرى.');
                });
        });
    }
});
