<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function store(Request $request, Job $job)
    {
        // التحقق من عدم التقديم مسبقاً
        $existingApplication = Application::where('user_id', auth()->id())
            ->where('job_id', $job->id)
            ->first();

        if ($existingApplication) {
            return redirect()->back()->with('error', 'لقد تقدمت لهذه الوظيفة مسبقاً');
        }

        Application::create([
            'user_id' => auth()->id(),
            'job_id' => $job->id,
            'resume_url' => 'default.pdf', // يمكنك تعديل هذا لرفع ملف
            'cover_letter' => $request->cover_letter,
            'status' => 'received',
        ]);

        return redirect()->back()->with('success', 'تم تقديم طلبك بنجاح');
    }
}