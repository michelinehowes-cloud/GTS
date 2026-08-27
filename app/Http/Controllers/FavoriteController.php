<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * عرض الشركات المفضلة للخريج
     */
    public function index()
    {
        $favorites = Favorite::where('graduate_id', auth()->id())
            ->with('company')
            ->get();

        return view('graduate.favorites', compact('favorites'));
    }

    /**
     * إضافة أو إزالة شركة من المفضلة
     */
    public function toggle(Request $request, $companyId)
    {
        $graduateId = auth()->id();
        
        $favorite = Favorite::where('graduate_id', $graduateId)
            ->where('company_id', $companyId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'status' => 'removed',
                'message' => 'تمت إزالة الشركة من المفضلة'
            ]);
        } else {
            Favorite::create([
                'graduate_id' => $graduateId,
                'company_id' => $companyId
            ]);
            return response()->json([
                'status' => 'added',
                'message' => 'تمت إضافة الشركة للمفضلة'
            ]);
        }
    }
}
