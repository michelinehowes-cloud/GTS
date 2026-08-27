@extends('layouts.app')

@section('title', 'تم تسليم السيرة الذاتية')

@section('content')
<style>
    .success-container {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .bento-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        padding: 40px;
        text-align: center;
        max-width: 500px;
        width: 100%;
    }
    .icon-wrapper {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        margin: 0 auto 20px;
        box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        animation: scaleIn 0.5s ease-out;
    }
    
    .icon-wrapper.warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 10px 20px rgba(245, 158, 11, 0.3);
    }

    .company-logo {
        height: 60px;
        object-fit: contain;
        margin-top: 15px;
        margin-bottom: 5px;
    }

    h1 {
        color: #1e293b;
        font-weight: 800;
        margin-bottom: 15px;
    }

    p {
        color: #64748b;
        font-size: 1.1rem;
        margin-bottom: 30px;
    }

    .btn-bento {
        background: #0f172a;
        color: white;
        border-radius: 16px;
        padding: 12px 24px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .btn-bento:hover {
        background: #1e293b;
        transform: translateY(-2px);
        color: white;
    }

    @keyframes scaleIn {
        from { transform: scale(0); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
</style>

<div class="container success-container">
    <div class="bento-card">
        @if($already_visited)
            <div class="icon-wrapper warning">
                <i class="fas fa-exclamation"></i>
            </div>
            <h1>تم التسليم مسبقاً</h1>
            <p>لقد قمت مسبقاً بتسليم سيرتك الذاتية لشركة <strong>{{ $company->name_ar }}</strong> في هذا المعرض.</p>
        @else
            <div class="icon-wrapper">
                <i class="fas fa-check"></i>
            </div>
            <h1>تم التسليم بنجاح!</h1>
            <p>تم استلام سيرتك الذاتية بنجاح من قبل شركة <strong>{{ $company->name_ar }}</strong>.</p>
        @endif

        @if($company->logo)
            <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name_ar }}" class="company-logo">
        @endif
        
        <div class="mt-4">
            <a href="{{ route('graduate.dashboard') }}" class="btn-bento w-100 mb-2">العودة للوحة التحكم</a>
            <a href="{{ route('job-fair.public') }}" class="btn btn-outline-secondary w-100 rounded-4 py-2">صفحة المعرض</a>
        </div>
    </div>
</div>

<!-- تفعيل مؤثرات صوتية بسيطة لتأكيد العملية -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if(!$already_visited)
            // يمكن إضافة تأثير صوتي هنا
            if(navigator.vibrate) {
                navigator.vibrate(200); // اهتزاز خفيف
            }
        @endif
    });
</script>
@endsection
