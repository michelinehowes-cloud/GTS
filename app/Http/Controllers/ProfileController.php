<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * عرض نموذج تعديل الملف الشخصي
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * تحديث الملف الشخصي
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $oldEmail = $user->email;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'current_password' => ['nullable', 'required_with:new_password'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        // مزامنة سجل الخريج إذا كان المستخدم خريجاً
        if ($user->role === 'graduate') {
            $gData = \App\Models\GraduateData::where('user_id', $user->id)
                ->orWhere('email', $oldEmail)
                ->first();
            if ($gData) {
                $gData->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'user_id' => $user->id,
                ]);
            }
        }

        // تحديث كلمة المرور إذا تم إدخالها
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->with('error', 'كلمة المرور الحالية غير صحيحة');
            }

            $user->update([
                'password' => Hash::make($request->new_password),
            ]);
        }

        return redirect()->back()->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    /**
     * حذف الحساب
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = Auth::user();
        
        if ($user->isProtectedSuperAdmin()) {
            return redirect()->back()->with('error', 'محاولة محظورة: لا يمكن حذف حساب مالك النظام الأساسي.');
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'تم حذف حسابك بنجاح');
    }
}