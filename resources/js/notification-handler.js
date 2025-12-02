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

                    if (data.notifications.length === 0) {
                        list.innerHTML = '<li class="dropdown-item text-center text-muted py-3">لا توجد إشعارات جديدة</li>';
                    } else {
                        data.notifications.forEach(notification => {
                            const bgClass = notification.is_read ? '' : 'bg-light';
                            const item = `
                                <li>
                                    <a class="dropdown-item d-flex align-items-start py-2 border-bottom ${bgClass}" 
                                       href="/notifications/${notification.id}" 
                                       style="cursor: pointer;">
                                        <div class="me-3 mt-1">
                                            <div class="icon-circle bg-${notification.type || 'primary'} rounded-circle d-flex align-items-center justify-content->
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
                    }

                    // Update badge
                    const badge = document.getElementById('notification-badge');
                    if (data.unread_count > 0) {
                        if (badge) {
                            badge.innerText = data.unread_count;
                        } else {
                            const badgeSpan = document.createElement('span');
                            badgeSpan.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                            badgeSpan.id = 'notification-badge';
                            badgeSpan.innerText = data.unread_count;
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
