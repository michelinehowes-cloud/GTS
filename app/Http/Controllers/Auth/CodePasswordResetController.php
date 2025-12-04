<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CodePasswordResetController extends Controller
{
    // عرض صفحة طلب الرمز (إدخال الإيميل)
    public function create()
    {
        return view('auth.passwords.code-request');
    }

    // إرسال الرمز
    public function store(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        // حذف الرموز القديمة لهذا الإيميل
        DB::table('password_reset_codes')->where('email', $request->email)->delete();

        // إنشاء رمز جديد (6 أرقام)
        $code = rand(100000, 999999);

        DB::table('password_reset_codes')->insert([
            'email' => $request->email,
            'code' => $code,
            'created_at' => Carbon::now()
        ]);

        // إرسال الإيميل
        // هنا سنستخدم NotificationMail أو Mailable بسيط. للسرعة سأستخدم Mail::raw أو Mailable مخصص إذا لزم الأمر.
        // سأستخدم NotificationService لإرسال إشعار يحتوي على الرمز، أو إرسال بريد مباشر.
        // الأفضل إرسال بريد مباشر مخصص للرمز.

        try {
            Mail::send('emails.reset-code', ['code' => $code], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('رمز استعادة كلمة المرور');
            });
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'فشل إرسال البريد الإلكتروني. حاول مرة أخرى.']);
        }

        return redirect()->route('password.code.verify', ['email' => $request->email]);
    }

    // عرض صفحة التحقق (إدخال الرمز وكلمة المرور الجديدة)
    public function verify(Request $request)
    {
        return view('auth.passwords.code-verify', ['email' => $request->email]);
    }

    // تغيير كلمة المرور
    public function update(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|numeric',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // التحقق من الرمز
        $record = DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->where('code', $request->code)
            ->first();

        if (!$record) {
            return back()->withErrors(['code' => 'رمز التحقق غير صحيح.']);
        }

        // التحقق من صلاحية الرمز (مثلاً 15 دقيقة)
        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            return back()->withErrors(['code' => 'انتهت صلاحية الرمز. اطلب رمزاً جديداً.']);
        }

        // تغيير كلمة المرور
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // حذف الرمز المستخدم
        DB::table('password_reset_codes')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'تم تغيير كلمة المرور بنجاح. يمكنك تسجيل الدخول الآن.');
    }
}
