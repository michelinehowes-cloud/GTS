<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // التعامل الذكي مع انتهاء صلاحية الجلسة أو الرمز المميز CSRF دون إظهار صفحة 419 المزعجة
        $this->renderable(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if (\Illuminate\Support\Facades\Auth::check()) {
                \Illuminate\Support\Facades\Auth::logout();
            }
            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            return redirect('/')->with('info', 'تم تسجيل الخروج بنجاح نظراً لانتهاء صلاحية الجلسة.');
        });
    }
}
