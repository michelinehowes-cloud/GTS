@extends('layouts.app')

@section('title', 'شركاتي المفضلة')

@section('content')
<div class="container-fluid px-2 px-md-3">
    <!-- Unified Page Hero Banner -->
    <x-page-hero
        title="قائمة الشركات المفضلة"
        subtitle="الشركات التي قمت بحفظها لسهولة المتابعة والتقديم السريع والتواصل المباشر"
        icon="fas fa-heart"
        :breadcrumbs="[
            ['label' => 'منصة الخريجين', 'url' => route('graduate.dashboard')],
            ['label' => 'الشركات المفضلة']
        ]"
        :badge="$favorites->count() > 0 ? $favorites->count() . ' شركة محفوظة' : null"
        badgeIcon="fas fa-bookmark"
    >
        <a href="{{ route('job-fair.public') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-store fs-6"></i>
            <span>استكشاف الشركات</span>
        </a>
        <a href="{{ route('graduate.dashboard') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-home fs-6"></i>
            <span>لوحة التحكم</span>
        </a>
    </x-page-hero>

    @if($favorites->isEmpty())
        <div class="card-modern text-center py-5">
            <div class="card-body py-5">
                <i class="far fa-heart display-3 text-muted mb-3 opacity-50"></i>
                <h4 class="fw-bold text-dark mb-2">قائمة المفضلة فارغة</h4>
                <p class="text-muted mb-4">قم بزيارة صفحة المعرض والشركات لاستكشاف الشركات وإضافتها إلى قائمة مفضلتك</p>
                <a href="{{ route('job-fair.public') }}" class="btn btn-primary-modern px-4 py-2">
                    <i class="fas fa-search me-1"></i> استكشاف الشركات
                </a>
            </div>
        </div>
    @else
        <div class="row g-4 mb-4">
            @foreach($favorites as $fav)
                <div class="col-md-6 col-lg-4" id="fav-card-{{ $fav->company_id }}">
                    <div class="card-modern h-100 d-flex flex-column">
                        <div class="card-body p-4 flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center p-3" style="width: 48px; height: 48px;">
                                        <i class="fas fa-building fa-lg"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1">{{ $fav->company->name ?? 'شركة' }}</h5>
                                        <span class="text-muted small">أضيفت: {{ $fav->created_at->format('Y-m-d') }}</span>
                                    </div>
                                </div>
                                
                                <button class="btn btn-sm btn-outline-danger-modern remove-fav" data-company="{{ $fav->company_id }}" title="إزالة من المفضلة">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            @if($fav->company && $fav->company->industry)
                                <div class="small text-muted mb-2">
                                    <i class="fas fa-tag me-1 text-primary"></i> المجال: {{ $fav->company->industry }}
                                </div>
                            @endif
                        </div>

                        <div class="card-footer bg-white p-3 border-top">
                            <a href="{{ route('messages.show', $fav->company_id) }}" class="btn btn-outline-primary-modern btn-sm w-100">
                                <i class="fas fa-envelope me-1"></i> مراسلة الشركة
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.remove-fav').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const companyId = this.dataset.company;
        
        if(confirm('هل أنت متأكد من إزالة الشركة من المفضلة؟')) {
            fetch(`/graduate/favorites/${companyId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'removed') {
                    const card = document.getElementById(`fav-card-${companyId}`);
                    card.style.transition = "opacity 0.3s ease";
                    card.style.opacity = 0;
                    setTimeout(() => card.remove(), 300);
                }
            })
            .catch(err => console.error(err));
        }
    });
</script>
@endpush
