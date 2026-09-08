<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PublicProfileController extends Controller
{
    public function show($id)
    {
        $graduate = User::with([
            'graduateData',
            'trainingApplications' => function ($q) {
                $q->where('status', 'approved')->with('training');
            }
        ])->findOrFail($id);

        // التأكد من أن المستخدم خريج
        if ($graduate->role !== 'graduate') {
            abort(404, 'هذا الملف الشخصي غير موجود أو ليس لخريج.');
        }

        return view('graduate.public_profile', compact('graduate'));
    }
}
