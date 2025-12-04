<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrainerController extends Controller
{
    /**
     * عرض قائمة المدربين
     */
    public function index()
    {
        $trainers = Trainer::withCount('trainings')->latest()->paginate(10);
        return view('training_coordinator.trainers.index', compact('trainers'));
    }

    /**
     * عرض نموذج إضافة مدرب جديد
     */
    public function create()
    {
        return view('training_coordinator.trainers.create');
    }

    /**
     * حفظ مدرب جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:trainers,email',
            'phone' => 'nullable|string|max:20',
            'specialization' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'linkedin_url' => 'nullable|url',
        ]);

        $data = $request->all();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('trainers', 'public');
        }

        Trainer::create($data);

        return redirect()->route('training-coordinator.trainers.index')
            ->with('success', 'تم إضافة المدرب بنجاح');
    }

    /**
     * عرض تفاصيل المدرب
     */
    public function show(Trainer $trainer)
    {
        // جلب التدريبات المرتبطة بالمدرب
        $trainer->load('trainings');
        return view('training_coordinator.trainers.show', compact('trainer'));
    }

    /**
     * عرض نموذج تعديل بيانات المدرب
     */
    public function edit(Trainer $trainer)
    {
        return view('training_coordinator.trainers.edit', compact('trainer'));
    }

    /**
     * تحديث بيانات المدرب
     */
    public function update(Request $request, Trainer $trainer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:trainers,email,' . $trainer->id,
            'phone' => 'nullable|string|max:20',
            'specialization' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'linkedin_url' => 'nullable|url',
        ]);

        $data = $request->all();

        if ($request->hasFile('photo')) {
            // حذف الصورة القديمة إذا وجدت
            if ($trainer->photo) {
                Storage::disk('public')->delete($trainer->photo);
            }
            $data['photo'] = $request->file('photo')->store('trainers', 'public');
        }

        $trainer->update($data);

        return redirect()->route('training-coordinator.trainers.index')
            ->with('success', 'تم تحديث بيانات المدرب بنجاح');
    }

    /**
     * حذف المدرب
     */
    public function destroy(Trainer $trainer)
    {
        if ($trainer->photo) {
            Storage::disk('public')->delete($trainer->photo);
        }

        $trainer->delete();

        return redirect()->route('training-coordinator.trainers.index')
            ->with('success', 'تم حذف المدرب بنجاح');
    }
}
