<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurveyTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * عرض معرض وقائمة قوالب الاستبيانات
     */
    public function index(Request $request)
    {
        // تأكد من وجود كافة القوالب الرسمية الـ 14 في أي بيئة تشغيل (بما فيها السيرفر الحي Railway)
        if (SurveyTemplate::count() < 14) {
            try {
                (new \Database\Seeders\SurveyTemplateSeeder())->run();
                SurveyTemplate::where('title', 'نموذج تقييم الشركاء (قسم التقييم والمتابعة)')->update(['category' => 'employment']);
                SurveyTemplate::where('title', 'نموذج تقييم التنظيم والتنسيق للفريق الداخلي')->update(['category' => 'events']);
                SurveyTemplate::where('title', 'استبيان آراء الزوار للفعاليات ومعرض التوظيف')->update(['category' => 'events']);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Auto-seeding SurveyTemplateSeeder: ' . $e->getMessage());
            }
        }

        $category = $request->query('category');

        $query = SurveyTemplate::with('creator')->latest();

        if ($category && in_array($category, ['training', 'employment', 'events', 'custom'])) {
            if ($category === 'custom') {
                $query->where('is_system', false);
            } elseif ($category === 'employment') {
                $query->whereIn('category', ['employment', 'partners']);
            } elseif ($category === 'events') {
                $query->whereIn('category', ['events', 'internal', 'visitors']);
            } else {
                $query->where('category', $category);
            }
        }

        $templates = $query->get();

        $stats = [
            'total' => SurveyTemplate::count(),
            'training' => SurveyTemplate::where('category', 'training')->count(),
            'employment' => SurveyTemplate::whereIn('category', ['employment', 'partners'])->count(),
            'events' => SurveyTemplate::whereIn('category', ['events', 'internal', 'visitors'])->count(),
            'custom' => SurveyTemplate::where('is_system', false)->count(),
        ];

        return view('evaluation-followup.surveys.templates.index', compact('templates', 'stats', 'category'));
    }

    /**
     * إرجاع بيانات قالب محدد (يدعم طلبات AJAX والمعاينة)
     */
    public function show(SurveyTemplate $template, Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'template' => [
                    'id' => $template->id,
                    'title' => $template->title,
                    'description' => $template->description,
                    'category' => $template->category,
                    'category_label' => $template->category_label,
                    'category_color' => $template->category_color,
                    'target_audience' => $template->target_audience,
                    'audience_label' => $template->audience_label,
                    'type' => $template->type,
                    'questions_count' => count($template->questions ?? []),
                    'questions' => $template->questions,
                    'is_system' => $template->is_system,
                    'create_url' => route('evaluation-followup.surveys.create', ['template_id' => $template->id]),
                ],
            ]);
        }

        return redirect()->route('evaluation-followup.surveys.create', ['template_id' => $template->id]);
    }

    /**
     * API يرجع القوالب مجمعة حسب التصنيف لملء النوافذ المنبثقة فورياً
     */
    public function apiList()
    {
        $all = SurveyTemplate::all()->map(function ($t) {
            return [
                'id' => $t->id,
                'title' => $t->title,
                'description' => $t->description,
                'category' => $t->category,
                'category_label' => $t->category_label,
                'category_color' => $t->category_color,
                'target_audience' => $t->target_audience,
                'audience_label' => $t->audience_label,
                'type' => $t->type,
                'questions_count' => count($t->questions ?? []),
                'questions' => $t->questions,
                'is_system' => $t->is_system,
            ];
        });

        $grouped = [
            'training' => $all->where('category', 'training')->values(),
            'employment' => $all->filter(fn($t) => in_array($t['category'], ['employment', 'partners']))->values(),
            'events' => $all->filter(fn($t) => in_array($t['category'], ['events', 'internal', 'visitors']))->values(),
            'custom' => $all->where('is_system', false)->values(),
            'all' => $all->values(),
        ];

        return response()->json([
            'success' => true,
            'templates' => $grouped,
        ]);
    }

    /**
     * حفظ استبيان قائم كقالب جديد للاستخدام المستقبلي
     */
    public function saveFromSurvey(Request $request, Survey $survey)
    {
        $request->validate([
            'template_title' => 'required|string|max:255',
            'template_category' => 'required|in:training,employment,events,general',
            'template_description' => 'nullable|string|max:1000',
        ], [
            'template_title.required' => 'يرجى إدخال اسم للقالب الجديد.',
            'template_category.required' => 'يرجى اختيار تصنيف القالب.',
        ]);

        if (empty($survey->questions) || !is_array($survey->questions)) {
            return redirect()->back()->with('error', 'لا يمكن حفظ هذا الاستبيان كقالب لأنه لا يحتوي على أي أسئلة صالحة.');
        }

        $template = SurveyTemplate::create([
            'title' => $request->input('template_title'),
            'description' => $request->input('template_description', $survey->description),
            'category' => $request->input('template_category'),
            'target_audience' => $survey->target_audience ?? 'all',
            'type' => $survey->type,
            'questions' => $survey->questions,
            'is_system' => false,
            'created_by' => Auth::id(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حفظ الاستبيان كقالب بنجاح!',
                'template_id' => $template->id,
            ]);
        }

        return redirect()->back()->with('success', "تم حفظ الاستبيان كقالب جديد بنجاح: «{$template->title}»");
    }

    /**
     * حذف قالب مخصص
     */
    public function destroy(SurveyTemplate $template)
    {
        if ($template->is_system) {
            return redirect()->back()->with('error', 'لا يمكن حذف القوالب الأساسية المعتمدة للمنظومة.');
        }

        $title = $template->title;
        $template->delete();

        return redirect()->back()->with('success', "تم حذف القالب «{$title}» بنجاح.");
    }
}
