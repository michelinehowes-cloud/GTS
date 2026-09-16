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
        $this->middleware('auth')->except(['publicShow']);
        $this->middleware('media_officer')->except(['publicShow']);
    }

    /**
     * عرض قائمة الأخبار
     */
    public function index(Request $request)
    {
        $query = News::with('creator');

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // التصفية حسب الحالة
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $stats = [
            'total' => News::count(),
            'active' => News::where('is_active', true)->count(),
            'draft' => News::where('is_active', false)->count(),
            'with_images' => News::whereNotNull('thumbnail_path')->count(),
        ];

        $news = $query->orderBy('published_at', 'desc')->orderBy('created_at', 'desc')->paginate(12);

        return view('media.news.index', compact('news', 'stats'));
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
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:published_at',
            'is_active' => 'nullable',
        ]);

        $data = [
            'title' => $request->title,
            'content' => $request->input('content'),
            'published_at' => $request->published_at ? $request->published_at : now(),
            'expires_at' => $request->expires_at,
            'is_active' => $request->boolean('is_active'),
            'created_by' => Auth::id(),
        ];

        // رفع الصورة المصغرة إذا وجدت
        if ($request->hasFile('thumbnail')) {
            $thumbnailName = time() . '_' . uniqid() . '.' . $request->thumbnail->getClientOriginalExtension();
            $data['thumbnail_path'] = $request->thumbnail->storeAs('news/thumbnails', $thumbnailName, 'public');
        }

        News::create($data);

        return redirect()->route('media.news.index')->with('success', 'تم إنشاء وحفظ الخبر بنجاح.');
    }

    /**
     * عرض تفاصيل الخبر
     */
    public function show(News $news)
    {
        $news->load('creator');
        $relatedNews = News::where('id', '!=', $news->id)->latest()->take(3)->get();
        return view('media.news.show', compact('news', 'relatedNews'));
    }

    /**
     * عرض تفاصيل الخبر للجمهور ورواد المنصة
     */
    public function publicShow(News $news)
    {
        abort_unless($news->is_active, 404);
        $news->load('creator');
        $relatedNews = News::published()->where('id', '!=', $news->id)->latest()->take(4)->get();
        return view('media.news.show', compact('news', 'relatedNews'));
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
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:published_at',
            'is_active' => 'nullable',
        ]);

        $data = [
            'title' => $request->title,
            'content' => $request->input('content'),
            'published_at' => $request->published_at,
            'expires_at' => $request->expires_at,
            'is_active' => $request->boolean('is_active'),
        ];

        // رفع الصورة المصغرة الجديدة إذا وجدت
        if ($request->hasFile('thumbnail')) {
            if ($news->thumbnail_path && Storage::disk('public')->exists($news->thumbnail_path)) {
                Storage::disk('public')->delete($news->thumbnail_path);
            }

            $thumbnailName = time() . '_' . uniqid() . '.' . $request->thumbnail->getClientOriginalExtension();
            $data['thumbnail_path'] = $request->thumbnail->storeAs('news/thumbnails', $thumbnailName, 'public');
        }

        $news->update($data);

        return redirect()->route('media.news.index')->with('success', 'تم تحديث الخبر بنجاح.');
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
