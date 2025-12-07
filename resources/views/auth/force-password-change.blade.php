<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تغيير كلمة المرور - مطلوب</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-change-container {
            max-width: 500px;
            width: 100%;
            padding: 20px;
        }

        .password-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .card-header i {
            font-size: 3rem;
            margin-bottom: 15px;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-10px);
            }

            75% {
                transform: translateX(10px);
            }
        }

        .card-header h3 {
            margin: 0;
            font-weight: 700;
        }

        .card-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .card-body {
            padding: 40px;
        }

        .alert {
            border-radius: 12px;
            border: none;
            margin-bottom: 25px;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.15);
        }

        .password-requirements {
            background: #f3f4f6;
            border-radius: 12px;
            padding: 15px;
            margin-top: 20px;
            font-size: 0.85rem;
        }

        .password-requirements h6 {
            font-weight: 700;
            color: #374151;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        .password-requirements ul {
            margin: 0;
            padding-right: 20px;
        }

        .password-requirements li {
            color: #6b7280;
            margin-bottom: 5px;
        }

        .password-requirements li.valid {
            color: #10b981;
        }

        .password-requirements li.valid::marker {
            content: "✓ ";
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            border: none;
            border-radius: 12px;
            padding: 14px 30px;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
        }

        .btn-secondary {
            background: #6b7280;
            border: none;
            border-radius: 12px;
            padding: 12px 25px;
            font-weight: 600;
            width: 100%;
            margin-top: 10px;
        }

        .password-toggle {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6b7280;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: #3b82f6;
        }

        .input-group {
            position: relative;
        }

        .invalid-feedback {
            display: block;
            margin-top: 5px;
            font-size: 0.875rem;
            color: #ef4444;
        }
    </style>
</head>

<body>
    <div class="password-change-container">
        <div class="password-card">
            <div class="card-header">
                <i class="fas fa-lock"></i>
                <h3>تغيير كلمة المرور مطلوب</h3>
                <p>يجب عليك تغيير كلمة المرور المؤقتة قبل المتابعة</p>
            </div>

            <div class="card-body">
                @if(session('warning'))
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ session('warning') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>يرجى تصحيح الأخطاء التالية:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update.force') }}" id="passwordForm">
                    @csrf

                    <!-- كلمة المرور الحالية -->
                    <div class="mb-4">
                        <label for="current_password" class="form-label">
                            <i class="fas fa-key me-1"></i>
                            كلمة المرور الحالية
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                id="current_password" name="current_password" required autofocus>
                            <span class="password-toggle" onclick="togglePassword('current_password')">
                                <i class="fas fa-eye" id="current_password_icon"></i>
                            </span>
                        </div>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- كلمة المرور الجديدة -->
                    <div class="mb-4">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-1"></i>
                            كلمة المرور الجديدة
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" required>
                            <span class="password-toggle" onclick="togglePassword('password')">
                                <i class="fas fa-eye" id="password_icon"></i>
                            </span>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- تأكيد كلمة المرور -->
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">
                            <i class="fas fa-check-circle me-1"></i>
                            تأكيد كلمة المرور الجديدة
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation" required>
                            <span class="password-toggle" onclick="togglePassword('password_confirmation')">
                                <i class="fas fa-eye" id="password_confirmation_icon"></i>
                            </span>
                        </div>
                    </div>

                    <!-- متطلبات كلمة المرور -->
                    <div class="password-requirements">
                        <h6><i class="fas fa-info-circle me-1"></i> متطلبات كلمة المرور:</h6>
                        <ul id="requirements">
                            <li id="req-length">8 أحرف على الأقل</li>
                            <li id="req-uppercase">حرف كبير واحد على الأقل (A-Z)</li>
                            <li id="req-lowercase">حرف صغير واحد على الأقل (a-z)</li>
                            <li id="req-number">رقم واحد على الأقل (0-9)</li>
                            <li id="req-special">رمز خاص واحد على الأقل (!@#$%^&*)</li>
                        </ul>
                    </div>

                    <!-- أزرار الإجراءات -->
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            تغيير كلمة المرور
                        </button>

                        <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                            @csrf
                            <button type="submit" class="btn btn-secondary">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                تسجيل الخروج
                            </button>
                        </form>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '_icon');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Password validation
        const passwordInput = document.getElementById('password');
        const requirements = {
            length: document.getElementById('req-length'),
            uppercase: document.getElementById('req-uppercase'),
            lowercase: document.getElementById('req-lowercase'),
            number: document.getElementById('req-number'),
            special: document.getElementById('req-special')
        };

        passwordInput.addEventListener('input', function () {
            const value = this.value;

            // Check length
            if (value.length >= 8) {
                requirements.length.classList.add('valid');
            } else {
                requirements.length.classList.remove('valid');
            }

            // Check uppercase
            if (/[A-Z]/.test(value)) {
                requirements.uppercase.classList.add('valid');
            } else {
                requirements.uppercase.classList.remove('valid');
            }

            // Check lowercase
            if (/[a-z]/.test(value)) {
                requirements.lowercase.classList.add('valid');
            } else {
                requirements.lowercase.classList.remove('valid');
            }

            // Check number
            if (/[0-9]/.test(value)) {
                requirements.number.classList.add('valid');
            } else {
                requirements.number.classList.remove('valid');
            }

            // Check special character
            if (/[!@#$%^&*(),.?":{}|<>]/.test(value)) {
                requirements.special.classList.add('valid');
            } else {
                requirements.special.classList.remove('valid');
            }
        });
    </script>
</body>

</html>