<?php
// ملف: app/Http/Controllers/CompanyController.php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::latest()->get();
        return view('admin.companies.index', compact('companies'));
    }

    public function create()
    {
        return view('admin.companies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'industry' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string',
            'password' => 'required|min:8|confirmed',
        ]);

        // إنشاء المستخدم أولاً
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'company',
        ]);

        // إنشاء الشركة
        Company::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'industry' => $request->industry,
            'address' => $request->address,
            'description' => $request->description,
            'is_approved' => true, // الموافقة تلقائياً عند الإنشاء من قبل المدير
        ]);

        return redirect()->route('admin.companies')
               ->with('success', 'تم إضافة الشركة بنجاح');
    }

    public function edit($id)
    {
        $company = Company::findOrFail($id);
        return view('admin.companies.edit', compact('company'));
    }

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email,' . $id,
            'phone' => 'required|string|max:20',
            'industry' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string',
        ]);

        $company->update($request->all());

        // تحديث بيانات المستخدم المرتبط
        if ($company->user) {
            $company->user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
        }

        return redirect()->route('admin.companies')
               ->with('success', 'تم تحديث بيانات الشركة بنجاح');
    }

    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        
        // حذف المستخدم المرتبط أولاً
        if ($company->user) {
            $company->user->delete();
        }
        
        // ثم حذف الشركة
        $company->delete();

        return redirect()->route('admin.companies')
               ->with('success', 'تم حذف الشركة بنجاح');
    }
}