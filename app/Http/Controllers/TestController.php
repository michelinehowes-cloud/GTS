<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\TrainingApplication;
use Illuminate\Http\Request;

class TestController extends Controller
{
    // عرض جميع الطلبات للمدير
    public function adminApplications()
    {
        $applications = TrainingApplication::with(['user', 'training.coordinator'])
            ->latest()
            ->get();
        
        $pendingCount = TrainingApplication::where('status', 'pending')->count();
        
        return view('admin.applications.index', compact('applications', 'pendingCount'));
    }

    // عرض طلبات التدريب لمنسق التدريب
   public function coordinatorApplications()
{
    $applications = TrainingApplication::with(['user', 'training'])
        ->latest()
        ->get(); // جميع الطلبات
    
    $pendingCount = TrainingApplication::where('status', 'pending')->count(); // جميع الطلبات المعلقة
    
    return view('training-coordinator.applications.index', compact('applications', 'pendingCount'));
}
    // تقديم طلب حضور تدريب - التحديث المهم!
    public function store(Request $request, Training $training)
    {
        // التحقق من أن المستخدم خريج
        if (auth()->user()->role !== 'graduate') {
            return redirect()->back()->with('error', 'ليس لديك صلاحية للتقديم على التدريبات');
        }

        // التحقق من أن التدريب نشط
        if ($training->status !== 'active') {
            return redirect()->back()->with('error', 'هذا التدريب غير متاح حالياً للتقديم');
        }

        // التحقق من عدم التقديم مسبقاً
        $existingApplication = TrainingApplication::where('training_id', $training->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existingApplication) {
            $statusMessage = [
                'pending' => 'قيد المراجعة',
                'approved' => 'مقبول', 
                'rejected' => 'مرفوض'
            ];
            return redirect()->back()->with('error', 
                'لقد قمت بالتقديم على هذا التدريب مسبقاً - الحالة: ' . 
                ($statusMessage[$existingApplication->status] ?? $existingApplication->status)
            );
        }

        // إنشاء طلب جديد - حفظ حقيقي في قاعدة البيانات
        TrainingApplication::create([
            'training_id' => $training->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
            'status' => 'pending',
            'applied_at' => now(),
        ]);

        return redirect()->route('graduate.trainings')
            ->with('success', 'تم تقديم طلبك بنجاح وسيتم مراجعته من قبل منسق التدريب');
    }

   // قبول طلب
public function approve($id)
{
    $application = TrainingApplication::findOrFail($id);
    
    // التحقق من الصلاحية (المدير أو منسق التدريب)
    $user = auth()->user();
    if (!in_array($user->role, ['admin', 'training_coordinator'])) {
        return redirect()->back()->with('error', 'ليس لديك صلاحية لقبول هذا الطلب');
    }
    
    $application->update(['status' => 'approved']);
    
    return redirect()->back()->with('success', 'تم قبول الطلب بنجاح');
}

// رفض طلب
public function reject($id)
{
    $application = TrainingApplication::findOrFail($id);
    
    // التحقق من الصلاحية (المدير أو منسق التدريب)
    $user = auth()->user();
    if (!in_array($user->role, ['admin', 'training_coordinator'])) {
        return redirect()->back()->with('error', 'ليس لديك صلاحية لرفض هذا الطلب');
    }
    
    $application->update(['status' => 'rejected']);
    
    return redirect()->back()->with('success', 'تم رفض الطلب بنجاح');
}
}