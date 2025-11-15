@extends('layouts.public')

@section('title', 'شكراً لك - ' . $survey->title)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow-lg border-0 text-center">
                <div class="card-body p-5">
                    <div class="success-icon mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>

                    <h2 class="text-success mb-3">شكراً لك!</h2>

                    <p class="lead mb-4">
                        تم استلام ردك على الاستبيان <strong>"{{ $survey->title }}"</strong> بنجاح.
                    </p>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        ردك مهم بالنسبة لنا ويساعدنا في تحسين خدماتنا.
                    </div>

                    <div class="mt-4">
                        <a href="{{ url('/') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-home mr-2"></i>
                            العودة للصفحة الرئيسية
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.success-icon {
    animation: checkmark 0.8s ease-in-out;
}

@keyframes checkmark {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.2);
        opacity: 0.8;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.card {
    border-radius: 15px;
    overflow: hidden;
}

.btn-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
    border: none;
    border-radius: 25px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
}
</style>
@endsection
