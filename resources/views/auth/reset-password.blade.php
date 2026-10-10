<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>تعيين كلمة المرور الجديدة - نظام إدارة الخريجين</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --university-blue: #045db0;
            --university-gold: #eeca3e;
            --background-light: #f8fafc;
        }
        
        body {
            background: linear-gradient(135deg, var(--university-blue), #1e40af);
            font-family: 'Tajawal', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.2);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--university-blue), #1e40af);
            color: white;
            padding: 30px;
            text-align: center;
            border-bottom: 3px solid var(--university-gold);
        }
        
        .login-logo {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            background: white;
            padding: 5px;
            border: 2px solid var(--university-gold);
            margin: 0 auto 15px auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-body {
            padding: 30px;
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--university-blue), #1e40af);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 700;
            width: 100%;
            color: white;
        }
        
        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 0.95rem;
        }
        
        .form-control:focus {
            border-color: var(--university-blue);
            box-shadow: 0 0 0 0.2rem rgba(30, 58, 138, 0.15);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="login-logo">
                <img src="{{ asset('images/logo.jpg') }}" alt="شعار مكتب تدريب الخريجين" 
                     style="max-width: 100%; max-height: 100%; border-radius: 8px;"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="d-none align-items-center justify-content-center w-100 h-100">
                    <i class="fas fa-graduation-cap" style="font-size: 2rem; color: #045db0;"></i>
                </div>
            </div>
            <h4>تعيين كلمة المرور الجديدة</h4>
        </div>
        <div class="login-body">
            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="mb-3">
                    <label for="email" class="form-label">البريد الإلكتروني</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email', $request->email) }}" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">كلمة المرور الجديدة</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password" required autocomplete="new-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password-confirm" class="form-label">تأكيد كلمة المرور</label>
                    <input type="password" class="form-control" 
                           id="password-confirm" name="password_confirmation" required autocomplete="new-password">
                </div>
                
                <button type="submit" class="btn btn-login">تغيير كلمة المرور</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>