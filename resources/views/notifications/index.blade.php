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
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="d-flex flex-column gap-2 mb-4">
                @forelse ($notifications as $notification)
                    <div class="card border-0 rounded-4 shadow-sm notification-item {{ $notification->is_read ? 'bg-white' : 'border-start border-4 border-primary' }}"
                         style="{{ $notification->is_read ? '' : 'background-color: #f8faff;' }}"
                         data-notification-id="{{ $notification->id }}">
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
                                            {{ $notification->title }}
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
                                                style="width: 32px; height: 32px; padding: 0;" title="تحديد كمقروء">
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
                    <div class="card border-0 rounded-4 shadow-sm p-5 text-center bg-white">
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
                <div class="d-flex justify-content-center">
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

            // Mark single notification as read
            document.querySelectorAll('.mark-as-read-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const notificationItem = this.closest('.notification-item');
                    const notificationId = notificationItem.dataset.notificationId;

                    fetch(`/notifications/${notificationId}/read`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                notificationItem.classList.remove('unread');
                                notificationItem.classList.add('read');
                                this.remove(); // Remove the button after marking as read
                            } else {
                                alert('Failed to mark notification as read.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while marking notification as read.');
                        });
                });
            });

            // Delete single notification
            document.querySelectorAll('.delete-notification-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const notificationItem = this.closest('.notification-item');
                    const notificationId = notificationItem.dataset.notificationId;

                    if (confirm('هل أنت متأكد أنك تريد حذف هذا الإشعار؟')) {
                        fetch(`/notifications/${notificationId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    notificationItem.remove();
                                } else {
                                    alert('فشل حذف الإشعار.');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('حدث خطأ أثناء حذف الإشعار.');
                            });
                    }
                });
            });

            // Mark all as read
            if (markAllAsReadBtn) {
                markAllAsReadBtn.addEventListener('click', function () {
                    if (confirm('هل أنت متأكد أنك تريد وضع جميع الإشعارات غير المقروءة كمقروءة؟')) {
                        fetch('/notifications/mark-all-read', {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    document.querySelectorAll('.notification-item.unread').forEach(item => {
                                        item.classList.remove('unread');
                                        item.classList.add('read');
                                        const markBtn = item.querySelector('.mark-as-read-btn');
                                        if (markBtn) markBtn.remove();
                                    });
                                    alert('تم وضع جميع الإشعارات كمقروءة.');
                                } else {
                                    alert('فشل وضع جميع الإشعارات كمقروءة.');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('حدث خطأ أثناء وضع جميع الإشعارات كمقروءة.');
                            });
                    }
                });
            }

            // Delete all read notifications
            if (deleteAllReadBtn) {
                deleteAllReadBtn.addEventListener('click', function () {
                    if (confirm('هل أنت متأكد أنك تريد حذف جميع الإشعارات المقروءة؟')) {
                        fetch('/notifications/read/delete', {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    document.querySelectorAll('.notification-item.read').forEach(item => {
                                        item.remove();
                                    });
                                    alert('تم حذف جميع الإشعارات المقروءة.');
                                } else {
                                    alert('فشل حذف الإشعارات المقروءة.');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('حدث خطأ أثناء حذف الإشعارات المقروءة.');
                            });
                    }
                });
            }

            // Filters
            function applyFilters() {
                const type = filterByType.value;
                const readStatus = filterByReadStatus.value;
                let url = '{{ route('notifications.index') }}';
                const params = new URLSearchParams();

                if (type) {
                    params.append('type', type);
                }
                if (readStatus) {
                    params.append('read', readStatus);
                }

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