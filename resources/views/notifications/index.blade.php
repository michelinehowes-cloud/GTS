@extends('layouts.app')

@section('title', 'الإشعارات')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <h1 class="mb-4">إشعاراتي</h1>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="row g-2 align-items-center justify-content-between">
                            <!-- Buttons Section -->
                            <div class="col-12 col-md-auto order-2 order-md-1">
                                <div class="d-flex gap-2 w-100">
                                    <button class="btn btn-outline-primary btn-sm flex-fill flex-md-grow-0"
                                        id="markAllAsReadBtn">
                                        <i class="fas fa-check-double me-1"></i> الكل مقروء
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm flex-fill flex-md-grow-0"
                                        id="deleteAllReadBtn">
                                        <i class="fas fa-trash me-1"></i> حذف المقروء
                                    </button>
                                </div>
                            </div>

                            <!-- Filters Section -->
                            <div class="col-12 col-md-auto order-1 order-md-2 mb-2 mb-md-0">
                                <div class="d-flex gap-2 w-100">
                                    <select class="form-select form-select-sm flex-fill" id="filterByType">
                                        <option value="">كل الأنواع</option>
                                        <option value="info" {{ request('type') == 'info' ? 'selected' : '' }}>معلومات
                                        </option>
                                        <option value="success" {{ request('type') == 'success' ? 'selected' : '' }}>نجاح
                                        </option>
                                        <option value="warning" {{ request('type') == 'warning' ? 'selected' : '' }}>تحذير
                                        </option>
                                        <option value="danger" {{ request('type') == 'danger' ? 'selected' : '' }}>خطر
                                        </option>
                                    </select>
                                    <select class="form-select form-select-sm flex-fill" id="filterByReadStatus">
                                        <option value="">الكل</option>
                                        <option value="unread" {{ request('read') == 'unread' ? 'selected' : '' }}>غير مقروءة
                                        </option>
                                        <option value="read" {{ request('read') == 'read' ? 'selected' : '' }}>مقروءة</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <ul class="list-group list-group-flush">
                        @forelse ($notifications as $notification)
                            <li class="list-group-item notification-item {{ $notification->is_read ? 'read' : 'unread' }}"
                                data-notification-id="{{ $notification->id }}">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-{{ $notification->icon }} me-3 fs-4 text-{{ $notification->color }}"></i>
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1">{{ $notification->title }}</h5>
                                        <p class="mb-1 text-muted">{{ $notification->message }}</p>
                                        <small class="text-secondary">
                                            {{ $notification->created_at->diffForHumans() }}
                                            @if($notification->sender)
                                                من {{ $notification->sender->name }}
                                            @endif
                                        </small>
                                    </div>
                                    <div class="notification-actions">
                                        @if(!$notification->is_read)
                                            <button class="btn btn-sm btn-outline-success mark-as-read-btn" title="وضع كمقروء">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        <button class="btn btn-sm btn-outline-danger delete-notification-btn" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted">لا توجد إشعارات حالياً.</li>
                        @endforelse
                    </ul>
                    <div class="card-footer">
                        {{ $notifications->links() }}
                    </div>
                </div>
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