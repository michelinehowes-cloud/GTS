<?php
$f = 'c:/Users/Sohib/graduate_training_system/resources/views/auth/login.blade.php';
$c = file_get_contents($f);

// Login already has its own structure, but we want it to look like Bento.
// Actually, it's easier to just rebuild it cleanly using the layout.
// Since login usually doesn't use the sidebar layout, we can use public.blade.php.

$newLogin = "@extends('layouts.public')
@section('title', 'تسجيل الدخول')

@section('content')
<div class=\"container py-5\">
    <div class=\"row justify-content-center\">
        <div class=\"col-md-6\">
            <x-bento-form title=\"تسجيل الدخول\" subtitle=\"مرحباً بك مجدداً في نظام إدارة الخريجين\" icon=\"fa-sign-in-alt\">
                <form method=\"POST\" action=\"{{ route('login') }}\">
                    @csrf
                    
                    <div class=\"form-section mb-4\">
                        <div class=\"col-12 mb-3\">
                            <label for=\"email\" class=\"form-label fw-bold\">البريد الإلكتروني <span class=\"text-danger\">*</span></label>
                            <input id=\"email\" type=\"email\" class=\"form-control form-control-lg @error('email') is-invalid @enderror\" name=\"email\" value=\"{{ old('email') }}\" required autocomplete=\"email\" autofocus>
                            @error('email')
                                <span class=\"invalid-feedback\" role=\"alert\">
                                    <strong>{{ \$message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class=\"col-12 mb-3\">
                            <label for=\"password\" class=\"form-label fw-bold\">كلمة المرور <span class=\"text-danger\">*</span></label>
                            <input id=\"password\" type=\"password\" class=\"form-control form-control-lg @error('password') is-invalid @enderror\" name=\"password\" required autocomplete=\"current-password\">
                            @error('password')
                                <span class=\"invalid-feedback\" role=\"alert\">
                                    <strong>{{ \$message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class=\"col-12 mb-3 d-flex justify-content-between align-items-center\">
                            <div class=\"form-check\">
                                <input class=\"form-check-input\" type=\"checkbox\" name=\"remember\" id=\"remember\" {{ old('remember') ? 'checked' : '' }}>
                                <label class=\"form-check-label\" for=\"remember\">
                                    تذكرني
                                </label>
                            </div>
                            @if (Route::has('password.request'))
                                <a class=\"text-primary text-decoration-none\" href=\"{{ route('password.request') }}\">
                                    نسيت كلمة المرور؟
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class=\"text-center\">
                        <button type=\"submit\" class=\"btn-register w-100\">
                            <i class=\"fas fa-sign-in-alt me-2\"></i> دخول
                        </button>
                    </div>
                    
                    <div class=\"text-center mt-4\">
                        <p class=\"mb-0\">ليس لديك حساب؟</p>
                        <a href=\"{{ route('graduate.register') }}\" class=\"btn btn-outline-primary mt-2 rounded-pill px-4\">
                            تسجيل خريج جديد
                        </a>
                        <a href=\"{{ route('company.register') }}\" class=\"btn btn-outline-secondary mt-2 rounded-pill px-4\">
                            تسجيل كشركة
                        </a>
                    </div>
                </form>
            </x-bento-form>
        </div>
    </div>
</div>
@endsection";

file_put_contents($f, $newLogin);
echo "Done login\n";
