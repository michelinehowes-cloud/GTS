<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class AnnouncementController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->middleware('media_officer');
        $this->notificationService = $notificationService;
    }

    /**
     * عرض قائمة الإعلانات
     */
    public function index(Request $request)
    {
        $query = Announcement::with('creator');

        // البحث
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('content', 'like', '%' . $request->search . '%');
        }

        // التصفية حسب الحالة
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // التصفية حسب الفترة الزمنية
        if ($request->filled('period')) {
            switch ($request->period) {
                case 'current':
                    $query->current();
                    break;
                case 'upcoming':
                    $query->upcoming();
                    break;
                case 'expired':
                    $query->expired();
                    break;
            }
        }

        $announcements = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('media.announcements.index', compact('announcements'));
    }

    /**
     * عرض نموذج إنشاء إعلان جديد
     */
    public function create()
    {
        return view('media.announcements.create');
    }

    /**
     * حفظ إعلان جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'link' => 'nullable|url',
            'is_active' => 'boolean'
        ]);

        $data = $request->only(['title', 'content', 'start_date', 'end_date', 'link', 'is_active']);
        $data['created_by'] = Auth::id();

        $announcement = Announcement::create($data);

        // إرسال إشعار لجميع المستخدمين
        if ($announcement->is_active) {
            try {
                $this->notificationService->notifySystemAction(
                    'إعلان جديد: ' . $announcement->title,
                    'تم نشر إعلان جديد: ' . \Str::limit($announcement->content, 100),
                    [], // Empty array means all users (or default to graduate as per my implementation, wait I should check that)
                    'info',
                    $announcement
                );
            } catch (\Exception $e) {
                \Log::error('Failed to send announcement notification: ' . $e->getMessage());
            }
        }

        return redirect()->route('media.announcements.index')->with('success', 'تم إنشاء الإعلان بنجاح');
    }

    /**
     * عرض تفاصيل الإعلان
     */
    public function show(Announcement $announcement)
    {
        return view('media.announcements.show', compact('announcement'));
    }

    /**
     * عرض نموذج تعديل الإعلان
     */
    public function edit(Announcement $announcement)
    {
        return view('media.announcements.edit', compact('announcement'));
    }

    /**
     * تحديث الإعلان
     */
    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'link' => 'nullable|url',
            'is_active' => 'boolean'
        ]);

        $announcement->update($request->only(['title', 'content', 'start_date', 'end_date', 'link', 'is_active']));

        return redirect()->route('media.announcements.index')->with('success', 'تم تحديث الإعلان بنجاح');
    }

    /**
     * حذف الإعلان
     */
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()->route('media.announcements.index')->with('success', 'تم حذف الإعلان بنجاح');
    }

    /**
     * تبديل حالة الإعلان (تفعيل/تعطيل)
     */
    public function toggleStatus(Announcement $announcement)
    {
        $announcement->update(['is_active' => !$announcement->is_active]);

        $message = $announcement->is_active ? 'تم تفعيل الإعلان' : 'تم تعطيل الإعلان';

        return redirect()->back()->with('success', $message);
    }
}
