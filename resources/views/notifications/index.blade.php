@extends('layouts.app')

@section('title', 'مركز الإشعارات')

@section('content')
<div class="container-fluid px-2 px-md-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <!-- Header & Action Bar -->
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
                <div>
                    <h2 class="fw-bold text-primary mb-1 fs-4 fs-md-3">
                        <i class="fas fa-bell me-2 text-warning"></i>مركز الإشعارات
                    </h2>
                    <p class="text-muted small mb-0">تابع آخر التحديثات والأنشطة المتعلقة بحسابك</p>
                </div>
                <div class="d-flex gap-2 w-100 w-sm-auto">
                    <button class="btn btn-outline-primary btn-sm rounded-pill px-3 flex-fill flex-sm-grow-0" id="markAllAsReadBtn">
                        <i class="fas fa-check-double me-1"></i> تحديد الكل كمقروء
                    </button>
                    <button class="btn btn-outline-danger btn-sm rounded-pill px-3 flex-fill flex-sm-grow-0" id="deleteAllReadBtn">
                        <i class="fas fa-trash me-1"></i> حذف المقروء
                    </button>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="card border-0 rounded-4 shadow-sm mb-3">
                <div class="card-body p-2 p-md-3">
                    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                        <div class="d-flex gap-1 flex-wrap">
                            <a href="{{ route('notifications.index') }}" 
                               class="btn btn-sm rounded-pill px-3 {{ !request('read') ? 'btn-primary' : 'btn-light text-muted' }}">
                                الكل
                            </a>
                            <a href="{{ route('notifications.index', ['read' => 'unread']) }}" 
                               class="btn btn-sm rounded-pill px-3 {{ request('read') == 'unread' ? 'btn-primary' : 'btn-light text-muted' }}">
                                غير مقروءة
                            </a>
                            <a href="{{ route('notifications.index', ['read' => 'read']) }}" 
                               class="btn btn-sm rounded-pill px-3 {{ request('read') == 'read' ? 'btn-primary' : 'btn-light text-muted' }}">
                                المقروءة
                            </a>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0 w-100 w-sm-auto">
                            <select class="form-select form-select-sm rounded-pill" id="filterByType" style="max-width: 160px;">
                                <option value="">كل الأنواع</option>
                                <option value="info" {{ request('type') == 'info' ? 'selected' : '' }}>معلومات</option>
                                <option value="success" {{ request('type') == 'success' ? 'selected' : '' }}>نجاح</option>
                                <option value="warning" {{ request('type') == 'warning' ? 'selected' : '' }}>تحذير</option>
                                <option value="danger" {{ request('type') == 'danger' ? 'selected' : '' }}>تنبيه مهم</option>
              <!-- Notifications List -->
            <div class="d-flex flex-column gap-2 mb-4" id="notificationsListContainer">
                @forelse ($notifications as $notification)
                    <div class="card border-0 rounded-4 shadow-sm notification-item {{ $notification->is_read ? 'is-read bg-white' : 'is-unread border-start border-4 border-primary' }}"
                         style="{{ $notification->is_read ? '' : 'background-color: #f8faff;' }}; transition: all 0.3s ease;"
                         data-notification-id="{{ $notification->id }}"
                         data-is-read="{{ $notification->is_read ? '1' : '0' }}">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-start gap-3">
                                <!-- Icon Circle -->
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1" 
                                     style="width: 42px; height: 42px; background: {{ $notification->is_read ? '#f1f5f9' : '#e0e7ff' }}; color: {{ $notification->is_read ? '#64748b' : '#3b82f6' }};">
                                    <i class="fas fa-{{ $notification->icon ?? 'bell' }} fa-lg"></i>
                                </div>

                                <!-- Content -->
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-1 mb-1">
                                        <h6 class="fw-bold text-dark mb-0 fs-6 {{ $notification->is_read ? 'opacity-85' : '' }}">
                                            <a href="{{ route('notifications.show', $notification->id) }}" class="text-decoration-none text-dark">
                                                {{ $notification->title }}
                                            </a>
                                            @if(!$notification->is_read)
                                                <span class="badge bg-primary rounded-pill ms-1" style="font-size: 0.65rem;">جديد</span>
                                            @endif
                                        </h6>
                                        <span class="text-muted small" style="font-size: 0.72rem;">
                                            <i class="far fa-clock me-1"></i>{{ $notification->created_at->locale('ar')->diffForHumans() }}
                                        </span>
                                    </div>
                                    <p class="text-muted small mb-2 text-break" style="line-height: 1.4;">
                                        {{ $notification->message }}
                                    </p>
                                    @if($notification->sender)
                                        <div class="small text-secondary" style="font-size: 0.72rem;">
                                            <i class="fas fa-user-circle me-1"></i>من: {{ $notification->sender->name }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="d-flex flex-column gap-1 flex-shrink-0">
                                    @if(!$notification->is_read)
                                        <button class="btn btn-sm btn-light text-success border mark-as-read-btn rounded-circle" 
                                                style="width: 32px; height: 32px; padding: 0;" title="تحديد كمقروء وإخفاء">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    <button class="btn btn-sm btn-light text-danger border delete-notification-btn rounded-circle" 
                                            style="width: 32px; height: 32px; padding: 0;" title="حذف الإشعار">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card border-0 rounded-4 shadow-sm p-5 text-center bg-white" id="emptyNotificationsState">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px;">
                            <i class="fas fa-bell-slash fa-2x text-muted opacity-50"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">لا توجد إشعارات حالياً</h5>
                        <p class="text-muted small mb-0">جميع الإشعارات والتنبيهات ستظهر لك هنا فور وصولها</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="d-flex justify-content-center" id="notificationsPagination">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const markAllAsReadBtn = document.getElementById('markAllAsReadBtn');
            const deleteAllReadBtn = document.getElementById('deleteAllReadBtn');
            const filterByType = document.getElementById('filterByType');
            const filterByReadStatus = document.getElementById('filterByReadStatus');
            const listContainer = document.getElementById('notificationsListContainer');

            function getCsrfToken() {
                return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            }

            // دالة التحقق من القائمة الفارغة وعرض رسالة فارغة ناعمة
            function checkEmptyState() {
                if (!listContainer) return;
                const items = listContainer.querySelectorAll('.notification-item');
                if (items.length === 0) {
                    let emptyState = document.getElementById('emptyNotificationsState');
                    if (!emptyState) {
                        emptyState = document.createElement('div');
                        emptyState.id = 'emptyNotificationsState';
                        emptyState.className = 'card border-0 rounded-4 shadow-sm p-5 text-center bg-white';
                        emptyState.innerHTML = `
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px;">
                                <i class="fas fa-bell-slash fa-2x text-muted opacity-50"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">لا توجد إشعارات حالياً</h5>
                            <p class="text-muted small mb-0">جميع الإشعارات والتنبيهات ستظهر لك هنا فور وصولها</p>
                        `;
                        listContainer.appendChild(emptyState);
                    }
                    emptyState.style.display = 'block';

                    const pagination = document.getElementById('notificationsPagination');
                    if (pagination) pagination.style.display = 'none';
                }
            }

            // دالة إخفاء وحذف عنصر الإشعار بحركة انسيابية سريعة
            function dismissNotificationCard(item, onFinish) {
                if (!item) return;
                item.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                item.style.opacity = '0';
                item.style.transform = 'translateX(30px)';
                item.style.maxHeight = item.offsetHeight + 'px';
                item.style.overflow = 'hidden';

                setTimeout(() => {
                    item.style.maxHeight = '0';
                    item.style.paddingTop = '0';
                    item.style.paddingBottom = '0';
                    item.style.marginTop = '0';
                    item.style.marginBottom = '0';
                    item.style.border = 'none';
                    setTimeout(() => {
                        item.remove();
                        if (typeof onFinish === 'function') onFinish();
                        checkEmptyState();
                    }, 260);
                }, 80);
            }

            // تحديث شارات العداد العالمية في الهيدر والدرج
            function updateGlobalBadges(count) {
                const badge = document.getElementById('notification-badge');
                if (count > 0) {
                    if (badge) {
                        badge.innerText = count;
                        badge.style.display = 'inline-block';
                    }
                } else {
                    if (badge) badge.remove();
                }
                const notifHeaderCount = document.getElementById('notification-header-count');
                if (notifHeaderCount) {
                    notifHeaderCount.innerText = count > 0 ? (count + ' غير مقروء') : '0 غير مقروء';
                    notifHeaderCount.style.display = count > 0 ? 'inline-block' : 'none';
                }
                const notifDrawerSubtitle = document.getElementById('notifDrawerSubtitle');
                if (notifDrawerSubtitle) {
                    notifDrawerSubtitle.innerText = count > 0 ? (count + ' غير مقروء') : 'لا توجد إشعارات جديدة';
                }
            }

            // 1. تحديد إشعار فردي كمقروء (يختفي مباشرة)
            document.querySelectorAll('.mark-as-read-btn').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const notificationItem = this.closest('.notification-item');
                    if (!notificationItem) return;
                    const notificationId = notificationItem.dataset.notificationId;

                    // تعطيل الزر فوراً لمنع التكرار
                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                    fetch(`/notifications/${notificationId}/read`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // إخفاء الإشعار فوراً من الصفحة
                            dismissNotificationCard(notificationItem);
                            if (typeof data.unread_count !== 'undefined') {
                                updateGlobalBadges(data.unread_count);
                            }
                        } else {
                            this.disabled = false;
                            this.innerHTML = '<i class="fas fa-check"></i>';
                            alert('تعذر تحديث حالة الإشعار، يرجى المحاولة لاحقاً.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.disabled = false;
                        this.innerHTML = '<i class="fas fa-check"></i>';
                        alert('حدث خطأ أثناء تحديث الإشعار.');
                    });
                });
            });

            // 2. حذف إشعار فردي
            document.querySelectorAll('.delete-notification-btn').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const notificationItem = this.closest('.notification-item');
                    if (!notificationItem) return;
                    const notificationId = notificationItem.dataset.notificationId;

                    if (confirm('هل أنت متأكد من رغبتك في حذف هذا الإشعار؟')) {
                        this.disabled = true;
                        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                        fetch(`/notifications/${notificationId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': getCsrfToken(),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                dismissNotificationCard(notificationItem);
                                if (typeof data.unread_count !== 'undefined') {
                                    updateGlobalBadges(data.unread_count);
                                }
                            } else {
                                this.disabled = false;
                                this.innerHTML = '<i class="fas fa-trash-alt"></i>';
                                alert('فشل حذف الإشعار.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            this.disabled = false;
                            this.innerHTML = '<i class="fas fa-trash-alt"></i>';
                            alert('حدث خطأ أثناء حذف الإشعار.');
                        });
                    }
                });
            });

            // 3. تحديد الكل كمقروء (تختفي كافة الإشعارات غير المقروءة مباشرة)
            if (markAllAsReadBtn) {
                markAllAsReadBtn.addEventListener('click', function () {
                    const unreadItems = listContainer ? listContainer.querySelectorAll('.notification-item.is-unread, .notification-item[data-is-read="0"]') : [];
                    if (unreadItems.length === 0) {
                        alert('لا توجد إشعارات غير مقروءة حالياً.');
                        return;
                    }

                    if (confirm('هل أنت متأكد من تحديد جميع الإشعارات كمقروءة وإخفائها؟')) {
                        const originalBtnHtml = markAllAsReadBtn.innerHTML;
                        markAllAsReadBtn.disabled = true;
                        markAllAsReadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> جاري التحديد...';

                        fetch('/notifications/mark-all-read', {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': getCsrfToken(),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            markAllAsReadBtn.disabled = false;
                            markAllAsReadBtn.innerHTML = originalBtnHtml;

                            if (data.success) {
                                // إخفاء متتالي وسريع لجميع الإشعارات غير المقروءة
                                unreadItems.forEach((item, index) => {
                                    setTimeout(() => {
                                        dismissNotificationCard(item);
                                    }, index * 40);
                                });
                                updateGlobalBadges(0);
                            } else {
                                alert('فشل تحديث الإشعارات كمقروءة.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            markAllAsReadBtn.disabled = false;
                            markAllAsReadBtn.innerHTML = originalBtnHtml;
                            alert('حدث خطأ أثناء محاولة وضع جميع الإشعارات كمقروءة.');
                        });
                    }
                });
            }

            // 4. حذف جميع الإشعارات المقروءة
            if (deleteAllReadBtn) {
                deleteAllReadBtn.addEventListener('click', function () {
                    const readItems = listContainer ? listContainer.querySelectorAll('.notification-item.is-read, .notification-item[data-is-read="1"]') : [];
                    if (readItems.length === 0) {
                        alert('لا توجد إشعارات مقروءة لحذفها.');
                        return;
                    }

                    if (confirm('هل أنت متأكد أنك تريد حذف جميع الإشعارات المقروءة نهائياً؟')) {
                        const originalBtnHtml = deleteAllReadBtn.innerHTML;
                        deleteAllReadBtn.disabled = true;
                        deleteAllReadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> جاري الحذف...';

                        fetch('/notifications/read/delete', {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': getCsrfToken(),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            deleteAllReadBtn.disabled = false;
                            deleteAllReadBtn.innerHTML = originalBtnHtml;

                            if (data.success) {
                                readItems.forEach((item, index) => {
                                    setTimeout(() => {
                                        dismissNotificationCard(item);
                                    }, index * 40);
                                });
                            } else {
                                alert('فشل حذف الإشعارات المقروءة.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            deleteAllReadBtn.disabled = false;
                            deleteAllReadBtn.innerHTML = originalBtnHtml;
                            alert('حدث خطأ أثناء حذف الإشعارات المقروءة.');
                        });
                    }
                });
            }

            // 5. الفلاتر
            function applyFilters() {
                const type = filterByType ? filterByType.value : '';
                const readStatus = filterByReadStatus ? filterByReadStatus.value : '';
                let url = '{{ route('notifications.index') }}';
                const params = new URLSearchParams();

                if (type) params.append('type', type);
                if (readStatus) params.append('read', readStatus);

                if (params.toString()) {
                    url += '?' + params.toString();
                }
                window.location.href = url;
            }

            if (filterByType) {
                filterByType.addEventListener('change', applyFilters);
            }

            if (filterByReadStatus) {
                filterByReadStatus.addEventListener('change', applyFilters);
            }
        });
    </script>
@endpush