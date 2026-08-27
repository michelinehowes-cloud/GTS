<?php

namespace App\Http\Controllers;

use App\Models\JobFair;
use App\Models\JobFairEvent;
use Illuminate\Http\Request;

class JobFairEventController extends Controller
{
    public function index(JobFair $fair)
    {
        $events = $fair->events()->orderBy('start_time')->get();
        return view('job-fair.admin.events', compact('fair', 'events'));
    }

    public function store(Request $request, JobFair $fair)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'speaker_name' => 'nullable|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'location' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:1',
        ]);

        $fair->events()->create($validated);

        return back()->with('success', 'تمت إضافة الفعالية بنجاح.');
    }

    public function destroy(JobFairEvent $event)
    {
        $event->delete();
        return back()->with('success', 'تم حذف الفعالية بنجاح.');
    }
}
