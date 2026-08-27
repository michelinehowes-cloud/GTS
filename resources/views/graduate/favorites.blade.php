@extends('layouts.app')

@section('title', 'شركاتي المفضلة')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-heart text-danger"></i> قائمة الشركات المفضلة
        </h1>
        <a href="{{ route('job-fair.public') }}" class="btn btn-primary">
            <i class="fas fa-store"></i> العودة للمعرض
        </a>
    </div>

    <div class="row">
        @if($favorites->isEmpty())
            <div class="col-12">
                <div class="alert alert-info text-center p-5 border-0 shadow-sm">
                    <i class="far fa-heart fa-4x text-muted mb-3"></i>
                    <h4>قائمة المفضلة فارغة</h4>
                    <p class="text-muted">قم بزيارة صفحة المعرض لاستكشاف الشركات وإضافتها للمفضلة هنا.</p>
                </div>
            </div>
        @else
            @foreach($favorites as $fav)
                <div class="col-md-6 col-lg-4 mb-4" id="fav-card-{{ $fav->company_id }}">
                    <div class="card h-100 shadow-sm border-0 position-relative hover-lift">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light rounded p-3 text-primary fs-3">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1">{{ $fav->company->name ?? 'شركة' }}</h5>
                                        <span class="text-muted small">تاريخ الإضافة: {{ $fav->created_at->format('Y-m-d') }}</span>
                                    </div>
                                </div>
                                
                                <button class="btn btn-sm text-danger remove-fav" data-company="{{ $fav->company_id }}" title="إزالة من المفضلة">
                                    <i class="fas fa-times fs-5"></i>
                                </button>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('messages.show', $fav->company_id) }}" class="btn btn-outline-success btn-sm w-100">
                                    <i class="fas fa-envelope"></i> مراسلة الشركة
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

<style>
    .hover-lift {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
</style>
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
});
</script>
@endpush
