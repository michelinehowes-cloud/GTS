<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        
        // توجيه المستخدم حسب الدور
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'training_coordinator':
                return redirect()->route('training.dashboard');
            case 'placement_coordinator':
                return redirect()->route('placement.dashboard');
            case 'graduate':
                return redirect()->route('graduate.dashboard');
            default:
                return view('dashboard', ['user' => $user]);
        }
    }

    public function adminDashboard()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }
        return view('admin.dashboard', ['user' => auth()->user()]);
    }

    public function trainingDashboard()
    {
        if (auth()->user()->role !== 'training_coordinator') {
            abort(403);
        }
        return view('training.dashboard', ['user' => auth()->user()]);
    }

    public function placementDashboard()
    {
        if (auth()->user()->role !== 'placement_coordinator') {
            abort(403);
        }
        return view('placement.dashboard', ['user' => auth()->user()]);
    }

    public function graduateDashboard()
    {
        if (auth()->user()->role !== 'graduate') {
            abort(403);
        }
        return view('graduate.dashboard', ['user' => auth()->user()]);
    }
}