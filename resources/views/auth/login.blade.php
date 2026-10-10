<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>تسجيل الدخول - نظام إدارة الخريجين</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --university-blue: #1565c0;
            --university-gold: #f59e0b;
            --background-light: #f8fafc;
        }

        body {
            background: linear-gradient(135deg, #0d3882 0%, #1565c0 50%, #1e88e5 100%);
            font-family: 'Tajawal', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(13, 56, 130, 0.35);
            overflow: hidden;
            width: 100%;
            max-width: 420px;
        }

        .login-header {
            background: linear-gradient(135deg, #0d3882 0%, #1565c0 50%, #1e88e5 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-bottom: 3px solid #f59e0b;
        }

        .login-logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: white;
            padding: 5px;
            border: 2px solid #f59e0b;
            margin: 0 auto 15px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
        }

        .login-body {
            padding: 30px;
        }

        .btn-login {
            background: linear-gradient(135deg, #0d3882 0%, #1565c0 50%, #1e88e5 100%);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 700;
            width: 100%;
            color: white;
            transition: all 0.25s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(21, 101, 192, 0.35);
            color: white;
        }

        .form-control {
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 15px;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: #1565c0;
            box-shadow: 0 0 0 0.25rem rgba(21, 101, 192, 0.15);
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <!-- اللوقو في صفحة تسجيل الدخول -->
            <div class="login-logo">
                <img src="{{ asset('images/logo.jpg') }}" alt="شعار مكتب تدريب الخريجين"
                    style="max-width: 100%; max-height: 100%; border-radius: 8px;"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="d-none align-items-center justify-content-center w-100 h-100">
                    <i class="fas fa-graduation-cap" style="font-size: 2rem; color: #1e3a8a;"></i>
                </div>
            </div>
            <h4>مكتب تدريب الخريجين</h4>
            <p class="mb-0">جامعة طرابلس</p>
        </div>
        <div class="login-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">البريد الإلكتروني أو اسم المستخدم</label>
                    <input type="text" class="form-control @error('email') is-invalid @enderror" id="email"
                        name="email" value="{{ old('email') }}" placeholder="البريد الإلكتروني أو اسم المستخدم (مثال: admin)" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">كلمة المرور</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                        name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">تذكرني</label>
                    </div>
                    @if (Route::has('password.request'))
                        <a class="text-decoration-none" href="{{ route('password.request') }}"
                            style="font-size: 0.9rem; color: var(--university-blue);">
                            نسيت كلمة المرور؟
                        </a>
                    @endif
                </div>

                <x-honeypot />
                <x-turnstile action="login" />

                <button type="submit" class="btn btn-login mt-2">تسجيل الدخول</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const rememberCheckbox = document.getElementById('remember');

            // التحقق من وجود Capacitor
            const isNativeApp = window.Capacitor && window.Capacitor.isNativePlatform && window.Capacitor.isNativePlatform();

            // استرجاع البيانات المحفوظة عند تحميل الصفحة
            if (isNativeApp && window.Capacitor.Plugins && window.Capacitor.Plugins.Storage) {
                const Storage = window.Capacitor.Plugins.Storage;

                // استرجاع البريد الإلكتروني المحفوظ
                Storage.get({ key: 'remembered_email' }).then(result => {
                    if (result.value) {
                        emailInput.value = result.value;
                        rememberCheckbox.checked = true;
                    }
                }).catch(err => console.log('Error loading saved email:', err));
            }

            // حفظ البيانات عند تسجيل الدخول
            form.addEventListener('submit', function (e) {
                if (isNativeApp && window.Capacitor.Plugins && window.Capacitor.Plugins.Storage) {
                    const Storage = window.Capacitor.Plugins.Storage;

                    if (rememberCheckbox.checked) {
                        // حفظ البريد الإلكتروني
                        Storage.set({
                            key: 'remembered_email',
                            value: emailInput.value
                        }).catch(err => console.log('Error saving email:', err));
                    } else {
                        // حذف البريد المحفوظ
                        Storage.remove({ key: 'remembered_email' })
                            .catch(err => console.log('Error removing email:', err));
                    }
                }
            });

            // معالج الأخطاء الشامل - يعرض شاشة الترحيب عند أي خطأ
            if (isNativeApp) {
                // معالج أخطاء JavaScript
                window.addEventListener('error', function (event) {
                    console.error('JavaScript Error:', event.error);
                    showErrorSplash('حدث خطأ في التطبيق');
                });

                // معالج الأخطاء غير المعالجة (Promise rejections)
                window.addEventListener('unhandledrejection', function (event) {
                    console.error('Unhandled Promise Rejection:', event.reason);
                    showErrorSplash('حدث خطأ في الاتصال');
                });

                // معالج أخطاء الشبكة
                window.addEventListener('offline', function () {
                    showErrorSplash('لا يوجد اتصال بالإنترنت');
                });

                // دالة لعرض شاشة الخطأ
                function showErrorSplash(message) {
                    // إنشاء شاشة الخطأ إذا لم تكن موجودة
                    let errorScreen = document.getElementById('error-splash-screen');

                    if (!errorScreen) {
                        errorScreen = document.createElement('div');
                        errorScreen.id = 'error-splash-screen';
                        errorScreen.innerHTML = `
                            <div class="error-splash-content">
                                <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="error-splash-logo">
                                <div class="error-splash-pulse"></div>
                                <p class="error-message">${message}</p>
                            </div>
                        `;
                        document.body.appendChild(errorScreen);

                        // إضافة الأنماط
                        const style = document.createElement('style');
                        style.textContent = `
                            #error-splash-screen {
                                position: fixed;
                                top: 0;
                                left: 0;
                                width: 100%;
                                height: 100%;
                                background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
                                z-index: 100000;
                                display: flex;
                                justify-content: center;
                                align-items: center;
                                opacity: 0;
                                transition: opacity 0.3s ease-out;
                            }
                            
                            #error-splash-screen.show {
                                opacity: 1;
                            }
                            
                            .error-splash-content {
                                text-align: center;
                                position: relative;
                            }
                            
                            .error-splash-logo {
                                width: 120px;
                                height: 120px;
                                border-radius: 50%;
                                object-fit: cover;
                                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
                                animation: errorShake 0.6s ease-out;
                                position: relative;
                                z-index: 2;
                            }
                            
                            .error-splash-pulse {
                                position: absolute;
                                top: 50%;
                                left: 50%;
                                transform: translate(-50%, -50%);
                                width: 120px;
                                height: 120px;
                                border-radius: 50%;
                                background: rgba(255, 255, 255, 0.3);
                                animation: errorPulse 1.5s ease-out infinite;
                                z-index: 1;
                            }
                            
                            .error-message {
                                color: white;
                                font-size: 1.2rem;
                                font-weight: 600;
                                margin-top: 20px;
                                text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
                            }
                            
                            @keyframes errorShake {
                                0%, 100% { transform: translateX(0); }
                                10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
                                20%, 40%, 60%, 80% { transform: translateX(10px); }
                            }
                            
                            @keyframes errorPulse {
                                0% {
                                    transform: translate(-50%, -50%) scale(1);
                                    opacity: 0.8;
                                }
                                100% {
                                    transform: translate(-50%, -50%) scale(1.8);
                                    opacity: 0;
                                }
                            }
                        `;
                        document.head.appendChild(style);
                    } else {
                        // تحديث الرسالة
                        const messageEl = errorScreen.querySelector('.error-message');
                        if (messageEl) messageEl.textContent = message;
                    }

                    // عرض الشاشة
                    errorScreen.style.display = 'flex';
                    setTimeout(() => errorScreen.classList.add('show'), 10);

                    // إخفاء بعد 3 ثوانٍ
                    setTimeout(() => {
                        errorScreen.classList.remove('show');
                        setTimeout(() => {
                            errorScreen.style.display = 'none';
                        }, 300);
                    }, 3000);
                }
            }
        });
    </script>
</body>

</html>