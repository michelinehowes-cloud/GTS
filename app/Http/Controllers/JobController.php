<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Company;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::with('company')->get();
        return view('placement.jobs.index', compact('jobs'));
    }

    public function create()
    {
        $companies = Company::where('verified', true)->get();
        return view('placement.jobs.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:internship,full_time,part_time,contract',
            'company_id' => 'required|exists:companies,id',
            'location' => 'required|string',
            'seats' => 'required|integer|min:1',
            'deadline' => 'required|date|after:today',
        ]);

        Job::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'company_id' => $request->company_id,
            'location' => $request->location,
            'seats' => $request->seats,
            'deadline' => $request->deadline,
            'salary' => $request->salary,
            'requirements' => $request->requirements ? json_encode(explode(',', $request->requirements)) : [],
            'status' => 'active',
        ]);

        return redirect()->route('placement.jobs')->with('success', 'تم إضافة فرصة العمل بنجاح');
    }

    public function companyJobs()
    {
        $jobs = Job::where('company_id', auth()->user()->company_id)->get();
        return view('company.jobs.index', compact('jobs'));
    }

    public function availableJobs()
    {
        $jobs = Job::with('company')->where('status', 'active')->get();
        return view('graduate.jobs.index', compact('jobs'));
    }
}