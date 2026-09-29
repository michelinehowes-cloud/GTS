<?php

namespace App\Http\Controllers;

use App\Models\TrainingProgram;
use App\Models\User;
use Illuminate\Http\Request;

class TrainingCoordinatorController extends Controller
{
    public function dashboard()
    {
        $programs = TrainingProgram::where('coordinator_id', auth()->id())->get();
        $activePrograms = $programs->where('status', 'active');
        
        return view('training-coordinator.dashboard', compact('programs', 'activePrograms'));
    }

    public function createProgram(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'capacity' => 'required|integer|min:1',
        ], [
            'end_date.after_or_equal' => 'يجب أن يكون تاريخ الانتهاء في نفس يوم البدء أو بعده.',
        ]);

        TrainingProgram::create([
            'title' => $request->title,
            'description' => $request->description,
            'duration' => $request->duration,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'capacity' => $request->capacity,
            'coordinator_id' => auth()->id(),
            'requirements' => $request->requirements ?? [],
        ]);

        return redirect()->back()->with('success', 'تم إنشاء برنامج التدريب بنجاح');
    }

    public function manageParticipants($programId)
    {
        $program = TrainingProgram::findOrFail($programId);
        $graduates = User::where('role', 'graduate')->get();
        
        return view('training-coordinator.participants', compact('program', 'graduates'));
    }
}