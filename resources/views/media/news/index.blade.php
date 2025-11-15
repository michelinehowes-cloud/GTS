@extends('layouts.app')

@section('title', 'إدارة الأخبار')

@section('page-title', 'إدارة الأخبار')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-newspaper me-2"></i>إدارة الأخبار
                </h5>
                <a href="{{ route('media.news.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>إضافة خبر جديد
                </a>
            </div>
            <div class="card-body">
                @if($news->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>العنوان</th>
                                    <th>تاريخ النشر</th>
                                    <th>الحالة</th>
                                    <th>الصورة المصغرة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($news as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h6 class="mb-0">{{ Str::limit($item->title, 50) }}</h6>
                                                <small class="text-muted">{{ Str::limit(strip_tags($item->content), 80) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($item->published_at)
                                            {{ $item->published_at->format('d/m/Y') }}
                                            <br>
                                            <small class="text-muted">{{ $item->published_at->diffForHumans() }}</small>
                                        @else
                                            <span class="text-warning">غير منشور</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $item->is_active ? 'success' : 'secondary' }}">
                                            {{ $item->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($item->thumbnail_path)
                                            <img src="{{ Storage::url($item->thumbnail_path) }}" alt="صورة مصغرة" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <span class="text-muted">لا توجد</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('media.news.show', $item) }}" class="btn btn-sm btn-outline-info" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('media.news.edit', $item) }}" class="btn btn-sm btn-outline-warning" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-{{ $item->is_active ? 'secondary' : 'success' }}" onclick="toggleStatus({{ $item->id }}, {{ $item->is_active ? 'false' : 'true' }})" title="{{ $item->is_active ? 'إلغاء التفعيل' : 'تفعيل' }}">
                                                <i class="fas fa-{{ $item->is_active ? 'eye-slash' : 'eye' }}"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteNews({{ $item->id }}, '{{ Str::limit($item->title, 30) }}')" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- التنقل بين الصفحات -->
                    <div class="d-flex justify-content-center">
                        {{ $news->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-newspaper fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted mb-3">لا توجد أخبار</h4>
                        <p class="text-muted mb-4">ابدأ بإضافة أول خبر لموقعك</p>
                        <a href="{{ route('media.news.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>إضافة خبر جديد
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleStatus(newsId, newStatus) {
    const action = newStatus ? 'تفعيل' : 'إلغاء تفعيل';
    if (confirm(`هل أنت متأكد من ${action} هذا الخبر؟`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/media/news/${newsId}/toggle-status`;

        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = document.querySelector('meta[name="csrf-token"]').content;
        form.appendChild(csrfField);

        document.body.appendChild(form);
        form.submit();
    }
}

function deleteNews(newsId, newsTitle) {
    if (confirm(`هل أنت متأكد من حذف الخبر "${newsTitle}"؟`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/media/news/${newsId}`;

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);

        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = document.querySelector('meta[name="csrf-token"]').content;
        form.appendChild(csrfField);

        document.body.appendChild(form);
        form.submit();
    }
}

// إضافة تأكيد CSRF token
document.addEventListener('DOMContentLoaded', function() {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    }
});
</script>
@endpush
