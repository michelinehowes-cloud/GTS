<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('id', '!=', auth()->id())->latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,training_coordinator,graduate,partnership_officer,career_guidance_officer,company,evaluation_followup',
            'phone' => 'nullable|string|max:20',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
        ]);

        return redirect()->route('admin.users')->with('success', 'تم إضافة المستخدم بنجاح');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
        'role' => 'required|in:admin,training_coordinator,graduate,partnership_officer,career_guidance_officer,company,evaluation_followup', // ✅ تحديث القائمة
            'phone' => 'nullable|string|max:20',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
        ];

        if ($request->password) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('admin.users')->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id == auth()->id()) {
            return redirect()->route('admin.users')->with('error', 'لا يمكن حذف حسابك الشخصي');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'تم حذف المستخدم بنجاح');
    }
}
