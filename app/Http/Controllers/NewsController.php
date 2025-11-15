<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('media_officer');
    }

    /**
     * عرض قائمة الأخبار
     */
    public function index(Request $request)
    {
        $query = News::with('creator');

        // البحث
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
        }

        // التصفية حسب الحالة
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $news = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('media.news.index', compact('news'));
    }

    /**
     * عرض نموذج إنشاء خبر جديد
     */
    public function create()
    {
        return view('media.news.create');
    }

    /**
     * حفظ خبر جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'published_at' => 'nullable|date',
            'is_active' => 'boolean'
        ]);

        $data = $request->only(['title', 'content', 'published_at', 'is_active']);
        $data['created_by'] = Auth::id();

        // رفع الصورة المصغرة إذا وجدت
        if ($request->hasFile('thumbnail')) {
            $thumbnailName = time() . '_' . uniqid() . '.' . $request->thumbnail->getClientOriginalExtension();
            $data['thumbnail_path'] = $request->thumbnail->storeAs('news/thumbnails', $thumbnailName, 'public');
        }

        News::create($data);

        return redirect()->route('media.news.index')->with('success', 'تم إنشاء الخبر بنجاح');
    }

    /**
     * عرض تفاصيل الخبر
     */
    public function show(News $news)
    {
        return view('media.news.show', compact('news'));
    }

    /**
     * عرض نموذج تعديل الخبر
     */
    public function edit(News $news)
    {
        return view('media.news.edit', compact('news'));
    }

    /**
     * تحديث الخبر
     */
    public function update(Request $request, News $news)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'published_at' => 'nullable|date',
            'is_active' => 'boolean'
        ]);

        $data = $request->only(['title', 'content', 'published_at', 'is_active']);

        // رفع الصورة المصغرة الجديدة إذا وجدت
        if ($request->hasFile('thumbnail')) {
            // حذف الصورة القديمة
            if ($news->thumbnail_path && Storage::disk('public')->exists($news->thumbnail_path)) {
                Storage::disk('public')->delete($news->thumbnail_path);
            }

            $thumbnailName = time() . '_' . uniqid() . '.' . $request->thumbnail->getClientOriginalExtension();
            $data['thumbnail_path'] = $request->thumbnail->storeAs('news/thumbnails', $thumbnailName, 'public');
        }

        $news->update($data);

        return redirect()->route('media.news.index')->with('success', 'تم تحديث الخبر بنجاح');
    }

    /**
     * حذف الخبر
     */
    public function destroy(News $news)
    {
        // حذف الصورة المصغرة
        if ($news->thumbnail_path && Storage::disk('public')->exists($news->thumbnail_path)) {
            Storage::disk('public')->delete($news->thumbnail_path);
        }

        $news->delete();

        return redirect()->route('media.news.index')->with('success', 'تم حذف الخبر بنجاح');
    }

    /**
     * تبديل حالة الخبر (تفعيل/تعطيل)
     */
    public function toggleStatus(News $news)
    {
        $news->update(['is_active' => !$news->is_active]);

        $message = $news->is_active ? 'تم تفعيل الخبر' : 'تم تعطيل الخبر';

        return redirect()->back()->with('success', $message);
    }
}
