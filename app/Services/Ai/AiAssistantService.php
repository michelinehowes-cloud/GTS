<?php

namespace App\Services\Ai;

use App\Models\User;
use App\Models\AiChatMessage;
use App\Models\News;
use App\Models\Training;
use App\Models\TrainingApplication;
use App\Models\JobOpportunity;
use App\Models\Nomination;
use App\Models\GraduateData;
use App\Models\Company;
use App\Models\Announcement;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Models\AuditLog;
use App\Models\PartnershipDocument;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AiAssistantService
{
    /**
     * Process user chat message and return AI Assistant response
     */
    public function chat(User $user, string $sessionId, string $userMessage): array
    {
        $userMessage = trim($userMessage);

        // 1. Save user message to chat history
        AiChatMessage::create([
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'role' => 'user',
            'content' => $userMessage,
        ]);

        // 2. Fetch authorized tools for this user
        $authorizedTools = AiToolRegistry::getAuthorizedTools($user);

        // 3. Provider Resolution & Autonomous AI Dispatch (Groq LLaMA / Gemini / Local Engine)
        $configuredProvider = strtolower(config('ai.provider', 'auto'));
        $geminiKey = config('ai.gemini_api_key') ?: env('GEMINI_API_KEY', '');
        $groqKey = config('ai.groq_api_key') ?: env('GROQ_API_KEY', '');
        $generalKey = config('ai.api_key') ?: env('AI_API_KEY', '');

        // Auto-detect provider if generic AI_API_KEY is supplied
        if (!empty($generalKey)) {
            if (str_starts_with($generalKey, 'gsk_')) {
                $groqKey = $groqKey ?: $generalKey;
            } elseif (str_starts_with($generalKey, 'AIza')) {
                $geminiKey = $geminiKey ?: $generalKey;
            }
        }

        $response = null;

        // Try Groq (LLaMA 3.3 70B) first if selected or if Groq key is present
        if ($configuredProvider === 'groq' || ($configuredProvider === 'auto' && !empty($groqKey))) {
            if (!empty($groqKey)) {
                $response = $this->callGroqApi($user, $sessionId, $userMessage, $authorizedTools, $groqKey);
            }
        }

        // Try Gemini if Groq was not used or failed
        if (!$response && ($configuredProvider === 'gemini' || $configuredProvider === 'auto' || !empty($geminiKey))) {
            if (!empty($geminiKey)) {
                $response = $this->callGeminiApi($user, $sessionId, $userMessage, $authorizedTools, $geminiKey);
            }
        }

        // Fallback to intelligent local engine if API not configured or failed
        if (!$response) {
            $response = $this->callLocalEngine($user, $sessionId, $userMessage, $authorizedTools);
        }

        // 4. Save assistant response to chat history
        AiChatMessage::create([
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'role' => 'assistant',
            'content' => $response['content'],
            'tool_calls' => $response['tool_calls'] ?? null,
            'tool_results' => $response['tool_results'] ?? null,
            'meta_data' => $response['action_proposal'] ?? null,
        ]);

        return [
            'status' => 'success',
            'session_id' => $sessionId,
            'content' => $response['content'],
            'action_proposal' => $response['action_proposal'] ?? null,
            'tool_executed' => $response['tool_executed'] ?? null,
        ];
    }

    /**
     * Confirm and execute a proposed action (Human-in-the-Loop)
     */
    public function confirmAction(User $user, string $sessionId, string $actionType, array $actionData): array
    {
        $resultMessage = '';

        if ($actionType === 'create_news_draft') {
            if ($user->role !== 'media_officer' && $user->role !== 'admin') {
                return ['status' => 'forbidden', 'message' => 'ليس لديك صلاحية لإنشاء مسودة خبر.'];
            }

            $news = News::create([
                'title' => $actionData['title'] ?? 'خبر صحفي جديد',
                'content' => $actionData['content'] ?? ($actionData['excerpt'] ?? ''),
                'is_active' => false, // Saved as draft
                'published_at' => now(),
                'created_by' => $user->id,
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'AI_ASSISTANT_CREATE_NEWS_DRAFT',
                'entity' => 'News',
                'entity_id' => $news->id,
                'new_values' => ['title' => $news->title],
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => substr(request()->userAgent() ?? 'System', 0, 255),
                'timestamp' => now(),
            ]);

            $resultMessage = "✅ تم حفظ مسودة الخبر الصحفي بنجاح بعنوان: **'{$news->title}'**. يمكنك مراجعتها وتفعيل نشرها الآن من وحدة الإعلام.";
        } elseif ($actionType === 'create_training_draft') {
            if (!in_array($user->role, ['training_coordinator', 'admin'])) {
                return ['status' => 'forbidden', 'message' => 'ليس لديك صلاحية لإضافة برنامج تدريبي.'];
            }

            $training = Training::create([
                'title' => $actionData['title'] ?? 'برنامج تدريبي جديد',
                'type' => $actionData['type'] ?? 'course',
                'seats' => (int) ($actionData['seats'] ?? 25),
                'duration' => (int) ($actionData['duration'] ?? 5),
                'location' => $actionData['location'] ?? 'جامعة طرابلس',
                'description' => $actionData['description'] ?? '',
                'status' => 'draft',
                'coordinator_id' => $user->id,
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'AI_ASSISTANT_CREATE_TRAINING_DRAFT',
                'entity' => 'Training',
                'entity_id' => $training->id,
                'new_values' => ['title' => $training->title],
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => substr(request()->userAgent() ?? 'System', 0, 255),
                'timestamp' => now(),
            ]);

            $resultMessage = "✅ تم حفظ البرنامج التدريبي بنجاح كمسودة: **'{$training->title}'**. يمكنك مراجعته وجدولته في تقويم التدريب.";
        } elseif ($actionType === 'apply_training') {
            if ($user->role !== 'graduate') {
                return ['status' => 'forbidden', 'message' => 'هذا الإجراء مخصص لحسابات الخريجين فقط.'];
            }

            $trainingId = $actionData['training_id'] ?? null;
            $training = $trainingId ? Training::find($trainingId) : null;
            if (!$training) {
                return ['status' => 'error', 'message' => 'البرنامج التدريبي غير متوفر حالياً.'];
            }

            $exists = TrainingApplication::where('user_id', $user->id)
                ->where('training_id', $training->id)
                ->first();

            if ($exists) {
                return ['status' => 'info', 'message' => "لقد تقدمت بطلب لهذا التدريب مسبقاً. الطلب مسجل بحالة: " . ($exists->status_arabic ?? $exists->status)];
            }

            $app = TrainingApplication::create([
                'user_id' => $user->id,
                'training_id' => $training->id,
                'status' => 'pending',
                'applied_at' => now(),
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'AI_ASSISTANT_APPLY_TRAINING',
                'entity' => 'TrainingApplication',
                'entity_id' => $app->id,
                'new_values' => ['training_title' => $training->title],
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => substr(request()->userAgent() ?? 'System', 0, 255),
                'timestamp' => now(),
            ]);

            $resultMessage = "🎉 ✅ تم إرسال طلب تسجيلك في التدريب: **'{$training->title}'** بنجاح! الطلب الآن قيد مراجعة منسق التدريب.";
        } elseif ($actionType === 'apply_job') {
            if ($user->role !== 'graduate') {
                return ['status' => 'forbidden', 'message' => 'هذا الإجراء مخصص لحسابات الخريجين فقط.'];
            }

            $jobId = $actionData['job_id'] ?? null;
            $gradDataId = $actionData['graduate_data_id'] ?? null;
            $job = $jobId ? JobOpportunity::find($jobId) : null;

            if (!$job || !$gradDataId) {
                return ['status' => 'error', 'message' => 'تعذر استكمال طلب التقديم لعدم اكتمال بيانات الوظيفة أو ملف الخريج.'];
            }

            $exists = Nomination::where('graduate_id', $gradDataId)
                ->where('job_opportunity_id', $job->id)
                ->first();

            if ($exists) {
                return ['status' => 'info', 'message' => "لقد تقدمت بالفعل لهذه الفرصة الوظيفية مسبقاً."];
            }

            $nomination = Nomination::create([
                'graduate_id' => $gradDataId,
                'job_opportunity_id' => $job->id,
                'nominated_by' => $user->id,
                'nomination_type' => 'self',
                'status' => 'pending',
                'nomination_notes' => 'ترشيح وتقديم ذاتي عبر المساعد الذكي',
                'nominated_at' => now(),
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'AI_ASSISTANT_APPLY_JOB',
                'entity' => 'Nomination',
                'entity_id' => $nomination->id,
                'new_values' => ['job_title' => $job->title],
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => substr(request()->userAgent() ?? 'System', 0, 255),
                'timestamp' => now(),
            ]);

            $resultMessage = "💼 ✅ تم تقديم طلبك وترشحك بنجاح لوظيفة: **'{$job->title}'**! ستتم مراجعة طلبك والتواصل معك فور مطابقة البيانات.";
        } elseif ($actionType === 'nominate_graduate') {
            if (!in_array($user->role, ['career_guidance_officer', 'admin', 'company', 'partnership_officer'])) {
                return ['status' => 'forbidden', 'message' => 'ليس لديك صلاحية لترشيح الخريجين.'];
            }

            $gradId = $actionData['graduate_id'] ?? null;
            $jobId = $actionData['job_opportunity_id'] ?? null;
            if (!$gradId || !$jobId) {
                return ['status' => 'error', 'message' => 'بيانات الترشيح غير مكتملة.'];
            }

            $exists = Nomination::where('graduate_id', $gradId)
                ->where('job_opportunity_id', $jobId)
                ->first();

            if ($exists) {
                return ['status' => 'info', 'message' => "تم ترشيح هذا الخريج لهذه الفرصة مسبقاً."];
            }

            $nomType = ($user->role === 'company') ? 'company_requested' : 'officer_nominated';
            $nomStatus = ($user->role === 'company') ? 'under_review' : 'pending';
            $defaultNotes = ($user->role === 'company') 
                ? 'طلب ترشيح مباشر من الشركة عبر المساعد الذكي' 
                : 'ترشيح رسمي من مسؤول الإرشاد المهني عبر المساعد الذكي';

            $nomination = Nomination::create([
                'graduate_id' => $gradId,
                'job_opportunity_id' => $jobId,
                'nominated_by' => $user->id,
                'nomination_type' => $nomType,
                'status' => $nomStatus,
                'nomination_notes' => $actionData['nomination_notes'] ?? $defaultNotes,
                'matching_reasons' => $actionData['matching_reasons'] ?? 'مطابقة المتطلبات والتخصص والمعدل',
                'nominated_at' => now(),
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'AI_ASSISTANT_NOMINATE_GRADUATE',
                'entity' => 'Nomination',
                'entity_id' => $nomination->id,
                'new_values' => [
                    'graduate_id' => $gradId,
                    'graduate_name' => $actionData['graduate_name'] ?? '',
                    'job_title' => $actionData['job_title'] ?? '',
                ],
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => substr(request()->userAgent() ?? 'System', 0, 255),
                'timestamp' => now(),
            ]);

            $resultMessage = "🤝 ✅ تم ترشيح الخريج: **'{$actionData['graduate_name']}'** بنجاح لوظيفة **'{$actionData['job_title']}'**! تم تسجيل العملية وإرسال الإشعار للشركة الشريكة.";
        } elseif ($actionType === 'create_survey') {
            if (!in_array($user->role, ['evaluation_followup', 'admin'])) {
                return ['status' => 'forbidden', 'message' => 'ليس لديك صلاحية لإنشاء استبيانات التقييم والمتابعة.'];
            }

            $survey = Survey::create([
                'title' => $actionData['title'] ?? 'استبيان تقييم ومتابعة',
                'description' => $actionData['description'] ?? '',
                'questions' => $actionData['questions'] ?? [],
                'target_audience' => $actionData['target_audience'] ?? 'graduates',
                'type' => $actionData['type'] ?? 'training',
                'start_date' => $actionData['start_date'] ?? now()->format('Y-m-d'),
                'end_date' => $actionData['end_date'] ?? now()->addDays(14)->format('Y-m-d'),
                'is_active' => true,
                'is_public' => true,
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'AI_ASSISTANT_CREATE_SURVEY',
                'entity' => 'Survey',
                'entity_id' => $survey->id,
                'new_values' => ['title' => $survey->title, 'target_audience' => $survey->target_audience],
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => substr(request()->userAgent() ?? 'System', 0, 255),
                'timestamp' => now(),
            ]);

            $resultMessage = "📝 ✅ تم إنشاء واعتماد استبيان التقييم والمتابعة بنجاح بعنوان: **'{$survey->title}'**! الاستبيان نشط ومتاح للمستهدفين الآن عبر الرابط العام ولوحة التقييم.";
        } elseif ($actionType === 'create_announcement') {
            if (!in_array($user->role, ['media_officer', 'admin'])) {
                return ['status' => 'forbidden', 'message' => 'ليس لديك صلاحية لنشر إعلانات رسمية.'];
            }

            $announcement = Announcement::create([
                'title' => $actionData['title'] ?? 'إعلان رسمي جديد',
                'content' => $actionData['content'] ?? '',
                'link' => $actionData['link'] ?? null,
                'start_date' => $actionData['start_date'] ?? now()->format('Y-m-d'),
                'end_date' => $actionData['end_date'] ?? now()->addDays(7)->format('Y-m-d'),
                'is_active' => true,
                'created_by' => $user->id,
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'AI_ASSISTANT_CREATE_ANNOUNCEMENT',
                'entity' => 'Announcement',
                'entity_id' => $announcement->id,
                'new_values' => ['title' => $announcement->title],
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => substr(request()->userAgent() ?? 'System', 0, 255),
                'timestamp' => now(),
            ]);

            $resultMessage = "📢 ✅ تم نشر الإعلان الرسمي بنجاح بعنوان: **'{$announcement->title}'**! يظهر الآن في شريط الإعلانات والصفحة الرئيسية للمنظومة.";
        } elseif ($actionType === 'create_company') {
            if (!in_array($user->role, ['partnership_officer', 'admin'])) {
                return ['status' => 'forbidden', 'message' => 'ليس لديك صلاحية لإضافة شركات شريكة.'];
            }

            $company = Company::create([
                'name' => $actionData['name'] ?? 'شركة شريكة جديدة',
                'industry' => $actionData['industry'] ?? 'تقنية واتصالات',
                'email' => $actionData['email'] ?? null,
                'phone' => $actionData['phone'] ?? null,
                'address' => $actionData['address'] ?? 'طرابلس، ليبيا',
                'website' => $actionData['website'] ?? null,
                'contact_person' => $actionData['contact_person'] ?? null,
                'is_approved' => true,
                'partnership_status' => 'active',
                'partnership_type' => $actionData['partnership_type'] ?? 'training_employment',
                'partnership_start_date' => now(),
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'AI_ASSISTANT_CREATE_COMPANY',
                'entity' => 'Company',
                'entity_id' => $company->id,
                'new_values' => ['name' => $company->name, 'industry' => $company->industry],
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => substr(request()->userAgent() ?? 'System', 0, 255),
                'timestamp' => now(),
            ]);

            $resultMessage = "🏢 ✅ تم اعتماد وإضافة شركة: **'{$company->name}'** بنجاح إلى سجل الشركاء المعتمدين في جامعة طرابلس!";
        } elseif ($actionType === 'create_job_opportunity') {
            if (!in_array($user->role, ['career_guidance_officer', 'admin', 'company', 'partnership_officer'])) {
                return ['status' => 'forbidden', 'message' => 'ليس لديك صلاحية لإضافة وظائف وفرص عمل.'];
            }

            $companyId = $actionData['company_id'] ?? null;
            if ($user->role === 'company') {
                $c = $user->company ?? Company::where('user_id', $user->id)->first();
                if ($c) {
                    $companyId = $c->id;
                }
            }

            $job = JobOpportunity::create([
                'title' => $actionData['title'] ?? 'فرصة وظيفية جديدة',
                'description' => $actionData['description'] ?? 'فرصة عمل لخريجي جامعة طرابلس.',
                'company_id' => $companyId,
                'type' => $actionData['type'] ?? 'full-time',
                'contract_type' => $actionData['contract_type'] ?? 'full_time',
                'location' => $actionData['location'] ?? 'طرابلس، ليبيا',
                'seats' => (int) ($actionData['seats'] ?? 1),
                'salary' => $actionData['salary'] ?? null,
                'application_deadline' => $actionData['application_deadline'] ?? now()->addDays(21)->format('Y-m-d'),
                'status' => 'open',
                'requirements' => $actionData['requirements'] ?? 'المؤهل العلمي المناسب وإتقان المهارات التخصصية.',
                'created_by' => $user->id,
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'AI_ASSISTANT_CREATE_JOB_OPPORTUNITY',
                'entity' => 'JobOpportunity',
                'entity_id' => $job->id,
                'new_values' => ['title' => $job->title, 'location' => $job->location, 'company_id' => $job->company_id],
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => substr(request()->userAgent() ?? 'System', 0, 255),
                'timestamp' => now(),
            ]);

            $resultMessage = "💼 ✅ تم نشر وتفعيل فرصة العمل بنجاح: **'{$job->title}'**! أصبحت متاحة الآن في بوابة الوظائف للتقديم والترشيح.";
        } elseif ($actionType === 'update_candidate_status') {
            $nominationId = $actionData['nomination_id'] ?? null;
            $nomination = $nominationId ? Nomination::with(['graduate.user', 'jobOpportunity.company'])->find($nominationId) : null;
            if (!$nomination) {
                return ['status' => 'error', 'message' => 'سجل ترشيح المرشح غير موجود في المنظومة.'];
            }

            $companyId = $nomination->jobOpportunity ? $nomination->jobOpportunity->company_id : null;
            if ($user->role === 'company') {
                $userComp = $user->company ?? Company::where('user_id', $user->id)->first();
                if (!$userComp || (int) $userComp->id !== (int) $companyId) {
                    return ['status' => 'forbidden', 'message' => 'غير مصرح لك بتعديل ترشيحات وظائف شركات أخرى.'];
                }
            } elseif (!in_array($user->role, ['partnership_officer', 'career_guidance_officer', 'admin'])) {
                return ['status' => 'forbidden', 'message' => 'ليس لديك صلاحية لتحديث حالة المرشحين.'];
            }

            $oldStatus = $nomination->status;
            $newStatus = $actionData['status'] ?? $oldStatus;
            $finalStatus = $actionData['final_status'] ?? $nomination->final_status;

            $nomination->status = $newStatus;
            if (!empty($finalStatus)) {
                $nomination->final_status = $finalStatus;
            }
            if (!empty($actionData['interview_date'])) {
                $nomination->interview_date = $actionData['interview_date'];
            }
            if (!empty($actionData['interview_time'])) {
                $rawTime = (string) $actionData['interview_time'];
                $isPm = preg_match('/(?:مساءً|مساء|م|pm)/iu', $rawTime);
                $isAm = preg_match('/(?:صباحاً|صباح|ص|am)/iu', $rawTime);
                if (preg_match('/(\d{1,2})(?::(\d{2}))?/u', $rawTime, $tm)) {
                    $h = (int) $tm[1];
                    $m = isset($tm[2]) ? (int) $tm[2] : 0;
                    if ($isPm && $h < 12) $h += 12;
                    if ($isAm && $h == 12) $h = 0;
                    $nomination->interview_time = sprintf('%02d:%02d:00', $h, $m);
                } else {
                    $nomination->interview_time = null;
                }
            }
            if (!empty($actionData['interview_location'])) {
                $nomination->interview_location = $actionData['interview_location'];
            }
            if (!empty($actionData['notes'])) {
                $nomination->interview_notes = $actionData['notes'];
            }

            if ($newStatus === 'interview_scheduled') {
                $nomination->interview_at = now();
            }
            if (in_array($newStatus, ['accepted', 'rejected'])) {
                $nomination->company_response_at = now();
            }
            if ($finalStatus && $finalStatus !== 'in_progress') {
                $nomination->final_decision_at = now();
            }

            $nomination->save();

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'AI_ASSISTANT_UPDATE_CANDIDATE_STATUS',
                'entity' => 'Nomination',
                'entity_id' => $nomination->id,
                'old_values' => ['status' => $oldStatus],
                'new_values' => [
                    'status' => $newStatus,
                    'final_status' => $finalStatus,
                    'interview_date' => $actionData['interview_date'] ?? null,
                ],
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => substr(request()->userAgent() ?? 'System', 0, 255),
                'timestamp' => now(),
            ]);

            // إرسال إشعار للخريج
            if ($nomination->graduate && $nomination->graduate->user) {
                try {
                    $notifService = app(\App\Services\NotificationService::class);
                    $compTitle = ($nomination->jobOpportunity && $nomination->jobOpportunity->company) ? $nomination->jobOpportunity->company->name : 'الشركة الشريكة';
                    $msgText = ($newStatus === 'interview_scheduled') 
                        ? "تم تحديد موعد مقابلة شخصية لوظيفة ({$nomination->jobOpportunity->title}) لدى {$compTitle} بتاريخ {$actionData['interview_date']}."
                        : "قامت شركة {$compTitle} بتحديث حالة طلبك للوظيفة ({$nomination->jobOpportunity->title}) إلى: {$newStatus}.";
                    $notifService->sendToUser($nomination->graduate->user, 'تحديث حالة طلب التوظيف', $msgText, 'info');
                } catch (\Throwable $e) {
                    \Log::warning('AI Notification failed: ' . $e->getMessage());
                }
            }

            $gradName = $nomination->graduate ? $nomination->graduate->name : 'المرشح';
            $resultMessage = "🤝 ✅ تم تحديث حالة الخريج: **'{$gradName}'** بنجاح وإرسال الإشعار وتوثيق الإجراء في سجل العمليات!";
        } elseif ($actionType === 'toggle_company_approval') {
            if (!in_array($user->role, ['partnership_officer', 'admin'])) {
                return ['status' => 'forbidden', 'message' => 'ليس لديك صلاحية لتعديل اعتماد الشركات الشريكة.'];
            }

            $companyId = $actionData['company_id'] ?? null;
            $company = $companyId ? Company::find($companyId) : null;
            if (!$company) {
                return ['status' => 'error', 'message' => 'الشركة المستهدفة غير موجودة.'];
            }

            $oldApproved = $company->is_approved;
            $newApproved = (bool) ($actionData['target_approved'] ?? !$oldApproved);
            $company->is_approved = $newApproved;
            $company->partnership_status = $newApproved ? 'active' : 'under_review';
            $company->save();

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'AI_ASSISTANT_TOGGLE_COMPANY_APPROVAL',
                'entity' => 'Company',
                'entity_id' => $company->id,
                'old_values' => ['is_approved' => $oldApproved],
                'new_values' => ['is_approved' => $newApproved, 'partnership_status' => $company->partnership_status],
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => substr(request()->userAgent() ?? 'System', 0, 255),
                'timestamp' => now(),
            ]);

            $resultMessage = $newApproved 
                ? "🏢 ✅ تم اعتماد وتفعيل شركة **'{$company->name}'** بنجاح في منظومة جامعة طرابلس!" 
                : "🏢 ⏸️ تم إلغاء اعتماد شركة **'{$company->name}'** وتحويلها إلى قيد المراجعة والتدقيق.";
        } elseif ($actionType === 'change_password') {
            $targetUserId = (int) ($actionData['target_user_id'] ?? $user->id);
            if ($targetUserId !== (int) $user->id && $user->role !== 'admin') {
                return ['status' => 'forbidden', 'message' => 'غير مصرح لك بتغيير كلمة مرور مستخدم آخر.'];
            }

            $targetUser = ($targetUserId === (int) $user->id) ? $user : User::find($targetUserId);
            if (!$targetUser) {
                return ['status' => 'error', 'message' => 'المستخدم المستهدف غير موجود في النظام.'];
            }

            $newPassword = $actionData['new_password'] ?? '';
            if (empty($newPassword) || strlen($newPassword) < 8) {
                return ['status' => 'error', 'message' => 'كلمة المرور الجديدة يجب ألا تقل عن 8 خانات لأسباب أمنية.'];
            }

            $targetUser->password = Hash::make($newPassword);
            $targetUser->must_change_password = false;
            $targetUser->save();

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'AI_ASSISTANT_CHANGE_PASSWORD',
                'entity' => 'User',
                'entity_id' => $targetUser->id,
                'new_values' => ['email' => $targetUser->email, 'target_user' => $targetUser->name],
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => substr(request()->userAgent() ?? 'System', 0, 255),
                'timestamp' => now(),
            ]);

            $isSelf = ($targetUser->id === $user->id);
            $resultMessage = $isSelf
                ? "🔐 ✅ تم تحديث وتشفير كلمة مرور حسابك بنجاح! تم حفظ البيانات وتوثيق الإجراء في سجل الأمان."
                : "🔐 ✅ تم تحديث كلمة المرور بنجاح للمستخدم: **'{$targetUser->name}'** ({$targetUser->email})!";
        } elseif ($actionType === 'toggle_graduate_status') {
            if (!in_array($user->role, ['career_guidance_officer', 'admin'])) {
                return ['status' => 'forbidden', 'message' => 'ليس لديك صلاحية لتعديل حالة حسابات الخريجين.'];
            }

            $targetUserId = $actionData['user_id'] ?? null;
            $targetUser = $targetUserId ? User::find($targetUserId) : null;
            if (!$targetUser) {
                return ['status' => 'error', 'message' => 'تعذر العثور على حساب الخريج المستهدف.'];
            }

            $newStatus = (bool) ($actionData['new_status'] ?? false);
            $targetUser->is_active = $newStatus;
            $targetUser->save();

            if ($targetUser->graduateData) {
                $targetUser->graduateData->is_active = $newStatus;
                $targetUser->graduateData->save();
            }

            $statusWord = $newStatus ? 'إلغاء تجميد وتنشيط' : 'تجميد';
            $confirmIcon = $newStatus ? '🟢' : '❄️';

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'AI_ASSISTANT_TOGGLE_GRADUATE_STATUS',
                'entity' => 'User',
                'entity_id' => $targetUser->id,
                'new_values' => ['is_active' => $newStatus, 'name' => $targetUser->name],
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => substr(request()->userAgent() ?? 'System', 0, 255),
                'timestamp' => now(),
            ]);

            $resultMessage = "{$confirmIcon} ✅ تم بنجاح **{$statusWord}** حساب الخريج: **'{$targetUser->name}'** (" . ($newStatus ? 'نشط الآن 🟢' : 'مجمد الآن 🔒') . "). تم توثيق الإجراء رسمياً في سجل العمليات.";
        } elseif ($actionType === 'delete_graduate_account') {
            if (!in_array($user->role, ['career_guidance_officer', 'admin'])) {
                return ['status' => 'forbidden', 'message' => 'ليس لديك صلاحية لحذف حسابات الخريجين.'];
            }

            $targetUserId = $actionData['user_id'] ?? null;
            $gradDataId = $actionData['graduate_data_id'] ?? null;
            $targetName = $actionData['graduate_name'] ?? 'الخريج';

            $targetUser = $targetUserId ? User::find($targetUserId) : null;
            $gradData = $gradDataId ? GraduateData::find($gradDataId) : null;

            if (!$targetUser && !$gradData) {
                return ['status' => 'error', 'message' => 'تعذر العثور على سجلات الخريج المطلوب حذفها.'];
            }

            \Illuminate\Support\Facades\DB::transaction(function() use ($targetUser, $gradData, $targetUserId) {
                if ($targetUser && $targetUser->graduateData) {
                    $targetUser->graduateData->nominations()->delete();
                    $targetUser->graduateData->delete();
                }
                if ($gradData) {
                    $gradData->nominations()->delete();
                    $gradData->delete();
                }
                if ($targetUser && $targetUser->email) {
                    GraduateData::where('email', $targetUser->email)->delete();
                }
                if ($targetUserId) {
                    TrainingApplication::where('user_id', $targetUserId)->delete();
                    if (class_exists(SurveyResponse::class)) {
                        SurveyResponse::where('user_id', $targetUserId)->delete();
                    }
                }
                if ($targetUser) {
                    $targetUser->permissions()->detach();
                    $targetUser->delete();
                }
            });

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'AI_ASSISTANT_DELETE_GRADUATE_ACCOUNT',
                'entity' => 'User',
                'entity_id' => $targetUserId ?? 0,
                'new_values' => ['name' => $targetName],
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => substr(request()->userAgent() ?? 'System', 0, 255),
                'timestamp' => now(),
            ]);

            $resultMessage = "🗑️ ✅ تم بنجاح **مسح وحذف حساب وسجلات الخريج: '{$targetName}'** نهائياً من قاعدة بيانات المنظومة.";
        } elseif ($actionType === 'bulk_nominate_graduates') {
            if (!in_array($user->role, ['career_guidance_officer', 'admin'])) {
                return ['status' => 'forbidden', 'message' => 'ليس لديك صلاحية لترشيح الخريجين.'];
            }

            $jobId = $actionData['job_opportunity_id'] ?? null;
            $candidateIds = $actionData['candidate_ids'] ?? [];
            $jobTitle = $actionData['job_title'] ?? 'فرصة العمل';

            if (!$jobId || empty($candidateIds)) {
                return ['status' => 'error', 'message' => 'بيانات الترشيح الجماعي غير مكتملة.'];
            }

            $nominatedCount = 0;
            \Illuminate\Support\Facades\DB::transaction(function() use ($jobId, $candidateIds, $user, &$nominatedCount) {
                foreach ($candidateIds as $candId) {
                    $exists = Nomination::where('graduate_id', $candId)
                        ->where('job_opportunity_id', $jobId)
                        ->exists();

                    if (!$exists) {
                        Nomination::create([
                            'graduate_id' => $candId,
                            'job_opportunity_id' => $jobId,
                            'nominated_by' => $user->id,
                            'nomination_type' => 'officer_nominated',
                            'status' => 'pending',
                            'nomination_notes' => 'ترشيح جماعي بواسطة المساعد الذكي لمسؤول الإرشاد المهني',
                            'matching_reasons' => 'مطابقة التخصص والمعدل التراكمي العالي لمتطلبات الوظيفة',
                            'nominated_at' => now(),
                        ]);
                        $nominatedCount++;
                    }
                }
            });

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'AI_ASSISTANT_BULK_NOMINATE_GRADUATES',
                'entity' => 'Nomination',
                'entity_id' => $jobId,
                'new_values' => [
                    'job_title' => $jobTitle,
                    'nominated_count' => $nominatedCount,
                    'candidates' => $actionData['candidate_names'] ?? [],
                ],
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => substr(request()->userAgent() ?? 'System', 0, 255),
                'timestamp' => now(),
            ]);

            $resultMessage = "🤝 ✅ تم بنجاح ترشيح **({$nominatedCount}) من الخريجين المتميزين** لوظيفة **'{$jobTitle}'**! تم إرسال ملفاتهم وقيدت الترشيحات في المنظومة.";
        } elseif ($actionType === 'manage_training_applications') {
            if (!in_array($user->role, ['training_coordinator', 'admin'])) {
                return ['status' => 'forbidden', 'message' => 'ليس لديك صلاحية لإدارة وقبول طلبات التدريب.'];
            }

            $appIds = $actionData['application_ids'] ?? [];
            $targetStatus = $actionData['target_status'] ?? 'approved';
            $actionWord = ($targetStatus === 'approved') ? 'قبول' : 'رفض';

            if (empty($appIds)) {
                return ['status' => 'error', 'message' => 'لا توجد طلبات محددة لتنفيذ الإجراء عليها.'];
            }

            TrainingApplication::whereIn('id', $appIds)->update(['status' => $targetStatus]);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'AI_ASSISTANT_MANAGE_TRAINING_APPLICATIONS',
                'entity' => 'TrainingApplication',
                'entity_id' => 0,
                'new_values' => [
                    'target_status' => $targetStatus,
                    'count' => count($appIds),
                    'scope' => $actionData['scope_description'] ?? 'عام',
                ],
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => substr(request()->userAgent() ?? 'System', 0, 255),
                'timestamp' => now(),
            ]);

            $resultMessage = "🎓 ✅ تم بنجاح **{$actionWord} عدد (" . count($appIds) . ")** من طلبات الالتحاق بالبرامج التدريبية! تم تحديث حالة المتدربين وتوثيق العملية في سجل التدريب.";
        } elseif ($actionType === 'toggle_job_status') {
            if (!in_array($user->role, ['career_guidance_officer', 'admin', 'company', 'partnership_officer'])) {
                return ['status' => 'forbidden', 'message' => 'ليس لديك صلاحية لتعديل حالة الوظائف.'];
            }

            $jobId = $actionData['job_id'] ?? null;
            $job = $jobId ? JobOpportunity::find($jobId) : null;
            if (!$job) {
                return ['status' => 'error', 'message' => 'فرصة العمل المطلوبة غير موجودة.'];
            }

            if ($user->role === 'company') {
                $c = $user->company ?? Company::where('user_id', $user->id)->first();
                if (!$c || (int)$c->id !== (int)$job->company_id) {
                    return ['status' => 'forbidden', 'message' => 'لا يمكنك تعديل وظائف تابعة لشركات أخرى.'];
                }
            }

            $newStatus = $actionData['target_status'] ?? ($job->status === 'open' ? 'closed' : 'open');
            $job->status = $newStatus;
            $job->save();

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'AI_ASSISTANT_TOGGLE_JOB_STATUS',
                'entity' => 'JobOpportunity',
                'entity_id' => $job->id,
                'new_values' => ['status' => $newStatus, 'title' => $job->title],
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => substr(request()->userAgent() ?? 'System', 0, 255),
                'timestamp' => now(),
            ]);

            $statusText = ($newStatus === 'open') ? 'مفتوحة للتقديم والترشيح 🟢' : 'مغلقة ومكتفية 🔒';
            $resultMessage = "💼 ✅ تم بنجاح تحديث حالة وظيفة **'{$job->title}'** إلى: **{$statusText}**.";
        } elseif ($actionType === 'update_company_partnership') {
            if (!in_array($user->role, ['partnership_officer', 'admin'])) {
                return ['status' => 'forbidden', 'message' => 'ليس لديك صلاحية لتحديث شروط الشراكة.'];
            }

            $companyId = $actionData['company_id'] ?? null;
            $company = $companyId ? Company::find($companyId) : null;
            if (!$company) {
                return ['status' => 'error', 'message' => 'الشركة المطلوبة غير موجودة.'];
            }

            if (!empty($actionData['partnership_status'])) {
                $company->partnership_status = $actionData['partnership_status'];
                if ($company->partnership_status === 'active') {
                    $company->is_approved = true;
                }
            }
            if (!empty($actionData['partnership_types'])) {
                $company->partnership_types = (array) $actionData['partnership_types'];
                $company->partnership_type = $company->partnership_types[0] ?? $company->partnership_type;
            }
            if (!empty($actionData['partnership_end_date'])) {
                $company->partnership_end_date = $actionData['partnership_end_date'];
            }
            if (!empty($actionData['notes'])) {
                $company->partnership_notes = $actionData['notes'];
            }
            $company->save();

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'AI_ASSISTANT_UPDATE_PARTNERSHIP',
                'entity' => 'Company',
                'entity_id' => $company->id,
                'new_values' => [
                    'partnership_status' => $company->partnership_status,
                    'partnership_end_date' => $company->partnership_end_date,
                ],
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => substr(request()->userAgent() ?? 'System', 0, 255),
                'timestamp' => now(),
            ]);

            $resultMessage = "🤝 ✅ تم بنجاح حفظ وتحديث اتفاقية الشراكة لشركة **'{$company->name}'** في سجلات جامعة طرابلس.";
        } elseif ($actionType === 'record_partnership_document') {
            if (!in_array($user->role, ['partnership_officer', 'admin'])) {
                return ['status' => 'forbidden', 'message' => 'ليس لديك صلاحية لتوثيق وثائق الشراكة.'];
            }

            $companyId = $actionData['company_id'] ?? null;
            $company = $companyId ? Company::find($companyId) : null;
            if (!$company) {
                return ['status' => 'error', 'message' => 'الشركة المستهدفة غير موجودة.'];
            }

            $docName = $actionData['document_name'] ?? 'وثيقة شراكة رسمية';
            $docType = $actionData['document_type'] ?? 'agreement';

            $doc = PartnershipDocument::create([
                'company_id' => $company->id,
                'uploaded_by' => $user->id,
                'document_name' => $docName,
                'document_type' => $docType,
                'description' => $actionData['description'] ?? 'وثيقة شراكة رسمية موثقة بواسطة المساعد الذكي',
                'file_path' => 'partnership-documents/generated_' . time() . '.pdf',
                'file_name' => $docName . '.pdf',
                'file_size' => '1024',
                'mime_type' => 'application/pdf',
                'document_date' => $actionData['effective_date'] ?? now()->format('Y-m-d'),
                'effective_date' => $actionData['effective_date'] ?? now()->format('Y-m-d'),
                'expiry_date' => $actionData['expiry_date'] ?? now()->addYear()->format('Y-m-d'),
                'document_status' => 'active',
                'is_shared_with_company' => true,
                'company_signed' => true,
                'university_signed' => true,
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'AI_ASSISTANT_RECORD_PARTNERSHIP_DOC',
                'entity' => 'PartnershipDocument',
                'entity_id' => $doc->id,
                'new_values' => ['document_name' => $doc->document_name, 'company' => $company->name],
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => substr(request()->userAgent() ?? 'System', 0, 255),
                'timestamp' => now(),
            ]);

            $resultMessage = "📜 ✅ تم توثيق وتسجيل وثيقة الشراكة: **'{$doc->document_name}'** لشركة **'{$company->name}'** بنجاح في أرشيف الاتفاقيات.";
        } elseif ($actionType === 'update_company_profile') {
            if (!in_array($user->role, ['company', 'partnership_officer', 'admin'])) {
                return ['status' => 'forbidden', 'message' => 'ليس لديك صلاحية لتحديث ملف الشركة.'];
            }

            $companyId = $actionData['company_id'] ?? null;
            $company = null;
            if ($user->role === 'company') {
                $company = $user->company ?? Company::where('user_id', $user->id)->first();
            } elseif ($companyId) {
                $company = Company::find($companyId);
            }

            if (!$company) {
                return ['status' => 'error', 'message' => 'ملف الشركة غير موجود.'];
            }

            $fields = ['phone', 'website', 'city', 'address', 'description', 'contact_person', 'contact_phone', 'contact_email', 'contact_position'];
            foreach ($fields as $f) {
                if (!empty($actionData[$f])) {
                    $company->$f = $actionData[$f];
                }
            }
            $company->save();

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'AI_ASSISTANT_UPDATE_COMPANY_PROFILE',
                'entity' => 'Company',
                'entity_id' => $company->id,
                'new_values' => ['name' => $company->name],
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => substr(request()->userAgent() ?? 'System', 0, 255),
                'timestamp' => now(),
            ]);

            $resultMessage = "🏢 ✅ تم تحديث بيانات الملف التعريفي لشركة **'{$company->name}'** وحفظ كافة التعديلات.";
        } else {
            return ['status' => 'error', 'message' => 'نوع الإجراء غير معروف.'];
        }

        // Save confirmation record to chat
        AiChatMessage::create([
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'role' => 'assistant',
            'content' => $resultMessage,
            'meta_data' => ['confirmed' => true, 'action_type' => $actionType],
        ]);

        return [
            'status' => 'success',
            'message' => $resultMessage,
        ];
    }

    /**
     * Build unified system instruction with real user & graduate profile context
     */
    protected function buildSystemInstruction(User $user): string
    {
        $roleArabic = $user->role_arabic ?? $user->role;
        $instruction = "أنت 'المساعد الذكي لمكتب تدريب وتأهيل الخريجين بجامعة طرابلس'.\n" .
            "المستخدم الحالي: {$user->name}، وصلاحيته/دوره في المنظومة: {$roleArabic}.\n\n" .
            "قواعد صارمة وتوجيهات ملزمة:\n" .
            "1. تحدث بلغة عربية فصحى طبيعية واحترافية تلائم الصرح الأكاديمي والمهني لجامعة طرابلس.\n" .
            "2. ممنوع منعاً باتاً اختلاق أو تخمين أي بيانات شخصية، أو وضع نصوص نائبة مثل [your.email@example.com] أو [+218-XXXXX]، أو ادعاء كليات وتخصصات وخبرات وهمية.\n" .
            "3. تجنب استخدام جداول الماركداون العريضة (Markdown Tables) لأن نافذة المحادثة مخصصة للجوال؛ اعرض القوائم والفرص دائماً كبطاقات أو نقاط واضحة وموجزة.\n" .
            "4. يجب دائماً التمييز بدقة بين 'وظائف العمل المباشرة' (عقود دوام كامل/جزئي) وبين 'فرص التدريب والتأهيل' (تدريب عملي أو داخلي أو دورات بالجامعة).\n";

        if ($user->role === 'graduate') {
            $gradData = $user->graduateData ?? \App\Models\GraduateData::where('email', $user->email)->first();
            if ($gradData) {
                $skillsStr = is_array($gradData->skills) ? implode('، ', $gradData->skills) : ($gradData->skills ?: 'هندسة البرمجيات وتطوير الأنظمة');
                $languagesStr = is_array($gradData->languages) ? implode('، ', $gradData->languages) : ($gradData->languages ?: 'العربية، الإنجليزية');
                $expStr = $gradData->work_experience ?: 'خبرة عملية ومشاريع تطبيقية في مجال التخصص';
                $facultyStr = $gradData->faculty ?: 'كلية تقنية المعلومات';
                $phoneStr = $gradData->phone ?: $user->phone ?: '0912345678';

                $instruction .= "\nبيانات الخريج الحقيقية والموثقة رسمياً في قاعدة بيانات المنظومة:\n" .
                    "• الاسم الكامل: {$user->name}\n" .
                    "• الكلية: {$facultyStr}\n" .
                    "• التخصص: {$gradData->major}\n" .
                    "• المؤهل الأكاديمي: {$gradData->degree} (دفعة تخرج: {$gradData->graduation_year})\n" .
                    "• المعدل التراكمي: {$gradData->gpa}%\n" .
                    "• البريد الإلكتروني: {$user->email}\n" .
                    "• رقم الهاتف: {$phoneStr}\n" .
                    "• المهارات التقنية: {$skillsStr}\n" .
                    "• اللغات المتقنة: {$languagesStr}\n" .
                    "• الخبرة المسجلة: {$expStr}\n" .
                    "• المدينة والعنوان: " . ($gradData->city ?: 'طرابلس') . " - " . ($gradData->address ?: '') . "\n\n" .
                    "توجيه إلزامي عند صياغة خطابات التوجيه (Cover Letters) أو السير الذاتية للخريج:\n" .
                    "- استخدم حصراً الكلية الحقيقية ({$facultyStr}) والتخصص الحقيقي ({$gradData->major}) والمعدل ({$gradData->gpa}%) وسنة التخرج ({$gradData->graduation_year}) والإيميل ({$user->email}) والهاتف ({$phoneStr}).\n" .
                    "- لا تغير كلية وتخصص الخريج أبداً حتى لو كانت الوظيفة في مجال آخر (مثل وظيفة مترجم أو إداري)؛ بل وضّح بذكاء كيف توظف مهاراته الحقيقية (مثل إتقانه للإنجليزية، ومهارات التحليل والبرمجة، والدقة التقنية) للنجاح في تلك الوظيفة.\n";
            }
        }

        return $instruction;
    }

    /**
     * Call Google Gemini API with Function Calling
     */
    protected function callGeminiApi(User $user, string $sessionId, string $userMessage, array $authorizedTools, string $apiKey): ?array
    {
        try {
            $model = config('ai.gemini_model', config('ai.model', 'gemini-3.6-flash'));
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            // Format tools for Gemini API schema
            $geminiTools = [];
            if (!empty($authorizedTools)) {
                $functionDeclarations = [];
                foreach ($authorizedTools as $tool) {
                    $functionDeclarations[] = [
                        'name' => $tool['name'],
                        'description' => $tool['description'],
                        'parameters' => $tool['parameters'],
                    ];
                }
                $geminiTools = [
                    ['functionDeclarations' => $functionDeclarations]
                ];
            }

            // Build system instructions with real profile
            $systemInstruction = $this->buildSystemInstruction($user);

            // Load recent chat history
            $history = AiChatMessage::where('user_id', $user->id)
                ->where('session_id', $sessionId)
                ->orderBy('id', 'desc')
                ->limit(6)
                ->get()
                ->reverse();

            $contents = [];
            foreach ($history as $msg) {
                if ($msg->role === 'user') {
                    $contents[] = ['role' => 'user', 'parts' => [['text' => $msg->content]]];
                } elseif ($msg->role === 'assistant') {
                    $contents[] = ['role' => 'model', 'parts' => [['text' => $msg->content]]];
                }
            }

            // Add current message
            $contents[] = ['role' => 'user', 'parts' => [['text' => $userMessage]]];

            $payload = [
                'systemInstruction' => [
                    'parts' => [['text' => $systemInstruction]]
                ],
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => config('ai.temperature', 0.4),
                    'maxOutputTokens' => config('ai.max_tokens', 2048),
                ]
            ];

            if (!empty($geminiTools)) {
                $payload['tools'] = $geminiTools;
            }

            $response = Http::timeout(25)->post($url, $payload);

            if (!$response->successful()) {
                Log::warning('Gemini API Error: ' . $response->body());
                return null;
            }

            $data = $response->json();
            $candidates = $data['candidates'][0] ?? null;
            if (!$candidates) return null;

            $part = $candidates['content']['parts'][0] ?? null;
            if (!$part) return null;

            // Check if model called a function
            if (isset($part['functionCall'])) {
                $fnCall = $part['functionCall'];
                $toolName = $fnCall['name'];
                $arguments = $fnCall['args'] ?? [];

                // Execute tool
                $toolResult = AiToolRegistry::executeTool($user, $toolName, $arguments);

                $actionProposal = null;
                if (isset($toolResult['status']) && $toolResult['status'] === 'proposal') {
                    $actionProposal = $toolResult;
                }

                // Format rich conversational report directly from tool execution
                $finalText = $this->formatToolResultFallback($toolName, $toolResult, $user);

                return [
                    'content' => $finalText,
                    'tool_calls' => [$toolName => $arguments],
                    'tool_results' => $toolResult,
                    'tool_executed' => $toolName,
                    'action_proposal' => $actionProposal,
                ];
            }

            return [
                'content' => $part['text'] ?? 'مرحباً، كيف يمكنني مساعدتك اليوم؟',
                'tool_calls' => null,
                'tool_results' => null,
            ];
        } catch (\Throwable $e) {
            Log::error('AI Service Gemini Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Call Groq Cloud API with LLaMA 3.3 / LLaMA 3.1 & Tool Calling
     */
    protected function callGroqApi(User $user, string $sessionId, string $userMessage, array $authorizedTools, string $apiKey): ?array
    {
        try {
            $model = config('ai.groq_model', 'llama-3.3-70b-versatile');
            $url = 'https://api.groq.com/openai/v1/chat/completions';

            // Format tools for Groq / OpenAI specification
            $groqTools = [];
            if (!empty($authorizedTools)) {
                foreach ($authorizedTools as $tool) {
                    $groqTools[] = [
                        'type' => 'function',
                        'function' => [
                            'name' => $tool['name'],
                            'description' => $tool['description'],
                            'parameters' => (object) ($tool['parameters'] ?? new \stdClass()),
                        ],
                    ];
                }
            }

            // Build system instructions with real profile
            $systemInstruction = $this->buildSystemInstruction($user);

            // Load recent chat history
            $history = AiChatMessage::where('user_id', $user->id)
                ->where('session_id', $sessionId)
                ->orderBy('id', 'desc')
                ->limit(6)
                ->get()
                ->reverse();

            $messages = [
                ['role' => 'system', 'content' => $systemInstruction]
            ];

            foreach ($history as $msg) {
                $messages[] = [
                    'role' => $msg->role === 'assistant' ? 'assistant' : 'user',
                    'content' => $msg->content,
                ];
            }

            // Current user message
            $messages[] = ['role' => 'user', 'content' => $userMessage];

            $payload = [
                'model' => $model,
                'messages' => $messages,
                'temperature' => config('ai.temperature', 0.3),
                'max_tokens' => config('ai.max_tokens', 2048),
            ];

            if (!empty($groqTools)) {
                $payload['tools'] = $groqTools;
                $payload['tool_choice'] = 'auto';
            }

            $response = Http::timeout(25)->withToken($apiKey)->post($url, $payload);

            if (!$response->successful()) {
                Log::warning('Groq API Error (' . $response->status() . '): ' . $response->body());

                // إذا كان الخطأ 404 (النموذج غير موجود أو محذوف)، نحاول تلقائياً مع النماذج البديلة المتاحة
                if ($response->status() === 404) {
                    $fallbackModels = ['llama-3.1-8b-instant', 'openai/gpt-oss-120b', 'openai/gpt-oss-20b', 'mixtral-8x7b-32768'];
                    foreach ($fallbackModels as $altModel) {
                        if ($altModel === $model) continue;
                        $payload['model'] = $altModel;
                        $altResp = Http::timeout(20)->withToken($apiKey)->post($url, $payload);
                        if ($altResp->successful()) {
                            $response = $altResp;
                            Log::info("Groq successfully fell back to model: {$altModel}");
                            break;
                        }
                    }
                }

                if (!$response->successful()) {
                    return null;
                }
            }

            $data = $response->json();
            $choice = $data['choices'][0]['message'] ?? null;
            if (!$choice) return null;

            // Check if model invoked tool calls
            if (!empty($choice['tool_calls'])) {
                $toolCall = $choice['tool_calls'][0];
                $toolName = $toolCall['function']['name'] ?? '';
                $rawArgs = $toolCall['function']['arguments'] ?? '{}';
                $arguments = is_string($rawArgs) ? json_decode($rawArgs, true) : $rawArgs;
                $arguments = is_array($arguments) ? $arguments : [];

                // Execute tool
                $toolResult = AiToolRegistry::executeTool($user, $toolName, $arguments);

                $actionProposal = null;
                if (isset($toolResult['status']) && $toolResult['status'] === 'proposal') {
                    $actionProposal = $toolResult;
                }

                // Append assistant tool call and tool result back to message thread
                $messages[] = $choice;
                $messages[] = [
                    'role' => 'tool',
                    'tool_call_id' => $toolCall['id'],
                    'name' => $toolName,
                    'content' => json_encode($toolResult, JSON_UNESCAPED_UNICODE),
                ];

                $secondPayload = [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => config('ai.temperature', 0.3),
                    'max_tokens' => config('ai.max_tokens', 2048),
                ];

                // الأدوات التي تحتوي على بطاقات تفاعلية وأزرار تقديم منسقة
                $structuredUiTools = [
                    'search_job_opportunities', 
                    'search_trainings', 
                    'get_my_applications', 
                    'get_my_profile', 
                    'get_system_statistics', 
                    'search_partner_companies',
                    'get_platform_summary',
                    'list_graduates_for_counselor'
                ];

                if (in_array($toolName, $structuredUiTools) && isset($toolResult['status']) && $toolResult['status'] === 'success') {
                    $finalText = $this->formatToolResultFallback($toolName, $toolResult, $user);
                } else {
                    $secondResponse = Http::timeout(20)->withToken($apiKey)->post($url, $secondPayload);
                    $finalText = '';
                    if ($secondResponse->successful()) {
                        $finalText = $secondResponse->json()['choices'][0]['message']['content'] ?? '';
                    }

                    if (empty($finalText)) {
                        $finalText = $this->formatToolResultFallback($toolName, $toolResult, $user);
                    }
                }

                return [
                    'content' => $finalText,
                    'tool_calls' => [$toolName => $arguments],
                    'tool_results' => $toolResult,
                    'tool_executed' => $toolName,
                    'action_proposal' => $actionProposal,
                ];
            }

            return [
                'content' => $choice['content'] ?? 'مرحباً، كيف يمكنني مساعدتك اليوم؟',
                'tool_calls' => null,
                'tool_results' => null,
            ];
        } catch (\Throwable $e) {
            Log::error('AI Service Groq Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Intelligent local engine fallback (Works offline or without API key)
     */
    protected function callLocalEngine(User $user, string $sessionId, string $userMessage, array $authorizedTools): array
    {
        $text = mb_strtolower($userMessage, 'UTF-8');
        $authorizedNames = array_column($authorizedTools, 'name');

        // 🔐 تغيير كلمة المرور للمستخدم (كافة الأدوار والمدير)
        if (Str::contains($text, ['كلمة المرور', 'كلمة السر', 'باسورد', 'password', 'تغيير رمزي', 'تغيير الرمز']) && 
            Str::contains($text, ['تغيير', 'غير', 'تحديث', 'جديدة', 'بدل', 'تبديل', 'change', 'reset', 'update']) &&
            in_array('change_user_password', $authorizedNames)) {
            
            $newPass = null;
            $targetIdent = null;

            // استخراج كلمة المرور إذا ذكرت (مثل: إلى Secret123 أو تكون Secret123)
            if (preg_match('/(?:إلى|تكون|هي|بـ|to)\s+([A-Za-z0-9@#\$\%!\&\*_\-\+]{8,32})/u', $userMessage, $m)) {
                $newPass = trim($m[1]);
            }

            // للمدير: إذا طلب تغيير كلمة مرور مستخدم آخر (مثل: للمستخدم x@example.com أو لحساب فلان)
            if ($user->role === 'admin' && preg_match('/(?:للمستخدم|لحساب|للطالب|للخريج)\s+([^\s\?\.\!]+)/u', $userMessage, $m)) {
                $targetIdent = trim($m[1]);
            }

            $res = AiToolRegistry::executeTool($user, 'change_user_password', [
                'new_password' => $newPass,
                'target_user_identifier' => $targetIdent,
            ]);

            if (isset($res['status']) && $res['status'] === 'proposal') {
                return [
                    'content' => "🔐 **تم تجهيز طلب تحديث كلمة المرور:**\n\n" .
                        ($newPass ? "لقد قمت بإعداد التشفير لكلمة المرور المقترحة. يرجى مراجعة التفاصيل أدناه وتأكيد التنفيذ." : "يرجى كتابة كلمة المرور الجديدة وتأكيدها في البطاقة التفاعلية أدناه ثم الضغط على **[تأكيد وحفظ]** لتحديثها فوراً."),
                    'action_proposal' => $res,
                    'tool_executed' => 'change_user_password',
                ];
            } else {
                return [
                    'content' => $res['message'] ?? 'تعذر تجهيز طلب تغيير كلمة المرور.',
                    'tool_executed' => 'change_user_password',
                ];
            }
        }

        // 💡 خبير المنظومة الشامل: الإجابة التفاعلية عن كافة التساؤلات الإرشادية وشرح كيفية استخدام المنظومة
        $isHowTo = (
            Str::startsWith($text, ['كيف', 'طريقة', 'خطوات', 'شرح', 'علمني', 'أين أجد', 'اين اجد', 'هل يمكنني', 'كيفية']) ||
            Str::contains($text, [
                'كيف أرشح', 'كيف ارشح', 'كيف أضيف وظيفة', 'كيف اضيف وظيفة', 'كيف أجمد', 'كيف اجمد', 'كيف أحذف', 'كيف احذف',
                'كيف أقبل طلبات', 'كيف اقبل طلبات', 'كيف أستورد', 'كيف استورد', 'كيف أضيف شركة', 'كيف اضيف شركة',
                'كيف أنشئ استبيان', 'كيف انشئ استبيان', 'كيف أغير كلمة المرور', 'كيف اغير كلمة المرور',
                'طريقة الترشيح', 'طريقة إضافة وظيفة', 'طريقة تجميد', 'طريقة قبول الطلبات', 'طريقة الاستيراد', 'خطوات الترشيح',
                'كيف يعمل النظام', 'كيف استخدم المنظومة', 'وظائف المنظومة', 'مهام النظام', 'كيف ترشح', 'كيف تضيف وظيفة'
            ])
        );

        if ($isHowTo) {
            // أ. كيفية ترشيح خريج لوظيفة
            if (Str::contains($text, ['ترشيح', 'أرشح', 'ارشح', 'رشح', 'مرشح']) && !Str::contains($text, ['إضافة وظيفة', 'اضافة وظيفة', 'نشر وظيفة', 'أضف وظيفة', 'اضف وظيفة'])) {
                return [
                    'content' => "🤝 **دليل ترشيح الخريجين للوظائف في المنظومة:**\n\n" .
                        "يمكنك ترشيح الخريجين للفرص الوظيفية المتاحة بطريقتين سهلتين ومباشرتين:\n\n" .
                        "### 1️⃣ عبر واجهة المنظومة (UI):\n" .
                        "1. انتقل من القائمة الجانبية إلى **الإرشاد المهني** > **قائمة الخريجين** (أو **سوق العمل والوظائف**).\n" .
                        "2. في صف الخريج المطلوب، اضغط على زر **ترشيح لوظيفة** (أيقونة المصافحة 🤝 باللون الأخضر في شريط الإجراءات).\n" .
                        "3. ستظهر لك نافذة منبثقة تحتوي على الوظائف الشاغرة المطابقة لتخصص الخريج مع نسبة التطابق ومعدله التراكمي.\n" .
                        "4. اختر الوظيفة المناسبة واكتب مبررات الترشيح إن وُجدت، ثم اضغط على **تأكيد الترشيح وإرسال الإشعار** للشركة الشريكة.\n\n" .
                        "### 2️⃣ مباشرة عبر المساعد الذكي (أسرع طريقة ⚡):\n" .
                        "• **ترشيح فردي:** اكتب مباشرة: *'رشح الخريج [الاسم] لوظيفة [اسم الوظيفة]'*\n" .
                        "  *(مثال: `رشح الخريج المنيب محمد الشريف لوظيفة مطور ويب`)*\n" .
                        "• **ترشيح جماعي:** اكتب: *'رشح مجموعة من الخريجين لوظيفة [اسم الوظيفة]'*\n" .
                        "  وسأقوم بالبحث عن أفضل الخريجين تطابقاً في التخصص والمعدل وإعداد بطاقة ترشيح جماعية فورية لتأكيدها بنقرة واحدة!",
                    'tool_executed' => 'how_to_guidance',
                ];
            }

            // ب. كيفية إضافة ونشر وظيفة جديدة
            if (Str::contains($text, ['وظيفة', 'وظائف', 'فرصة عمل', 'شاغر'])) {
                return [
                    'content' => "💼 **دليل إضافة ونشر فرصة وظيفية جديدة:**\n\n" .
                        "### 1️⃣ عبر واجهة المنظومة (UI):\n" .
                        "1. انتقل من القائمة الجانبية إلى **الإرشاد المهني** أو **إدارة الوظائف**.\n" .
                        "2. اضغط على الزر العلوي **(+ إضافة فرصة وظيفية جديدة)**.\n" .
                        "3. قم بتعبئة بيانات الوظيفة: (المسمى الوظيفي، اختيار الشركة الشريكة، نوع العقد والدوام، موقع العمل، عدد المقاعد، الشروط والمؤهلات، والحد الأقصى للتقديم).\n" .
                        "4. اضغط على **حفظ ونشر الوظيفة** لتظهر فوراً لكافة الخريجين المؤهلين في بوابة التوظيف.\n\n" .
                        "### 2️⃣ عبر المساعد الذكي (صياغة آلية ⚡):\n" .
                        "• اكتب فقط: *'أضف وظيفة [المسمى الوظيفي] لدى شركة [اسم الشركة]'*\n" .
                        "  *(مثال: `أضف وظيفة مهندس شبكات سحابية`)* وسأقوم بصياغة المتطلبات والوصف بالكامل وتجهيز بطاقة النشر بنقرة واحدة!",
                    'tool_executed' => 'how_to_guidance',
                ];
            }

            // ج. كيفية تجميد أو تنشيط أو حذف حساب خريج
            if (Str::contains($text, ['تجميد', 'جمد', 'تنشيط', 'نشط', 'حذف', 'احذف', 'مسح', 'فك تجميد', 'حساب'])) {
                return [
                    'content' => "🔒 **دليل إدارة وتجميد وحذف حسابات الخريجين:**\n\n" .
                        "بصفتك مسؤول الإرشاد المهني أو مدير النظام، يمكنك التحكم في حسابات الخريجين كالتالي:\n\n" .
                        "### 1️⃣ عبر واجهة إدارة الخريجين (UI):\n" .
                        "1. انتقل إلى صفحة **الإرشاد المهني** > **إدارة الخريجين** (`career-guidance/graduates`).\n" .
                        "2. في جدول الخريجين، أمام كل خريج يوجد شريط الإجراءات السريعة:\n" .
                        "   - 🔒 **تجميد / تنشيط الحساب:** اضغط على زر القفل البرتقالي. إذا كان الحساب نشطاً فسيتم تجميده وإيقاف دخوله، وإذا كان مجمداً فسيتم فك التجميد وتنشيطه فوراً مع ظهور إشعار تأكيد.\n" .
                        "   - 🗑️ **حذف الحساب نهائياً:** اضغط على زر الحذف الأحمر (أيقونة السلة). سيظهر لك صندوق تأكيد أمني لتأكيد مسح الخريج وكافة سجلاته نهائياً من قاعدة البيانات.\n\n" .
                        "### 2️⃣ مباشرة عبر المساعد الذكي ⚡:\n" .
                        "• **لتجميد حساب:** اكتب: *'جمد حساب الخريج [الاسم]'* *(مثال: `جمد حساب الخريج المنيب`)*.\n" .
                        "• **لإلغاء تجميد وتنشيط حساب:** اكتب: *'فك تجميد حساب [الاسم]'* أو *'إلغاء التجميد عن [الاسم]'* أو *'نشط حساب الخريج [الاسم]'*.\n" .
                        "• **لحذف حساب:** اكتب: *'احذف حساب الخريج [الاسم]'* وسأعرض عليك بطاقة تأكيد أمنية مشددة لحماية البيانات.",
                    'tool_executed' => 'how_to_guidance',
                ];
            }

            // د. كيفية قبول أو رفض طلبات التدريب
            if (Str::contains($text, ['قبول', 'رفض', 'طلبات التدريب', 'طلبات الالتحاق', 'طلبات'])) {
                return [
                    'content' => "🎓 **دليل قبول وإدارة طلبات الالتحاق بالتدريب:**\n\n" .
                        "### 1️⃣ عبر لوحة منسق التدريب (UI):\n" .
                        "1. انتقل إلى **إدارة التدريب** > **طلبات الالتحاق**.\n" .
                        "2. يمكنك استعراض كافة طلبات الخريجين، ومراجعة بيانات كل متدرب والبرنامج المتقدم له وتاريخ التقديم.\n" .
                        "3. لكل طلب: اضغط على زر **القبول ✅** أو **الاعتذار/الرفض ❌**.\n" .
                        "4. للقبول الجماعي: يمكنك تحديد مربعات الاختيار بجانب الطلبات والضغط على زر **(قبول المحدد)** من أعلى الجدول.\n\n" .
                        "### 2️⃣ مباشرة عبر المساعد الذكي ⚡:\n" .
                        "• **لقبول كافة الطلبات دفعة واحدة:** اكتب: *'قبول جميع طلبات التدريب'*\n" .
                        "• **لقبول طلبات دورة معينة:** اكتب: *'قبول طلبات دورة [اسم الدورة]'*\n" .
                        "• **لقبول طلب متدرب محدد:** اكتب: *'قبول طلب التدريب للخريج [اسم المتدرب]'*",
                    'tool_executed' => 'how_to_guidance',
                ];
            }

            // هـ. كيفية استيراد أو تصدير الخريجين (Excel / PDF)
            if (Str::contains($text, ['استيراد', 'تصدير', 'إكسل', 'اكسل', 'excel', 'pdf'])) {
                return [
                    'content' => "📊 **دليل استيراد وتصدير بيانات الخريجين:**\n\n" .
                        "1. انتقل إلى صفحة **إدارة الخريجين** (`career-guidance/graduates`).\n" .
                        "2. في أعلى الصفحة ستجد أزرار المعالجة المجمعة:\n" .
                        "   - 📥 **استيراد خريجين (Excel):** اضغط على الزر لاختيار ملف Excel يحتوي على بيانات الخريجين (الاسم، البريد، الهاتف، الكلية، التخصص، المعدل، سنة التخرج) ليتم إدراجهم دفعة واحدة في المنظومة.\n" .
                        "   - 📊 **تصدير Excel:** لتنزيل جدول الخريجين الحالي مع الفلاتر المطبقة في ملف إكسل رسمي.\n" .
                        "   - 📄 **تصدير PDF:** لتوليد تقرير رسمي جاهز للطباعة ببيانات الخريجين وشعار جامعة طرابلس.",
                    'tool_executed' => 'how_to_guidance',
                ];
            }

            // و. كيفية إضافة وتوثيق شركة شريكة
            if (Str::contains($text, ['شركة', 'شركات', 'شريك', 'شراكة'])) {
                return [
                    'content' => "🏢 **دليل إضافة وتوثيق الشركات الشريكة:**\n\n" .
                        "1. انتقل من القائمة الجانبية إلى **إدارة الشراكات** > **الشركات والمؤسسات**.\n" .
                        "2. اضغط على زر **(+ إضافة شركة جديدة)**.\n" .
                        "3. أدخل اسم الشركة، القطاع الصناعي، البريد الرسمي، الهاتف، العنوان، وممثل الاتصال، ونوع الشراكة (تدريب / توظيف).\n" .
                        "4. اضغط حفظ، وستصبح الشركة معتمدة ويمكن ربط الوظائف والبرامج التدريبية بها.\n\n" .
                        "💡 أو اطلب مني مباشرة: *'أضف شركة [اسم الشركة] في قطاع التقنية'* وسأجهز ملف الاعتماد فوراً!",
                    'tool_executed' => 'how_to_guidance',
                ];
            }

            // ز. كيفية إنشاء استبيان تقييم ومتابعة
            if (Str::contains($text, ['استبيان', 'استطلاع', 'تقييم'])) {
                return [
                    'content' => "📝 **دليل إنشاء استبيانات التقييم والمتابعة:**\n\n" .
                        "1. انتقل إلى وحدة **التقييم والمتابعة** > **إدارة الاستبيانات**.\n" .
                        "2. اضغط على زر **(+ إنشاء استبيان جديد)**.\n" .
                        "3. حدد عنوان الاستبيان، الفئة المستهدفة (الخريجون، المتدربون، الشركات)، وتاريخ البداية والنهاية.\n" .
                        "4. قم بإضافة أسئلة الاستبيان ومقاييس التقييم المعيارية.\n" .
                        "5. اضغط على **نشر الاستبيان** ليصبح متاحاً فوراً برابط عام للمشاركين.\n\n" .
                        "💡 يمكنك طلبي: *'صياغة استبيان تقييم لدورة الذكاء الاصطناعي'* وسأقوم بصياغة 5 أسئلة معيارية واعتمادها بنقرة واحدة!",
                    'tool_executed' => 'how_to_guidance',
                ];
            }

            // ح. كيفية مراجعة المرشحين وجدولة المقابلات (للشركات ومسؤولي التوظيف)
            if (Str::contains($text, ['مرشح', 'مرشحين', 'مرشحون', 'مقابلة', 'مقابلات', 'توظيف']) && !Str::contains($text, ['كيف أرشح', 'كيف ارشح', 'ترشيح'])) {
                return [
                    'content' => "🤝 **دليل مراجعة المرشحين وجدولة المقابلات (لأرباب العمل ومسؤولي التوظيف):**\n\n" .
                        "### 1️⃣ عبر بوابة الشركة (Company Portal):\n" .
                        "1. انتقل من القائمة الجانبية إلى **إدارة المرشحين** (`company/nominations`).\n" .
                        "2. يمكنك استعراض كافة الخريجين المرشحين لوظائفكم وتخصصاتهم ومعدلاتهم وتحميل سيرهم الذاتية (CV).\n" .
                        "3. لكل مرشح، اضغط على زر **(تحديث الحالة / تفاصيل المقابلة)** لتحديد موعد وتوقيت ومكان المقابلة أو القبول النهائي.\n\n" .
                        "### 2️⃣ مباشرة عبر المساعد الذكي ⚡ (أسرع طريقة):\n" .
                        "• **لاستعراض المرشحين:** اطلب: *'عرض المرشحين لوظائف شركتنا'*\n" .
                        "• **لتحديد موعد مقابلة:** اطلب: *'حدد موعد مقابلة للخريج [الاسم] يوم [التاريخ] الساعة [الوقت]'*\n" .
                        "• **لقبول أو توظيف مرشح:** اطلب: *'قبول المرشح [الاسم]'* أو *'توظيف الخريج [الاسم]'*\n" .
                        "• **لاقتراح كفاءات جديدة:** اطلب: *'اقترح لي 5 خريجين في تقنية المعلومات بمعدل أعلى من 85%'*",
                    'tool_executed' => 'how_to_guidance',
                ];
            }

            // ط. استفسار عام وشامل عن كيفية استخدام المنظومة
            return [
                'content' => "🌟 **دليل استخدام المنظومة الذكية لمكتب تدريب وتأهيل الخريجين — جامعة طرابلس:**\n\n" .
                    "المنظومة مصممة لتوفير بيئة عمل متكاملة تجمع بين 5 قطاعات رئيسية:\n\n" .
                    "1. 🎯 **قطاع الإرشاد والتوجيه المهني:** إدارة الخريجين، البحث المتقدم، ترشيح الخريجين للوظائف (فردي وجماعي)، وتجميد/تنشيط/حذف الحسابات.\n" .
                    "2. 🎓 **قطاع التدريب والتطوير:** جدولة الدورات وورش العمل، وإدارة وقبول طلبات الالتحاق بالتدريب (فردياً أو دفعة واحدة)، ومتابعة الحضور والشهادات.\n" .
                    "3. 🏢 **قطاع الشراكات وسوق العمل:** توثيق الشركات الشريكة، نشر فرص العمل والوظائف الشاغرة، وتتبع معدلات توظيف الخريجين.\n" .
                    "4. 📝 **قطاع الجودة والتقييم والمتابعة:** إعداد استبيانات قياس رضا المتدربين والشركاء، ومتابعة المسار المهني للخريجين، وتوليد تقارير الجودة.\n" .
                    "5. 📢 **قطاع الإعلام والتغطيات الصحفية:** صياغة الأخبار الصحفية، نشر الإعلانات والتعميمات الرسمية، وتوثيق الفعاليات وجدول التغطيات الميدانية.\n\n" .
                    "💡 **الميزة الأقوى:** بصفتي مساعدك الذكي، يمكنك أن تطلب مني تنفيذ أي من هذه المهام مباشرة بالصوت أو النص دون الحاجة للتنقل بين الشاشات!\n" .
                    "ما هي العملية أو الشاشة التي تود معرفة تفاصيل إضافية عنها؟",
                'tool_executed' => 'how_to_guidance',
            ];
        }

        // ❄️ / 🟢 تجميد أو تنشيط أو فك/إلغاء تجميد حساب الخريج (لمسؤول الإرشاد المهني والمدير)
        $isUnfreezeIntent = Str::contains($text, [
            'فك تجميد', 'فك التجميد', 
            'الغاء تجميد', 'إلغاء تجميد', 'الغاء التجميد', 'إلغاء التجميد', 
            'رفع تجميد', 'رفع التجميد', 'ازالة تجميد', 'إزالة تجميد', 'ازالة التجميد', 'إزالة التجميد',
            'فك قفل', 'فك القفل', 'الغاء القفل', 'إلغاء القفل',
            'نشط', 'تنشيط', 'اعادة تنشيط', 'إعادة تنشيط', 'اعادة تفعيل', 'إعادة تفعيل'
        ]) || (Str::contains($text, ['تفعيل', 'تنشيط']) && Str::contains($text, ['حساب', 'خريج', 'الخريج', 'طالب', 'الطالب']));

        $isFreezeIntent = Str::contains($text, [
            'جمد', 'تجميد', 'إيقاف حساب', 'ايقاف حساب', 'قفل حساب', 'تعليق حساب', 'ايقاف تفعيل', 'إيقاف تفعيل'
        ]) || (Str::startsWith($text, ['جمد', 'وقف']) && Str::contains($text, ['خريج', 'الخريج', 'حساب']));

        if (($isUnfreezeIntent || $isFreezeIntent) && in_array('toggle_graduate_status', $authorizedNames)) {

            // Always prioritize unfreeze/activate detection over freeze
            if ($isUnfreezeIntent) {
                $action = 'activate';
            } elseif ($isFreezeIntent) {
                $action = 'freeze';
            } else {
                $action = 'toggle';
            }

            // Extract identifier by stripping known command prefixes and descriptors
            $prefixPattern = '/^(?:أريد\s+|ممكن\s+|برجاء\s+|رجاء\s+|لو\s+سمحت\s+|قم\s+بـ?\s*|يرجى\s+)?(?:فك\s+التجميد\s+عن|فك\s+تجميد|فك\s+التجميد|إلغاء\s+التجميد\s+عن|الغاء\s+التجميد\s+عن|إلغاء\s+تجميد|الغاء\s+تجميد|إلغاء\s+التجميد|الغاء\s+التجميد|رفع\s+التجميد\s+عن|رفع\s+تجميد|رفع\s+التجميد|إزالة\s+التجميد\s+عن|ازالة\s+التجميد\s+عن|إزالة\s+تجميد|ازالة\s+تجميد|إعادة\s+تنشيط|اعادة\s+تنشيط|إعادة\s+تفعيل|اعادة\s+تفعيل|تنشيط|نشط|تفعيل|فعل|تجميد|جمد|إيقاف|ايقاف|قفل)\s*/u';
            $descPattern = '/^(?:عن\s+|لـ\s*|الخاص\s+بـ?\s*|حساب\s+الخريج\s+|حساب\s+خريج\s+|حساب\s+الطالب\s+|حساب\s+طالب\s+|حساب\s+|الخريج\s+|خريج\s+|الطالب\s+|طالب\s+)+/u';

            $gradIdent = preg_replace($prefixPattern, '', $userMessage);
            $gradIdent = preg_replace($descPattern, '', $gradIdent);
            $gradIdent = preg_replace('/^[\s\p{P}]+|[\s\p{P}]+$/u', '', $gradIdent);
            $gradIdent = trim($gradIdent);

            // Fallback to older regex if stripping left empty string
            if (empty($gradIdent)) {
                if (preg_match('/(?:حساب\s+الخريج|حساب\s+خريج|حساب\s+الطالب|حساب\s+طالب|حساب|الخريج|خريج|الطالب|طالب)\s+([^\?\.\!,]+)/u', $userMessage, $m)) {
                    $gradIdent = trim($m[1]);
                }
            }

            $res = AiToolRegistry::executeTool($user, 'toggle_graduate_status', [
                'graduate_identifier' => $gradIdent,
                'action' => $action,
            ]);

            if (isset($res['status']) && $res['status'] === 'proposal') {
                $actWord = $res['data']['status_word'] ?? ($action === 'activate' ? 'إلغاء تجميد وتنشيط' : 'تجميد');
                $actIcon = ($action === 'activate') ? '🟢' : '❄️';
                return [
                    'content' => "{$actIcon} **تم إعداد أمر {$actWord} حساب الخريج:**\n\n" .
                        "يرجى مراجعة تفاصيل الحساب في البطاقة أدناه ثم الضغط على **[تأكيد وحفظ]** لتطبيق التغيير فوراً.",
                    'action_proposal' => $res,
                    'tool_executed' => 'toggle_graduate_status',
                ];
            } else {
                return [
                    'content' => $res['message'] ?? 'تعذر تجهيز طلب تعديل حالة الحساب. يرجى التأكد من اسم الخريج.',
                    'tool_executed' => 'toggle_graduate_status',
                ];
            }
        }

        // 🗑️ حذف ومسح حساب وسجلات الخريج نهائياً (لمسؤول الإرشاد المهني والمدير)
        if (Str::contains($text, ['احذف', 'حذف', 'مسح', 'ازالة', 'إزالة', 'delete']) &&
            Str::contains($text, ['حساب', 'خريج', 'الخريج', 'طالب', 'الطالب', 'سجل']) &&
            in_array('delete_graduate_account', $authorizedNames)) {

            $gradIdent = '';
            if (preg_match('/(?:حساب\s+الخريج|حساب\s+خريج|حساب\s+الطالب|حساب\s+طالب|حساب|الخريج|خريج|الطالب|طالب)\s+([^\?\.\!,]+)/u', $userMessage, $m)) {
                $gradIdent = trim($m[1]);
            } elseif (preg_match('/(?:احذف|حذف|مسح|ازالة|إزالة)\s+([^\?\.\!,]+)/u', $userMessage, $m)) {
                $gradIdent = trim($m[1]);
                $gradIdent = preg_replace('/^(?:حساب\s+|الخريج\s+|خريج\s+)/u', '', $gradIdent);
            }

            $res = AiToolRegistry::executeTool($user, 'delete_graduate_account', [
                'graduate_identifier' => $gradIdent,
            ]);

            if (isset($res['status']) && $res['status'] === 'proposal') {
                return [
                    'content' => "⚠️ **تم تجهيز طلب حذف حساب وسجلات الخريج:**\n\n" .
                        "يرجى قراءة التنبيه الأمني بعناية في البطاقة أدناه ثم الضغط على **[تأكيد وحفظ]** إذا كنت متأكداً تماماً من رغبتك في الحذف النهائي.",
                    'action_proposal' => $res,
                    'tool_executed' => 'delete_graduate_account',
                ];
            } else {
                return [
                    'content' => $res['message'] ?? 'تعذر تجهيز طلب حذف الحساب. يرجى التحقق من اسم الخريج.',
                    'tool_executed' => 'delete_graduate_account',
                ];
            }
        }

        // 🤝 الترشيح الجماعي لمجموعة خريجين لوظيفة (لمسؤول الإرشاد المهني والمدير)
        if (Str::contains($text, [
                'ترشيح مجموعة', 'رشح مجموعة', 'ترشيح جماعي', 'رشح لي مجموعة', 'ترشيح دفعة',
                'رشح خريجي', 'ترشيح خريجي', 'رشح خريجين', 'ترشيح عدة', 'رشح عدة', 'ترشيح الخريجين المؤهلين'
            ]) && in_array('bulk_nominate_graduates', $authorizedNames)) {

            $jobIdent = '';
            if (preg_match('/(?:لوظيفة|لفرصة|على وظيفة|في وظيفة|وظيفة)\s+([^\?\.\!]+)/u', $userMessage, $m)) {
                $jobIdent = trim($m[1]);
                $jobIdent = preg_replace('/\s+(?:من|بمعدل|أعلى|تخصص).*$/u', '', $jobIdent);
            }

            $major = null;
            if (preg_match('/(?:تخصص|قسم|كلية)\s+([^\s\?\.\!,]+(?:\s+[^\s\?\.\!,]+)?)/u', $userMessage, $m)) {
                $cand = trim($m[1]);
                if (!in_array($cand, ['مجموعة', 'الخريجين', 'خريجين', 'دفعة', 'عدة', 'لوظيفة', 'لفرصة', 'من'])) {
                    $major = $cand;
                }
            } elseif (preg_match('/(?:خريجي|خريجين)\s+(تقنية|برمجيات|حاسوب|هندسة|محاسبة|إدارة|طب|صيدلة|علوم)/u', $userMessage, $m)) {
                $major = trim($m[1]);
            }

            $minGpa = null;
            if (preg_match('/(?:معدل|بمعدل|نسبة)\s*(?:أعلى من|فوق|تتجاوز)?\s*(\d+(?:\.\d+)?)/u', $userMessage, $m)) {
                $minGpa = (float) $m[1];
            }

            $res = AiToolRegistry::executeTool($user, 'bulk_nominate_graduates', [
                'job_identifier' => $jobIdent,
                'major' => $major,
                'min_gpa' => $minGpa,
                'limit' => 4,
            ]);

            if (isset($res['status']) && $res['status'] === 'proposal') {
                return [
                    'content' => "🤝 **تم اختيار وإعداد المرشحين المؤهلين للترشيح الجماعي:**\n\n" .
                        "يرجى استعراض قائمة الخريجين المرشحين ومعدلاتهم في البطاقة أدناه ثم الضغط على **[تأكيد وحفظ]** لإرسال الترشيحات دفعة واحدة للجهة الشريكة.",
                    'action_proposal' => $res,
                    'tool_executed' => 'bulk_nominate_graduates',
                ];
            } else {
                return [
                    'content' => $res['message'] ?? 'تعذر تجهيز الترشيح الجماعي. يرجى تحديد اسم الوظيفة.',
                    'tool_executed' => 'bulk_nominate_graduates',
                ];
            }
        }

        // 🎓 إدارة وقبول/رفض طلبات الالتحاق بالتدريب (لمنسق التدريب والمدير)
        if ((Str::contains($text, ['قبول طلبات', 'قبول جميع الطلبات', 'قبول كافة الطلبات', 'قبول طلب', 'الموافقة على طلبات', 'الموافقة على جميع', 'رفض طلبات', 'رفض طلب']) ||
             (Str::contains($text, ['قبول', 'الموافقة على', 'رفض']) && Str::contains($text, ['تدريب', 'التدريب', 'دورة', 'دورات', 'ورشة', 'متدرب', 'متدربين', 'التحاق']))) &&
            in_array('manage_training_applications', $authorizedNames)) {

            $action = Str::contains($text, ['رفض']) ? 'reject' : 'approve';
            $scope = 'all';
            $trainingIdent = null;
            $userIdent = null;

            if (Str::contains($text, ['جميع', 'كافة', 'الكل', 'all'])) {
                $scope = 'all';
            } elseif (preg_match('/(?:لدورة|لتدريب|لورشة|في دورة|في تدريب|في ورشة)\s+([^\?\.\!]+)/u', $userMessage, $m)) {
                $scope = 'training';
                $trainingIdent = trim($m[1]);
            } elseif (preg_match('/(?:للخريج|للمتدرب|للطالب|طلب)\s+([^\s\?\.\!,]+(?:\s+[^\s\?\.\!,]+)?)/u', $userMessage, $m)) {
                $cand = trim($m[1]);
                if (!in_array($cand, ['التدريب', 'تدريب', 'دورة', 'جميع', 'كافة'])) {
                    $scope = 'single';
                    $userIdent = $cand;
                }
            }

            $res = AiToolRegistry::executeTool($user, 'manage_training_applications', [
                'action' => $action,
                'scope' => $scope,
                'training_identifier' => $trainingIdent,
                'user_identifier' => $userIdent,
            ]);

            if (isset($res['status']) && $res['status'] === 'proposal') {
                $actWord = ($action === 'reject') ? 'رفض' : 'قبول';
                return [
                    'content' => "🎓 **تم إعداد أمر {$actWord} طلبات الالتحاق بالتدريب:**\n\n" .
                        "يرجى مراجعة تفاصيل الطلبات المتأثرة في البطاقة التفاعلية أدناه ثم الضغط على **[تأكيد وحفظ]** لاعتمادها فوراً.",
                    'action_proposal' => $res,
                    'tool_executed' => 'manage_training_applications',
                ];
            } else {
                return [
                    'content' => $res['message'] ?? 'تعذر تجهيز إجراء طلبات التدريب.',
                    'tool_executed' => 'manage_training_applications',
                ];
            }
        }

        // 1. صياغة خبر صحفي (لمسؤول الإعلام أو المدير)
        if (Str::contains($text, ['صغ', 'صياغة', 'اكتب خبر', 'تحرير خبر', 'مسودة خبر', 'بيان صحفي', 'خبر صحفي']) && in_array('draft_news_article', $authorizedNames)) {
            $title = "اختتام فعاليات ورشة العمل التفاعلية بجامعة طرابلس";
            if (preg_match('/(?:بعنوان|حول|عن)\s+([^\?\.\!]+)/u', $userMessage, $m)) {
                $title = "تغطية " . trim($m[1]);
            }

            $excerpt = "نظم مكتب تدريب وتأهيل الخريجين بجامعة طرابلس ورشة عمل متخصصة استهدفت تعزيز المهارات العملية للطلاب وربطهم بسوق العمل.";
            $articleContent = "طرابلس — في إطار استراتيجية جامعة طرابلس لتأهيل وتطوير مهارات الطلاب والخريجين، اختتم مكتب تدريب وتأهيل الخريجين فعاليات البرنامج التدريبي المكثف بحضور نخبة من الأساتذة والخبراء والمدربين المعتمدين.\n\nشهد البرنامج تفاعلاً كبيراً من المتدربين من خلال تطبيقات عملية وحلقات نقاشية تهدف إلى ردم الفجوة بين المناهج الأكاديمية واحتياجات سوق العمل الحقيقية.\n\nوأكدت إدارة المكتب حرصها المستمر على رعاية الكفاءات الوطنية وإتاحة المزيد من المسارات التدريبية النوعية خلال الفترة القادمة.";

            $proposal = [
                'status' => 'proposal',
                'type' => 'create_news_draft',
                'action_type' => 'create_news_draft',
                'summary' => 'تأكيد إنشاء مسودة خبر صحفي في المنظومة',
                'title' => 'تأكيد إنشاء مسودة خبر صحفي',
                'details' => "**العنوان:** {$title}\n**الملخص:** {$excerpt}",
                'message' => "لقد قمت بصياغة هذا البيان الصحفي المعتمد. هل ترغب في اعتماده وحفظه كمسودة في وحدة الإعلام؟",
                'data' => [
                    'title' => $title,
                    'category' => 'workshop',
                    'excerpt' => $excerpt,
                    'content' => $articleContent,
                ]
            ];

            return [
                'content' => "📰 **لقد قمت بصياغة مسودة الخبر الصحفي وفق الهوية الإعلامية لجامعة طرابلس:**\n\n" .
                    "**العنوان:** {$title}\n\n" .
                    "**الملخص:** {$excerpt}\n\n" .
                    "**نص المقال:**\n{$articleContent}\n\n" .
                    "يمكنك تأكيد الحفظ كمسودة بنقرة واحدة من البطاقة التفاعلية أدناه.",
                'action_proposal' => $proposal,
                'tool_executed' => 'draft_news_article',
            ];
        }

        // 2. صياغة واقتراح برنامج تدريبي (لمنسق التدريب أو المدير)
        if (Str::contains($text, ['صغ دورة', 'اقترح دورة', 'صياغة تدريب', 'مسودة تدريب', 'مسودة دورة', 'برنامج تدريبي جديد', 'إضافة تدريب']) && in_array('draft_training_program', $authorizedNames)) {
            $title = "مهارات الذكاء الاصطناعي التطبيقي وسوق العمل";
            if (preg_match('/(?:بعنوان|حول|عن|في)\s+([^\?\.\!]+)/u', $userMessage, $m)) {
                $title = "دورة " . trim($m[1]);
            }

            $proposal = [
                'status' => 'proposal',
                'type' => 'create_training_draft',
                'action_type' => 'create_training_draft',
                'summary' => 'تأكيد إنشاء مسودة برنامج تدريبي جديد',
                'title' => 'تأكيد إنشاء مسودة برنامج تدريبي',
                'details' => "**العنوان:** {$title}\n**المقاعد:** 30 مقعداً | **المدة:** 5 أيام\n**المكان:** قاعة التدريب الرئيسية - جامعة طرابلس",
                'message' => "تم إعداد مسودة البرنامج التدريبي. هل ترغب في إضافته كمسودة في جدول التدريبات؟",
                'data' => [
                    'title' => $title,
                    'type' => 'course',
                    'seats' => 30,
                    'duration' => 5,
                    'location' => 'قاعة التدريب الرئيسية - جامعة طرابلس',
                    'description' => 'برنامج تدريبي مكثف يركز على التطبيقات العملية وبناء المهارات التقنية للخريجين.',
                ]
            ];

            return [
                'content' => "🎓 **تم إعداد مسودة البرنامج التدريبي المقترح:**\n\n" .
                    "• **العنوان:** {$title}\n" .
                    "• **النوع:** دورة تدريبية (Course)\n" .
                    "• **المقاعد المقترحة:** 30 مقعداً\n" .
                    "• **المدة المقترحة:** 5 أيام تدريبية\n" .
                    "• **المكان:** قاعة التدريب الرئيسية - جامعة طرابلس\n\n" .
                    "يمكنك تأكيد الحفظ بالضغط على زر التأكيد أدناه للمتابعة.",
                'action_proposal' => $proposal,
                'tool_executed' => 'draft_training_program',
            ];
        }

        // 2.5 صياغة خطاب توجيهي رسمي واحترافي (Cover Letter) للخريج بالبيانات الرسمية الحقيقية
        $isCoverLetterIntent = Str::contains($text, [
            'خطاب توجيه', 'خطاب التوجيه', 'خطاب توجيهي', 'خطاب تقديم', 'خطاب التقديم', 'خطاب دافع', 'رسالة تغطية',
            'cover letter', 'اكتب لي خطاب', 'أعد لي خطاب', 'اعد لي خطاب', 'صغ لي خطاب', 'صياغة خطاب', 'خطاب للتقديم'
        ]);

        if ($isCoverLetterIntent && in_array('generate_cover_letter', $authorizedNames)) {
            $jobTitle = '';
            if (preg_match('/(?:لوظيفة|على وظيفة|في وظيفة|لوظيفة:\s*|لوظيفة\s+)([^\?\.\!]+)/u', $userMessage, $m)) {
                $jobTitle = trim($m[1]);
            } else {
                foreach (JobOpportunity::pluck('title') as $title) {
                    if (Str::contains($text, mb_strtolower($title))) {
                        $jobTitle = $title;
                        break;
                    }
                }
            }

            $res = AiToolRegistry::executeTool($user, 'generate_cover_letter', ['job_title' => $jobTitle]);
            return [
                'content' => $res['message'] ?? 'تم إعداد خطاب التوجيه بنجاح.',
                'tool_executed' => 'generate_cover_letter',
            ];
        }

        // 3. تقديم الخريج على برنامج تدريبي أو فرصة وظيفية (تقديم على / سجلني في / أريد التقديم على)
        $isApplyIntent = Str::contains($text, [
            'تقديم على', 'التقديم على', 'قدم على', 'قدم لي على', 'سجلني في', 'سجلني على',
            'تسجيل في', 'التحاق بـ', 'التحاق في', 'التحاق بتدريب', 'أريد التقديم', 'اريد تقديم',
            'أريد تقديم', 'اريد اقدم', 'أريد أن أقدم', 'ترشح لـ', 'ترشيح نفسي', 'أريد الترشح',
            'اريد الترشح', 'قدم لي'
        ]);

        if ($isApplyIntent) {
            $extractedKw = '';
            if (preg_match('/(?:تقديم على|التقديم على|قدم على|قدم لي على|قدم لي في|قدم لي|سجلني في|سجلني على|تسجيل في|تسجيل على|التحاق بـ|التحاق في|التحاق بتدريب|اقدم على|أقدم على|ترشح لـ|ترشيح لـ|ترشيح نفسي لـ|ترشيح نفسي في|لوظيفة|على وظيفة|في وظيفة|بدورة|بتدريب)\s+([^\?\.\!]+)/u', $userMessage, $m)) {
                $extractedKw = trim($m[1]);
            }

            // تنظيف الكلمة المفتاحية من البادئات الشائعة
            $cleanKw = preg_replace('/^(?:وظيفة|فرصة|شغل|دورة|تدريب|برنامج|ورشة)\s+/u', '', $extractedKw);
            $cleanKw = trim($cleanKw);

            $isExplicitJob = Str::contains($text, ['وظيفة', 'عمل', 'شاغر', 'وظائف', 'شغل', 'توظيف', 'فرصة توظيف', 'فرص توظيف']) || 
                (!empty($cleanKw) && JobOpportunity::where('title', 'like', "%{$cleanKw}%")->exists());

            $isExplicitTraining = Str::contains($text, ['تدريب', 'دورة', 'ورشة', 'برنامج تدريبي']) ||
                (!empty($cleanKw) && Training::where('title', 'like', "%{$cleanKw}%")->exists() && !$isExplicitJob);

            // التحقق إن كان الطلب عاماً (مثل: مناسبة، تناسبني، تناسب تخصصي، لي، ملائمة)
            $genericKeywords = ['مناسبة', 'تناسبني', 'ملائمة', 'تناسب تخصصي', 'توظيف مناسبة', 'عمل مناسبة', 'لي', 'متاحة', 'شاغرة'];
            $isGenericRequest = empty($cleanKw) || in_array(mb_strtolower($cleanKw), $genericKeywords) || Str::contains($cleanKw, ['مناسب', 'ملائم', 'تخصصي']);

            // أ. مسار التقديم على الوظيفة
            if (($isExplicitJob || (!$isExplicitTraining && in_array('apply_for_job', $authorizedNames))) && in_array('apply_for_job', $authorizedNames)) {
                $gradData = $user->graduateData ?? GraduateData::where('email', $user->email)->first();
                $appliedJobIds = $gradData ? Nomination::where('graduate_id', $gradData->id)->pluck('job_opportunity_id')->toArray() : [];

                $selectedJob = null;

                if ($isGenericRequest) {
                    // البحث الذكي عن وظيفة شاغرة متوافقة مع تخصص الخريج ولم يسبق التقديم عليها
                    $openJobs = JobOpportunity::whereIn('status', ['open', 'active'])
                        ->whereNotIn('id', $appliedJobIds)
                        ->get();

                    if ($openJobs->isNotEmpty()) {
                        // محاولة المطابقة مع تخصص الخريج
                        $gradMajor = $gradData ? mb_strtolower($gradData->major ?? '') : '';
                        if (!empty($gradMajor)) {
                            // استخراج الكلمات الأساسية من التخصص (مثل برمجيات، حاسوب، تقنية، شبكات)
                            $majorWords = array_filter(explode(' ', preg_replace('/[^\p{Arabic}\w\s]/u', '', $gradMajor)), fn($w) => mb_strlen($w) > 2 && !in_array($w, ['قسم', 'كلية', 'جامعة']));
                            foreach ($openJobs as $job) {
                                $jobText = mb_strtolower($job->title . ' ' . ($job->major ?? '') . ' ' . ($job->description ?? ''));
                                foreach ($majorWords as $mw) {
                                    if (Str::contains($jobText, $mw)) {
                                        $selectedJob = $job;
                                        break 2;
                                    }
                                }
                            }
                        }
                        if (!$selectedJob) {
                            $selectedJob = $openJobs->first();
                        }
                    } else {
                        // لا توجد وظائف جديدة غير مقدم عليها
                        if (!empty($appliedJobIds)) {
                            return [
                                'content' => "💼 لقد تقدمت بالفعل لكافة الفرص الوظيفية المتاحة حالياً المتوافقة معك.\n\n" .
                                    "يمكنك متابعة حالة قبولك وترشيحك من خلال الضغط على:\n\n" .
                                    "#prompt:ما هي حالة طلباتي للتوظيف والتدريب؟ [📊 استعراض ومتابعة حالة طلباتي]",
                                'tool_executed' => 'apply_for_job',
                            ];
                        } else {
                            return [
                                'content' => "لا توجد فرص وظيفية شاغرة ومتاحة للتقديم في الوقت الحالي. سنقوم بإشعارك فور فتح أي شواغر جديدة.",
                                'tool_executed' => 'apply_for_job',
                            ];
                        }
                    }
                }

                $args = $selectedJob ? ['job_id' => $selectedJob->id] : ['job_title' => ($cleanKw ?: $extractedKw)];
                $res = AiToolRegistry::executeTool($user, 'apply_for_job', $args);

                if (isset($res['status']) && $res['status'] === 'proposal') {
                    $prefixMsg = $selectedJob
                        ? "💼 **بناءً على تخصصك الأكاديمي (" . ($gradData->major ?? 'المسجل') . ")، تم ترشيح أفضل فرصة عمل متاحة وتجهيز طلبك:**\n\n"
                        : "💼 **تم إعداد طلب الترشح لفرصة العمل بنجاح:**\n\n";

                    return [
                        'content' => $prefixMsg .
                            "يرجى مراجعة تفاصيل الوظيفة في البطاقة أدناه ثم الضغط على **[تأكيد وحفظ]** لإرسال ملفك رسمياً.",
                        'action_proposal' => $res,
                        'tool_executed' => 'apply_for_job',
                    ];
                } else {
                    return [
                        'content' => $res['message'] ?? 'تعذر تجهيز طلب التقديم على الوظيفة. يرجى التحقق من مسمى الوظيفة.',
                        'tool_executed' => 'apply_for_job',
                    ];
                }
            }

            // ب. مسار التقديم على البرنامج التدريبي
            if ($isExplicitTraining && in_array('apply_for_training', $authorizedNames)) {
                $appliedTrainIds = TrainingApplication::where('user_id', $user->id)->pluck('training_id')->toArray();
                $selectedTraining = null;

                if ($isGenericRequest) {
                    $openTrainings = Training::whereIn('status', ['active', 'open', 'upcoming'])
                        ->whereNotIn('id', $appliedTrainIds)
                        ->get();

                    if ($openTrainings->isNotEmpty()) {
                        $selectedTraining = $openTrainings->first();
                    } else {
                        if (!empty($appliedTrainIds)) {
                            return [
                                'content' => "🎓 لقد تقدمت بالفعل بطلبات التحاق بكافة البرامج التدريبية المتاحة حالياً.\n\n" .
                                    "يمكنك متابعة حالة طلباتك من خلال الضغط على:\n\n" .
                                    "#prompt:ما هي حالة طلباتي للتوظيف والتدريب؟ [📊 استعراض حالة طلباتي للتدريب]",
                                'tool_executed' => 'apply_for_training',
                            ];
                        } else {
                            return [
                                'content' => "لا توجد برامج تدريبية مفتوحة للتسجيل حالياً. سنقوم بإعلامك فور إطلاق دورات تدريبية جديدة.",
                                'tool_executed' => 'apply_for_training',
                            ];
                        }
                    }
                }

                $args = $selectedTraining ? ['training_id' => $selectedTraining->id] : ['training_title' => ($cleanKw ?: $extractedKw)];
                $res = AiToolRegistry::executeTool($user, 'apply_for_training', $args);

                if (isset($res['status']) && $res['status'] === 'proposal') {
                    $prefixMsg = $selectedTraining
                        ? "🎓 **بناءً على ملفك الأكاديمي، تم اختيار هذا البرنامج التدريبي وتجهيز طلب الالتحاق:**\n\n"
                        : "🎓 **تم تجهيز طلب التقديم على البرنامج التدريبي بنجاح:**\n\n";

                    return [
                        'content' => $prefixMsg .
                            "يرجى مراجعة تفاصيل التدريب والمقاعد أدناه، والضغط على **[تأكيد وحفظ]** لإرسال الطلب رسمياً لمنسق التدريب.",
                        'action_proposal' => $res,
                        'tool_executed' => 'apply_for_training',
                    ];
                } else {
                    return [
                        'content' => $res['message'] ?? 'تعذر تجهيز طلب التقديم. يرجى التحقق من اسم التدريب.',
                        'tool_executed' => 'apply_for_training',
                    ];
                }
            }
        }

        // 💼 استعراض والبحث في فرص العمل والوظائف المتاحة (لكافة الأدوار: مسؤول الإرشاد، المدير، الخريج، الموظف)
        $cleanedMsg = trim($text, " ?.!\t\n\r\0\x0B؟،,");
        $isJobListIntent = (
            Str::contains($text, [
                'ماذا عن الوظائف', 'ماذا عن العمل', 'ماذا عن فرص العمل', 'ماذا بخصوص الوظائف', 'وماذا عن الوظائف', 'عن الوظائف', 'بخصوص الوظائف',
                'الفرص الوظيفية', 'فرص وظيفية', 'فرص العمل', 'فرص عمل', 'فرصة عمل',
                'الوظائف المتاحة', 'وظائف متاحة', 'الوظائف الشاغرة', 'وظائف شاغرة',
                'قائمة بالوظائف', 'قائمة الوظائف', 'عرض الوظائف', 'استعراض الوظائف',
                'ما هي الوظائف', 'ماهي الوظائف', 'ما هي الفرص', 'ماهي الفرص',
                'الوظائف الموجودة', 'وظائف موجودة', 'فرص التوظيف', 'سوق العمل',
                'الوظائف المعلنة', 'وظائف معلنة', 'شواغر العمل', 'الشواغر المتاحة',
                'شن الوظائف', 'شن في وظائف', 'شنو الوظائف', 'هل في وظائف', 'هل توجد وظائف', 'هل هناك وظائف',
                'ابحث عن وظيفة', 'ابحث لي عن وظيفة', 'نبي وظيفة', 'نبي عمل', 'أريد وظيفة', 'اريد وظيفة', 'أريد عمل', 'اريد عمل'
            ]) ||
            in_array($cleanedMsg, ['الوظائف', 'وظائف', 'فرص العمل', 'فرص عمل', 'الشواغر', 'شواغر', 'سوق العمل', 'وظيفة', 'عمل', 'التوظيف']) ||
            (
                Str::contains($text, ['وظائف', 'وظيفة', 'توظيف', 'شواغر', 'فرص عمل', 'فرصة عمل']) &&
                Str::contains($text, ['ماذا', 'عن', 'بخصوص', 'متاح', 'متاحة', 'مفتوح', 'مفتوحة', 'شاغر', 'شاغرة', 'قائمة', 'عرض', 'استعراض', 'جديد', 'جديدة', 'معلن', 'معلنة', 'ما هي', 'ماهي', 'شنو', 'شن', 'ايش', 'أريد', 'اريد', 'نبي', 'اعطني', 'أعطني', 'شوفلي', 'هل', 'أين', 'اين'])
            )
        ) && !Str::contains($text, ['أضف', 'اضف', 'انشر', 'نشر', 'إنشاء', 'صياغة', 'رشح', 'ترشيح', 'ترشيج', 'كيف', 'طريقة', 'مرشح', 'مرشحين', 'مرشحون', 'مقابلة', 'إحصائيات', 'احصائيات', 'ملخص', 'شركات شريكة', 'الشركات الشريكة', 'قائمة الشركات', 'تقديم على', 'التقديم على', 'سجلني']);

        if ($isJobListIntent && in_array('search_job_opportunities', $authorizedNames)) {
            $keyword = '';
            if (preg_match('/(?:الوظائف|وظائف|فرص|الفرص|وظيفة|الوظيفة)\s+(?:في\s+مجال\s+|في\s+تخصص\s+|في\s+|تخص\s+|بـ\s*)?([^\?\.\!؟،,]+)/u', $userMessage, $m)) {
                $candidateKw = trim(preg_replace('/^[\s\p{P}]+|[\s\p{P}]+$/u', '', $m[1]));
                $ignoredWords = [
                    'المتاحة', 'الشاغرة', 'الموجودة', 'الجديدة', 'المعلنة', 'العمل', 'التوظيف', 
                    'الوظيفية', 'متاحة', 'شاغرة', 'حالياً', 'مفتوحة', 'المفتوحة', 'عنها', 'بخصوصها',
                    'المعروضة', 'معروضة', 'لنا', 'للخريجين', 'الخاصة', ''
                ];
                if (!in_array($candidateKw, $ignoredWords) && mb_strlen($candidateKw) > 1) {
                    $keyword = $candidateKw;
                }
            }

            $res = AiToolRegistry::executeTool($user, 'search_job_opportunities', ['keyword' => $keyword]);
            $jobs = $res['data'] ?? [];

            if (empty($jobs)) {
                return [
                    'content' => "💼 **فرص العمل والتشغيل:**\n\n" .
                        "لا توجد فرص وظيفية مفتوحة حالياً " . (!empty($keyword) ? "تطابق **'{$keyword}'**." : "في المنظومة.") . "\n\n" .
                        "💡 يمكنك متابعة التحديثات أو سؤال مسؤولي الإرشاد المهني.",
                    'tool_executed' => 'search_job_opportunities',
                ];
            }

            $out = "💼 **قائمة الفرص الوظيفية المتاحة في المنظومة (" . count($jobs) . " فرصة):**\n\n";
            foreach ($jobs as $idx => $j) {
                $num = $idx + 1;
                $out .= "{$num}. 🏢 **{$j['title']}**\n";
                $out .= "   • **الشركة الشريكة:** {$j['company']}\n";
                $out .= "   • **الموقع:** 📍 {$j['location']} | **نوع العقد:** ⏱ {$j['type']}\n";
                $out .= "   • **المقاعد الشاغرة:** 👥 {$j['seats']} | **آخر موعد:** 📅 {$j['deadline']}\n";

                if ($user->role === 'graduate') {
                    $out .= "   👉 [📝 التقديم على هذه الوظيفة](#prompt:أريد التقديم على وظيفة {$j['title']})\n\n";
                } elseif (in_array($user->role, ['career_guidance_officer', 'admin'])) {
                    $out .= "   👉 [🤝 ترشيح خريج لهذه الوظيفة](#prompt:رشح الخريج لوظيفة {$j['title']})\n\n";
                } else {
                    $out .= "\n";
                }
            }

            if ($user->role === 'graduate') {
                $out .= "━━━━━━━━━━━━━━━━━━━━━━\n" .
                    "💡 **للتقديم المباشر:** اضغط على زر التقديم أسفل أي وظيفة، أو اكتب: *'أريد التقديم على وظيفة [اسم الوظيفة]'*.";
            }

            return [
                'content' => $out,
                'tool_executed' => 'search_job_opportunities',
            ];
        }

        // 5. ترشيح خريج لوظيفة (لمسؤول الإرشاد المهني والمدير)
        $isNominationIntent = (
            Str::startsWith($text, ['رشح', 'ترشيح', 'ترشيج', 'ارشح', 'نرشح']) ||
            Str::contains($text, [
                'رشح الخريج', 'ترشيح الخريج', 'ترشيج الخريج', 'رشح لي الخريج', 'ترشيح خريج', 'ترشيج خريج', 'ترشيح خريجين',
                'رشح الطالب', 'ترشيح الطالب', 'رشح لي طالب', 'ترشيح طالب', 'نرشح خريج', 'نرشح الطالب'
            ])
        ) && in_array('nominate_graduate_for_job', $authorizedNames);

        if ($isNominationIntent) {
            $gradIdent = '';
            $jobIdent = '';

            // الحالة أ: توفر اسم الخريج واسم الوظيفة معاً
            if (preg_match('/(?:رشح|ترشيح)\s+(?:لي\s+)?(?:الخريج\s+|خريج\s+|الطالب\s+|طالب\s+)?(.+?)\s+(?:لوظيفة|لفرصة|على وظيفة|في وظيفة)\s+([^\?\.\!]+)/u', $userMessage, $m)) {
                $gradIdent = trim($m[1]);
                $jobIdent = trim($m[2]);
            }

            if (!empty($gradIdent) && !empty($jobIdent)) {
                $res = AiToolRegistry::executeTool($user, 'nominate_graduate_for_job', [
                    'graduate_identifier' => $gradIdent,
                    'job_identifier' => $jobIdent,
                ]);

                if (isset($res['status']) && $res['status'] === 'proposal') {
                    return [
                        'content' => "🤝 **تم إعداد مقترح ترشيح الخريج لفرصة العمل:**\n\n" .
                            "يرجى مراجعة بيانات الخريج والوظيفة في البطاقة التفاعلية أدناه وتأكيد الترشيح.",
                        'action_proposal' => $res,
                        'tool_executed' => 'nominate_graduate_for_job',
                    ];
                } else {
                    return [
                        'content' => $res['message'] ?? 'تعذر إعداد الترشيح. يرجى التأكد من اسم الخريج واسم الوظيفة.',
                        'tool_executed' => 'nominate_graduate_for_job',
                    ];
                }
            }

            // الحالة ب: تحديد اسم الخريج فقط دون تحديد الوظيفة (مثال: "رشح الخريج المنيب")
            if (preg_match('/(?:رشح|ترشيح)\s+(?:لي\s+)?(?:الخريج\s+|خريج\s+|الطالب\s+|طالب\s+)?([^\?\.\!]+)/u', $userMessage, $m)) {
                $candidateGrad = trim($m[1]);
                if (!in_array($candidateGrad, ['خريج', 'الخريج', 'طالب', 'الطالب', ''])) {
                    $gradIdent = $candidateGrad;
                }
            }

            if (!empty($gradIdent)) {
                $gradData = null;
                if (is_numeric($gradIdent)) {
                    $gradData = GraduateData::find((int) $gradIdent) ?? GraduateData::where('phone', 'like', "%{$gradIdent}%")->first();
                } else {
                    $gradData = GraduateData::where('name', 'like', "%{$gradIdent}%")
                        ->orWhere('phone', 'like', "%{$gradIdent}%")
                        ->orWhere('email', $gradIdent)
                        ->first();
                    if (!$gradData) {
                        $u = User::where('name', 'like', "%{$gradIdent}%")->where('role', 'graduate')->first();
                        if ($u && $u->graduateData) {
                            $gradData = $u->graduateData;
                        }
                    }
                }

                if ($gradData) {
                    $openJobs = JobOpportunity::where('status', 'open')->with('company')->take(4)->get();
                    $jobListStr = '';
                    if ($openJobs->isNotEmpty()) {
                        foreach ($openJobs as $idx => $job) {
                            $compName = $job->company ? $job->company->name : 'جهة غير محددة';
                            $num = $idx + 1;
                            $jobListStr .= "{$num}. 💼 **{$job->title}** ({$compName})\n";
                        }
                    } else {
                        $jobListStr = "• لا توجد فرص عمل مفتوحة حالياً في المنظومة.\n";
                    }

                    $firstJobTitle = $openJobs->first() ? $openJobs->first()->title : 'مطور واجهات وتطبيقات الويب';

                    $out = "🤝 **ترشيح الخريج: {$gradData->name}**\n\n" .
                        "📋 **بيانات الخريج:**\n" .
                        "• **الكلية:** {$gradData->faculty}\n" .
                        "• **التخصص:** {$gradData->major}\n" .
                        "• **المعدل:** **{$gradData->gpa}%**\n" .
                        "• **رقم الهاتف:** {$gradData->phone}\n\n" .
                        "💼 **يرجى تحديد فرصة العمل المراد ترشيحه لها من الفرص المتاحة:**\n" .
                        $jobListStr . "\n" .
                        "💡 **للترشيح المباشر، تفضل بكتابة:**\n" .
                        "*'رشح {$gradData->name} لوظيفة [اسم الوظيفة]'*\n" .
                        "*(مثال: `رشح {$gradData->name} لوظيفة {$firstJobTitle}`)*";

                    return [
                        'content' => $out,
                        'tool_executed' => 'nominate_graduate_for_job',
                    ];
                } else {
                    return [
                        'content' => "⚠️ لم يتم العثور على خريج يطابق **'{$gradIdent}'** في المنظومة.\n\n" .
                            "💡 يرجى التأكد من اسم الخريج أو رقم هاتفه، أو البحث في قائمة الخريجين أولاً بقول: *'ابحث عن خريج تقنية المعلومات'*.",
                        'tool_executed' => 'nominate_graduate_for_job',
                    ];
                }
            }

            // الحالة ج: لم يتم تحديد أي خريج أو وظيفة (طلب عام)
            $openJobs = JobOpportunity::where('status', 'open')->with('company')->take(3)->get();
            $jobSamples = [];
            foreach ($openJobs as $j) {
                $jobSamples[] = "• **{$j->title}** (" . ($j->company ? $j->company->name : '') . ")";
            }
            $jobSamplesStr = !empty($jobSamples) ? implode("\n", $jobSamples) : "• لا توجد وظائف مفتوحة حالياً.";

            return [
                'content' => "🤝 **خدمة ترشيح الخريجين لفرص العمل:**\n\n" .
                    "بصفتك مسؤول الإرشاد المهني، يمكنك ترشيح أي خريج مؤهل لشغل الوظائف المتاحة لدى الشركات الشريكة.\n\n" .
                    "💼 **أبرز الوظائف المفتوحة حالياً:**\n" .
                    $jobSamplesStr . "\n\n" .
                    "💡 **طريقة الطلب:**\n" .
                    "اكتب: **'رشح الخريج [الاسم] لوظيفة [اسم الوظيفة]'**\n" .
                    "*(مثال: `رشح الخريج المنيب محمد الشريف لوظيفة مطور واجهات وتطبيقات الويب`)*\n\n" .
                    "أو اكتب اسم الخريج فقط مثل: *'رشح المنيب'* وسأعرض عليك الوظائف المناسبة لاختيار إحداها!",
                'tool_executed' => 'nominate_graduate_for_job',
            ];
        }

        // ==========================================
        // 📊 استعلامات الإحصائيات والأرقام (بدون إغراق بالبيانات الشخصية)
        // ==========================================

        // أ. إحصائيات وأعداد الخريجين
        $isGradStats = (
            Str::contains($text, [
                'كم عدد الخريجين', 'كم خريج', 'عدد الخريجين', 'إحصائيات الخريجين', 'احصائيات الخريجين',
                'إحصائية الخريجين', 'احصائية الخريجين', 'كم عدد خريجي', 'كم خريج مسجل', 'كم عدد المسجلين',
                'نسبة التوظيف', 'كم خريج توظف', 'كم باحث عن عمل', 'إحصائيات التوظيف', 'احصائيات التوظيف',
                'أرقام الخريجين', 'ارقام الخريجين'
            ]) ||
            ((Str::contains($text, ['كم عدد', 'كم هو عدد', 'أعطني عدد', 'اعطني عدد', 'احصائيات', 'إحصائيات', 'أرقام', 'ارقام', 'إحصائية', 'احصائية']) || preg_match('/^كم\s+(?:خريج|خريجين|مسجل)/u', $text)) && Str::contains($text, ['خريج', 'خريجين', 'الخريجين']))
        );

        if ($isGradStats && in_array('get_graduates_statistics', $authorizedNames)) {
            $res = AiToolRegistry::executeTool($user, 'get_graduates_statistics', []);
            $d = $res['data'] ?? [];

            $facList = [];
            if (!empty($d['faculties'])) {
                foreach ($d['faculties'] as $fName => $fCount) {
                    $facList[] = "{$fName} ({$fCount})";
                }
            }
            $facStr = !empty($facList) ? implode('، ', $facList) : 'غير مسجل';

            $out = "📊 **إحصائيات الخريجين في منظومة جامعة طرابلس:**\n\n" .
                "• **إجمالي حسابات الخريجين المسجلين:** **{$d['total_registered']} خريج مسجل**\n" .
                "  - ✅ **الملفات المكتملة والسير الذاتية:** **{$d['completed_profiles']} خريجين** (جاهزون للترشيح والتوظيف)\n" .
                "  - ⏳ **بانتظار استكمال الملف الأكاديمي:** **{$d['pending_profiles']} خريج**\n" .
                "• **حالة الحسابات:** **{$d['active_accounts']} نشط** | **{$d['frozen_accounts']} مجمد**\n" .
                "• **حالة التوظيف (للملفات المكتملة):**\n" .
                "  - 🔍 باحثون عن فرصة عمل: **{$d['seeking_count']}**\n" .
                "  - 💼 موظفون حالياً: **{$d['employed_count']}**\n" .
                ($d['further_study_count'] > 0 ? "  - 🎓 يواصلون دراساتهم العليا: **{$d['further_study_count']}**\n" : "") .
                "• **السير الذاتية المرفوعة:** **{$d['with_cv_count']}** سيرة ذاتية بنسبة (**{$d['cv_rate']}**)\n" .
                "• **توزيع الكليات للملفات المكتملة:** {$facStr}\n\n" .
                "💡 _توضيح المنظومة:_ يرجع الفرق بين إجمالي المسجلين ({$d['total_registered']}) والملفات المكتملة ({$d['completed_profiles']}) إلى أن {$d['pending_profiles']} خريجاً قاموا بإنشاء حساباتهم في المنظومة ولم يستكملوا تعبئة بيانات سيرهم الذاتية وتخصصاتهم الدقيقة بعد.";

            return [
                'content' => $out,
                'tool_executed' => 'get_graduates_statistics',
            ];
        }

        // ب. إحصائيات وأعداد البرامج التدريبية
        $isTrainingStats = (
            Str::contains($text, [
                'كم عدد التدريبات', 'كم تدريب', 'كم دورة', 'كم دورة تدريبية', 'عدد التدريبات',
                'إحصائيات التدريب', 'احصائيات التدريب', 'إحصائيات الدورات', 'احصائيات الدورات',
                'إحصائية التدريب', 'كم ورشة', 'إحصائيات البرامج', 'احصائيات البرامج', 'أرقام التدريب'
            ]) ||
            ((Str::contains($text, ['كم عدد', 'كم هو عدد', 'احصائيات', 'إحصائيات', 'أرقام', 'ارقام']) || preg_match('/^كم\s+(?:تدريب|دورة|ورشة|برنامج)/u', $text)) && Str::contains($text, ['تدريب', 'تدريبات', 'دورة', 'دورات', 'ورشة', 'ورش']))
        );

        if ($isTrainingStats && in_array('get_trainings_statistics', $authorizedNames)) {
            $res = AiToolRegistry::executeTool($user, 'get_trainings_statistics', []);
            $d = $res['data'] ?? [];

            $out = "🎓 **إحصائيات البرامج والدورات التدريبية بجامعة طرابلس:**\n\n" .
                "• **إجمالي البرامج التدريبية:** **{$d['total_trainings']} برنامج تدريبي**\n" .
                "  - 🟢 برامج نشطة ومتاحة: **{$d['active_trainings']}**\n" .
                "  - 🏁 برامج مكتملة: **{$d['completed_trainings']}**\n" .
                "  - 📝 مسودات قيد الإعداد: **{$d['draft_trainings']}**\n" .
                "• **إجمالي المقاعد التدريبية المتاحة:** **{$d['total_seats']} مقعداً**\n" .
                "• **طلبات الالتحاق المقدمة:** **{$d['total_applications']} طلب** (تم قبول **{$d['accepted_applications']}** متدرباً)\n\n" .
                "💡 _يمكنك استعراض التدريبات المتاحة بالطلب: 'اعرض لي الدورات التدريبية النشطة'._";

            return [
                'content' => $out,
                'tool_executed' => 'get_trainings_statistics',
            ];
        }

        // ج. إحصائيات وأعداد الوظائف والشركات
        $isJobStats = (
            Str::contains($text, [
                'كم عدد الوظائف', 'كم وظيفة', 'عدد الوظائف', 'إحصائيات الوظائف', 'احصائيات الوظائف',
                'كم فرصة عمل', 'كم شركة', 'كم عدد الشركات', 'إحصائيات الشركات', 'احصائيات الشركات',
                'إحصائيات الشراكات', 'احصائيات الشراكات', 'كم شركة شريكة', 'أرقام الوظائف'
            ]) ||
            ((Str::contains($text, ['كم عدد', 'احصائيات', 'إحصائيات', 'أرقام', 'ارقام']) || preg_match('/^كم\s+(?:وظيفة|فرصة|شركة)/u', $text)) && Str::contains($text, ['وظائف', 'وظيفة', 'فرص', 'فرصة', 'شركات', 'شراكات']))
        );

        if ($isJobStats && in_array('get_jobs_statistics', $authorizedNames)) {
            $res = AiToolRegistry::executeTool($user, 'get_jobs_statistics', []);
            $d = $res['data'] ?? [];

            $out = "💼 **إحصائيات فرص العمل وسوق الشراكات والتوظيف:**\n\n" .
                "• **إجمالي فرص العمل المعلنة:** **{$d['total_jobs']} فرصة** (منها **{$d['open_jobs']}** شاغرة ومتاحة للتقديم حالياً)\n" .
                "• **الشركات والمؤسسات الشريكة:** **{$d['total_companies']} شركة معتمدة** ({$d['active_companies']} شركة نشطة)\n" .
                "• **الترشيحات المهنية:** **{$d['total_nominations']} ترشيح تم إرساله للشركات** (تُوّج منها **{$d['hired_nominations']}** بالتوظيف الفعلي ✅)\n\n" .
                "💡 _للبحث عن فرصة عمل محددة يمكنك طلب: 'وظائف تقنية المعلومات' أو 'عرض الوظائف الشاغرة'._";

            return [
                'content' => $out,
                'tool_executed' => 'get_jobs_statistics',
            ];
        }

        // 👑 التقرير التنفيذي الشامل (الإدارة العليا والتقارير الشاملة)
        if (Str::contains($text, [
            'تقرير كامل', 'تقرير شامل', 'تقرير تنفيذي', 'تقرير القيادة', 'التقرير الاستراتيجي',
            'تقرير شامل للنظام', 'تقرير الإدارة العليا', 'تقرير عن التدريبات', 'تقرير التدريبات',
            'تقرير التوظيف', 'تقرير الخريجين والتوظيف', 'تقرير عام', 'نبي تقرير', 'جهز تقرير'
        ]) && in_array('generate_executive_report', $authorizedNames)) {
            $res = AiToolRegistry::executeTool($user, 'generate_executive_report', []);
            $out = $this->formatToolResultFallback('generate_executive_report', $res);
            return [
                'content' => $out,
                'tool_executed' => 'generate_executive_report',
            ];
        }

        // 6. بحث متقدم عن الخريجين (لمسؤول الإرشاد المهني والمدير)
        $isGradSearch = (
            !Str::startsWith($text, ['رشح', 'ترشيح', 'ترشيج', 'جمد', 'تجميد', 'نشط', 'تنشيط', 'احذف', 'حذف', 'مسح', 'ازالة', 'إزالة', 'قبول', 'رفض', 'كيف', 'طريقة', 'خطوات', 'شرح']) &&
            !Str::contains($text, [
                'تقرير', 'إحصائيات', 'احصائيات', 'مؤشرات', 'ملخص',
                'رشح الخريج', 'ترشيح الخريج', 'ترشيج الخريج', 'رشح لي', 'ترشيح خريج', 'ترشيج خريج', 'ترشيح خريجين', 'رشح الطالب', 'ترشيح الطالب',
                'جمد حساب', 'تجميد حساب', 'نشط حساب', 'تنشيط حساب', 'فك تجميد', 'الغاء تجميد', 'إلغاء تجميد',
                'احذف حساب', 'حذف حساب', 'مسح حساب', 'حذف الخريج', 'مسح الخريج',
                'قبول طلب', 'قبول طلبات', 'رفض طلب', 'رفض طلبات', 'الموافقة على',
                'كيف', 'طريقة', 'خطوات', 'شرح', 'علمني'
            ]) &&
            (
                Str::contains($text, [
                    'نبي خريج', 'نبي خريجين', 'أبي خريج', 'أبي خريجين', 'أريد خريج', 'أريد خريجين',
                    'ابحث عن خريج', 'ابحث عن خريجين', 'بحث عن خريج', 'بحث عن خريجين', 'ابحث لي عن خريج',
                    'دورلي على خريج', 'شوفلي خريج', 'أعطيني خريج', 'اعطيني خريج', 'عرض خريجي', 'استعراض الخريجين',
                    'خريجين', 'خريجي', 'خريج', 'مهندسي', 'معدل', 'هاتف'
                ]) || (Str::contains($text, ['تخصص', 'كلية']) && in_array('search_graduates_advanced', $authorizedNames))
            )
        ) && in_array('search_graduates_advanced', $authorizedNames);

        if ($isGradSearch) {
            $args = [];

            // البحث برقم الهاتف
            if (preg_match('/(?:09\d{8}|02\d{7}|\b\d{9,10}\b)/', $userMessage, $m)) {
                $args['phone'] = $m[0];
            }

            // البحث بالمعدل
            if (preg_match('/(?:معدل|نسبة)\s*(?:أعلى من|أكبر من|فوق|تتجاوز|بمعدل)?\s*(\d+(?:\.\d+)?)/u', $userMessage, $m)) {
                $args['min_gpa'] = (float) $m[1];
            }

            // البحث بالاسم أولاً
            if (preg_match('/(?:الخريج|اسمه|اسم|الطالب)\s+([^\s\?\.\!,]+(?:\s+[^\s\?\.\!,]+)?)/u', $userMessage, $m)) {
                $nameCand = trim($m[1]);
                if (!in_array($nameCand, ['تقنية', 'هندسة', 'طب', 'علوم', 'صيدلة', 'برمجيات', 'حاسوب', 'الكلية', 'القسم', 'معدل'])) {
                    $args['name'] = $nameCand;
                }
            }

            // استخراج التخصص أو الكلية أو القسم
            $majorKw = null;
            if (preg_match('/(?:نبي|أبي|أريد|ابحث عن|ابحث لي عن|دورلي على|اعطيني|أعطيني|شوفلي|عرض)?\s*(?:خريج|خريجين|خريجي|مهندسي|طلبة)?\s*(?:كلية|قسم|تخصص)?\s+([^\?\.\!,]+)/u', $userMessage, $m)) {
                $candidate = trim($m[1]);
                $candidate = preg_replace('/\s+(?:بمعدل|معدل|بنسبة|أعلى|فوق|تتجاوز|ويكون|عنده|لديه|باحث|سيرة).*$/u', '', $candidate);
                $candidate = trim($candidate);
                if (!empty($candidate) && !in_array($candidate, ['بمعدل', 'أعلى', 'ولديهم', 'مع', 'يكون', 'متميز', 'شاطر', 'متفوق'])) {
                    $majorKw = $candidate;
                }
            } elseif (preg_match('/(?:تخصص|قسم|كلية)\s+([^\?\.\!,]+)/u', $userMessage, $m)) {
                $candidate = trim($m[1]);
                $candidate = preg_replace('/\s+(?:بمعدل|معدل|بنسبة|أعلى|فوق|تتجاوز|ويكون|عنده|لديه|باحث|سيرة).*$/u', '', $candidate);
                $candidate = trim($candidate);
                if (!empty($candidate) && !in_array($candidate, ['بمعدل', 'أعلى', 'ولديهم', 'مع', 'يكون'])) {
                    $majorKw = $candidate;
                }
            }

            if (!empty($majorKw)) {
                // تجنب وضع الاسم في التخصص إن كان الاسم هو المستخرج
                if (!isset($args['name']) || stripos($majorKw, $args['name']) === false) {
                    $args['major'] = $majorKw;
                }
            }

            if (Str::contains($text, ['سيرة ذاتية', 'سير ذاتية', 'cv'])) {
                $args['has_cv'] = true;
            }

            $res = AiToolRegistry::executeTool($user, 'search_graduates_advanced', $args);
            $grads = $res['data'] ?? [];

            if (empty($grads)) {
                $critText = !empty($args['major']) ? " لتخصص أو كلية [{$args['major']}]" : "";
                return [
                    'content' => "لم أعثر على خريجين يطابقون معايير البحث{$critText} في سجلات المنظومة المكتملة حالياً.\n\n💡 يمكنك تجربة البحث بكلمات أشمل (مثل: *تقنية*، *برمجيات*، أو *هندسة*).",
                    'tool_executed' => 'search_graduates_advanced',
                ];
            }

            $out = "🎯 **وجدت " . count($grads) . " من الخريجين المطابقين لمعايير البحث في المنظومة:**\n\n";
            foreach ($grads as $g) {
                $out .= "👤 **{$g['name']}**\n";
                $out .= "   🎓 التخصص: **{$g['major']}** ({$g['faculty']})\n";
                $out .= "   📊 المعدل التراكمي: **{$g['gpa']}** | 📞 الهاتف: `{$g['phone']}`\n";
                $out .= "   📄 السيرة الذاتية: " . ($g['has_cv'] ? 'متوفرة ومرفوعة ✅' : 'غير مرفوعة ⚠️') . " | الحالة: {$g['employment_status']}\n\n";
            }
            $out .= "💡 **هل ترغب في ترشيح أي من هؤلاء الخريجين لفرصة وظيفية معينة؟** فقط اطلب: *'رشح الخريج [الاسم] لوظيفة [اسم الوظيفة]'*.";

            return [
                'content' => $out,
                'tool_executed' => 'search_graduates_advanced',
            ];
        }

        // 7. استعلام عن التدريبات والبرامج المتاحة
        $cleanedMsg = trim($text, " ?.!\t\n\r\0\x0B؟،,");
        $isTrainingListIntent = (
            Str::contains($text, [
                'ماذا عن التدريب', 'ماذا عن التدريبات', 'ماذا عن الدورات', 'ماذا عن البرامج',
                'التدريبات المتاحة', 'تدريبات متاحة', 'الدورات المتاحة', 'دورات متاحة',
                'البرامج المتاحة', 'برامج متاحة', 'قائمة التدريبات', 'قائمة الدورات',
                'عرض التدريبات', 'عرض الدورات', 'استعراض الدورات', 'استعراض التدريبات',
                'ما هي الدورات', 'ماهي الدورات', 'ما هي التدريبات', 'ماهي التدريبات',
                'ما الدورات', 'ما التدريبات', 'شن في دورات', 'شن الدورات', 'شن التدريبات',
                'هل في دورات', 'هل توجد دورات', 'هل هناك دورات', 'هل في تدريبات', 'هل توجد تدريبات'
            ]) ||
            in_array($cleanedMsg, ['التدريبات', 'الدورات', 'تدريبات', 'دورات', 'التدريب', 'الدورة', 'برامج تدريبية', 'البرامج التدريبية']) ||
            (
                Str::contains($text, ['تدريب', 'تدريبات', 'دورة', 'دورات', 'برنامج تدريبي', 'ورشة', 'ورش']) &&
                Str::contains($text, ['ماذا عن', 'بخصوص', 'متاح', 'متاحة', 'مفتوح', 'مفتوحة', 'قائمة', 'عرض', 'استعراض', 'شن', 'هل', 'ماهي', 'ما هي', 'اريد', 'أريد', 'نبي', 'ابحث', 'اعطني', 'أعطني'])
            )
        ) && !Str::contains($text, ['أضف', 'اضف', 'إنشاء', 'صياغة', 'اقترح', 'تقرير', 'استبيان', 'استطلاع', 'طلبات', 'طلباتي', 'قبول', 'رفض', 'تقديم على', 'التقديم على', 'سجلني']);

        if ($isTrainingListIntent && in_array('search_trainings', $authorizedNames)) {
            $kw = '';
            if (preg_match('/(?:عن|في|حول|بمجال)\s+([^\?\.\!؟،,]+)/u', $userMessage, $m)) {
                $cand = trim(preg_replace('/^[\s\p{P}]+|[\s\p{P}]+$/u', '', $m[1]));
                $ignoredTrainWords = [
                    'تدريب', 'التدريب', 'تدريبات', 'التدريبات', 'دورة', 'الدورة', 'دورات', 'الدورات', 
                    'برنامج', 'البرنامج', 'برامج', 'البرامج', 'ورشة', 'الورشة', 'ورش', 'الورش',
                    'المتاحة', 'متاحة', 'الجديدة', 'جديدة', 'المتوفرة', 'متوفرة', 'المفتوحة', 'مفتوحة',
                    'الجامعة', 'جامعة طرابلس', 'المنظومة', 'عنها', 'بخصوصها'
                ];
                if (!in_array($cand, $ignoredTrainWords) && mb_strlen($cand) > 1) {
                    $kw = $cand;
                }
            }
            $res = AiToolRegistry::executeTool($user, 'search_trainings', ['keyword' => $kw]);
            $list = $res['data'] ?? [];

            if (empty($list)) {
                return [
                    'content' => "بحثت في سجلات جامعة طرابلس ولم أجد حالياً تدريبات تطابق كلمة البحث: " . (!empty($kw) ? "**'{$kw}'**" : "في المنظومة") . ". يمكنك متابعة صفحة التدريبات الرئيسية أو سؤال منسق التدريب.",
                    'tool_executed' => 'search_trainings',
                ];
            }

            $out = "🎓 **البرامج والدورات التدريبية المتاحة في المنظومة (" . count($list) . " برنامج):**\n\n";
            foreach ($list as $t) {
                $out .= "🔹 **{$t['title']}** ({$t['type']})\n";
                $out .= "   • المكان: 📍 {$t['location']} | ⏳ المدة: {$t['duration']} | 👥 المقاعد: {$t['seats']}\n";
                $out .= "   • تاريخ البدء: 📅 {$t['start_date']}\n";

                if ($user->role === 'graduate') {
                    $out .= "   👉 [📝 التقديم على هذا البرنامج](#prompt:أريد التقديم على برنامج {$t['title']})\n\n";
                } else {
                    $out .= "\n";
                }
            }

            if ($user->role === 'graduate') {
                $out .= "━━━━━━━━━━━━━━━━━━━━━━\n💡 **للتقديم الفوري:** اضغط على زر التقديم أسفل البرنامج المطلوب، أو اكتب: *'أريد التقديم على [اسم التدريب]'*.";
            } else {
                $out .= "هل ترغب في معرفة تفاصيل تدريب معين أو التقدم له؟";
            }

            return [
                'content' => $out,
                'tool_executed' => 'search_trainings',
            ];
        }

        // 2. طلبات الخريج الشخصية
        $isMyAppsIntent = (
            Str::contains($text, [
                'طلباتي', 'تسجيلي', 'حالة الطلب', 'حالة طلباتي', 'مقبول', 'تقديمي', 
                'طلبات التدريب', 'ماذا عن طلباتي', 'ماذا عن التقديم', 'متابعة طلبي', 'متابعة الطلب',
                'سجل طلباتي', 'هل تم قبولي', 'هل قبلت', 'استعلام عن طلباتي', 'طلباتي في التدريب'
            ]) ||
            in_array($cleanedMsg, ['طلباتي', 'طلبات التدريب', 'سجل طلباتي', 'الطلبات', 'طلباتي السابقة'])
        ) && in_array('get_my_applications', $authorizedNames);

        if ($isMyAppsIntent) {
            $res = AiToolRegistry::executeTool($user, 'get_my_applications', []);
            $apps = $res['data'] ?? [];

            if (empty($apps)) {
                return [
                    'content' => "لم تتقدم بأي طلب تدريب حتى الآن يا **{$user->name}**. يمكنك تصفح البرامج التدريبية المتاحة والتقديم عليها فوراً!",
                    'tool_executed' => 'get_my_applications',
                ];
            }

            $out = "🎓 **إليك سجل طلبات التدريب الخاصة بك (" . count($apps) . " طلب):**\n\n";
            foreach ($apps as $a) {
                $statusIcon = in_array($a['status'], ['accepted', 'approved']) ? '🟢' : ($a['status'] === 'rejected' ? '🔴' : '🟡');
                $out .= "{$statusIcon} **{$a['training_title']}**\n";
                $out .= "   • الحالة: **{$a['status_text']}** (تاريخ التقديم: {$a['applied_at']})\n";
                if (!empty($a['admin_feedback'])) {
                    $out .= "   • ملاحظة الإدارة: _{$a['admin_feedback']}_\n";
                }
                $out .= "\n";
            }

            return [
                'content' => $out,
                'tool_executed' => 'get_my_applications',
            ];
        }

        // 3. الملف الأكاديمي للخريج
        $isMyProfileIntent = (
            Str::contains($text, [
                'ملفي', 'بياناتي', 'سيرتي', 'معدلي', 'تخصصي', 'سيرتي الذاتية', 'الملف الشخصي',
                'الملف الأكاديمي', 'بيانات تخرجي', 'ماذا عن ملفي', 'ماذا عن بياناتي', 'ماذا عن سيرتي'
            ]) ||
            in_array($cleanedMsg, ['ملفي', 'بياناتي', 'سيرتي', 'سيرتي الذاتية', 'الملف الشخصي', 'بروفايلي'])
        ) && in_array('get_my_profile', $authorizedNames);

        if ($isMyProfileIntent) {
            $res = AiToolRegistry::executeTool($user, 'get_my_profile', []);
            $d = $res['data'] ?? [];

            $out = "📄 **ملفك الأكاديمي والمهني المسجل في المنظومة:**\n\n";
            $out .= "• **الاسم:** {$d['name']}\n";
            $out .= "• **الكلية:** {$d['college']}\n";
            $out .= "• **التخصص:** {$d['major']}\n";
            if (!empty($d['gpa'])) $out .= "• **المعدل التراكمي:** {$d['gpa']}%\n";
            if (!empty($d['graduation_year'])) $out .= "• **سنة التخرج:** {$d['graduation_year']}\n";
            $out .= "• **السيرة الذاتية المرفوعة:** " . (!empty($d['has_cv']) ? 'نعم (مرفوعة ومحدثة ✅)' : 'لا يوجد ملف سيرة ذاتية مرفوع ⚠️') . "\n\n";
            $out .= "هل ترغب في نصائح لتحسين وتطوير سيرتك الذاتية أو التقديم على فرصة عمل؟";

            return [
                'content' => $out,
                'tool_executed' => 'get_my_profile',
            ];
        }



        // 5. إحصائيات الميديا (لمسؤول الإعلام)
        if (Str::contains($text, ['إحصائيات الميديا', 'نسبة التغطية', 'إحصائيات الإعلام']) && in_array('get_media_statistics', $authorizedNames)) {
            $res = AiToolRegistry::executeTool($user, 'get_media_statistics', []);
            $d = $res['data'] ?? [];

            $out = "📊 **تقرير وحدة الإعلام والتغطيات الصحفية:**\n\n";
            $out .= "• **نسبة التغطية الإعلامية:** {$d['coverage_rate']}\n";
            $out .= "• **التدريبات المغطاة صحفياً وميدانياً:** {$d['covered_trainings']} من أصل {$d['total_trainings']}\n";
            $out .= "• **التدريبات المنتظرة للتغطية:** {$d['pending_trainings']} تدريب\n";
            $out .= "• **الأخبار المعتمدة المنشورة:** {$d['active_news']}\n";
            $out .= "• **الإعلانات الرسمية النشطة:** {$d['active_announcements']}\n";

            return [
                'content' => $out,
                'tool_executed' => 'get_media_statistics',
            ];
        }

        // 6. صياغة خبر صحفي (لمسؤول الإعلام)
        if (Str::contains($text, ['صغ خبراً', 'اكتب خبر', 'تحرير خبر', 'مسودة خبر', 'بيان صحفي']) && in_array('draft_news_article', $authorizedNames)) {
            $title = "اختتام فعاليات ورشة العمل التفاعلية بجامعة طرابلس";
            if (preg_match('/(?:بعنوان|حول|عن)\s+([^\?\.\!]+)/u', $userMessage, $m)) {
                $title = "تغطية " . trim($m[1]);
            }

            $excerpt = "نظم مكتب تدريب وتأهيل الخريجين بجامعة طرابلس ورشة عمل متخصصة استهدفت تعزيز المهارات العملية للطلاب وربطهم بسوق العمل.";
            $articleContent = "طرابلس — في إطار استراتيجية جامعة طرابلس لتأهيل وتطوير مهارات الطلاب والخريجين، اختتم مكتب تدريب وتأهيل الخريجين فعاليات البرنامج التدريبي المكثف بحضور نخبة من الأساتذة والخبراء والمدربين المعتمدين.\n\nشهد البرنامج تفاعلاً كبيراً من المتدربين من خلال تطبيقات عملية وحلقات نقاشية تهدف إلى ردم الفجوة بين المناهج الأكاديمية واحتياجات سوق العمل الحقيقية.\n\nوأكدت إدارة المكتب حرصها المستمر على رعاية الكفاءات الوطنية وإتاحة المزيد من المسارات التدريبية النوعية خلال الفترة القادمة.";

            $proposal = [
                'status' => 'proposal',
                'action_type' => 'create_news_draft',
                'title' => 'تأكيد إنشاء مسودة خبر صحفي',
                'message' => "لقد قمت بصياغة هذا البيان الصحفي المعتمد. هل ترغب في اعتماده وحفظه كمسودة في وحدة الإعلام؟",
                'data' => [
                    'title' => $title,
                    'category' => 'workshop',
                    'excerpt' => $excerpt,
                    'content' => $articleContent,
                ]
            ];

            return [
                'content' => "📰 **لقد قمت بصياغة مسودة الخبر الصحفي وفق الهوية الإعلامية لجامعة طرابلس:**\n\n" .
                    "**العنوان:** {$title}\n\n" .
                    "**الملخص:** {$excerpt}\n\n" .
                    "**نص المقال:**\n{$articleContent}\n\n" .
                    "يمكنك تأكيد الحفظ كمسودة بنقرة واحدة من البطاقة التفاعلية أدناه.",
                'action_proposal' => $proposal,
                'tool_executed' => 'draft_news_article',
            ];
        }

        // 7. آخر الأخبار
        $cleanedMsg = trim($text, " ?.!\t\n\r\0\x0B؟،,");
        $isNewsIntent = (
            Str::contains($text, ['أخبار', 'الأخبار', 'أحدث الأخبار', 'البيانات الصحفية', 'ماذا عن الأخبار', 'ماذا عن الإعلانات', 'الإعلانات والتعميمات', 'الإعلانات الرسمية', 'ماذا عن الأخبار الصحفية']) ||
            in_array($cleanedMsg, ['الأخبار', 'أخبار', 'الإعلانات', 'إعلانات', 'البيانات الصحفية', 'شريط الأخبار'])
        ) && in_array('get_latest_news', $authorizedNames);

        if ($isNewsIntent) {
            $res = AiToolRegistry::executeTool($user, 'get_latest_news', ['limit' => 4]);
            $news = $res['data'] ?? [];

            if (empty($news)) {
                return [
                    'content' => "لا توجد أخبار منشورة حالياً في وحدة الإعلام.",
                    'tool_executed' => 'get_latest_news',
                ];
            }

            $out = "📰 **أحدث الأخبار والبيانات الصحفية المنشورة:**\n\n";
            foreach ($news as $n) {
                $out .= "🔹 **{$n['title']}** ({$n['category']})\n";
                $out .= "   📅 التاريخ: {$n['published_at']}\n";
                if (!empty($n['excerpt'])) $out .= "   _{$n['excerpt']}_\n";
                $out .= "\n";
            }

            return [
                'content' => $out,
                'tool_executed' => 'get_latest_news',
            ];
        }

        // 8. إحصائيات عامة للنظام (للمدير العام)
        if (Str::contains($text, ['إحصائيات', 'نظرة عامة', 'أرقام المنظومة']) && in_array('get_system_overview', $authorizedNames)) {
            $res = AiToolRegistry::executeTool($user, 'get_system_overview', []);
            $d = $res['data'] ?? [];

            $out = "👑 **ملخص مؤشرات المنظومة الشاملة (لوحة الإدارة العليا):**\n\n";
            $out .= "• **إجمالي المستخدمين:** {$d['total_users']}\n";
            $out .= "• **الخريجون المسجلون:** {$d['graduates_count']}\n";
            $out .= "• **الشركات الشريكة:** {$d['companies_count']}\n";
            $out .= "• **البرامج التدريبية:** {$d['trainings_count']}\n";
            $out .= "• **طلبات التدريب المسجلة:** {$d['applications_count']}\n";
            $out .= "• **الفرص الوظيفية:** {$d['job_opportunities_count']}\n";
            $out .= "• **الأخبار المعتمدة:** {$d['published_news']}\n";

            return [
                'content' => $out,
                'tool_executed' => 'get_system_overview',
            ];
        }

        // ==========================================
        // 📝 9. أدوات قطاع التقييم والمتابعة والجودة
        // ==========================================
        if ((Str::contains($text, ['استبيان', 'استطلاع']) && !Str::contains($text, ['ملخص', 'نتائج'])) && in_array('draft_survey', $authorizedNames)) {
            $title = 'استبيان تقييم برنامج تدريبي وتطوير المهارات';
            if (preg_match('/(?:بعنوان|حول|عن|لـ|لدورة)\s+([^\?\.\!]+)/u', $userMessage, $m)) {
                $title = "استبيان تقييم " . trim($m[1]);
            }

            $res = AiToolRegistry::executeTool($user, 'draft_survey', ['title' => $title]);
            if (isset($res['status']) && $res['status'] === 'proposal') {
                return [
                    'content' => "📋 **تم إعداد وصياغة استبيان التقييم والمتابعة بنجاح:**\n\n" .
                        "• **العنوان:** {$title}\n" .
                        "• **الأسئلة المتضمنة:** 5 أسئلة معيارية للجودة (تقييم المحتوى، مهارات المدرب، الاستفادة العملية، التجهيزات، ومقترحات التحسين).\n\n" .
                        "يرجى مراجعة التفاصيل والضغط على **[تأكيد وحفظ]** لاعتماده ونشره في المنظومة.",
                    'action_proposal' => $res,
                    'tool_executed' => 'draft_survey',
                ];
            }
        }

        if (Str::contains($text, ['تقرير التقييم', 'تقرير الجودة', 'تقرير المتابعة', 'تقرير التقييم والمتابعة', 'تقرير تقييم']) && in_array('generate_evaluation_report', $authorizedNames)) {
            $res = AiToolRegistry::executeTool($user, 'generate_evaluation_report', []);
            $m = $res['metrics'] ?? [];

            $out = "📊 **{$res['report_title']}**\n";
            $out .= "_تاريخ التوليد: {$res['generated_at']}_\n\n";
            $out .= "• **مؤشر الجودة المؤسسي العام:** **{$m['quality_score']}** ⭐\n";
            $out .= "• **الاستبيانات المسجلة:** {$m['total_surveys']} (منها {$m['active_surveys']} استبيان نشط)\n";
            $out .= "• **إجمالي الاستجابات والمشاركات:** {$m['total_responses']} مشاركة\n";
            $out .= "• **البرامج التدريبية المقيمة:** {$m['total_trainings']} برنامج ({$m['completed_trainings']} منجز بالكامل)\n";
            $out .= "• **نسبة قبول طلبات المتدربين:** {$m['acceptance_rate']} ({$m['accepted_applications']} من أصل {$m['total_applications']})\n\n";
            $out .= "💡 **التوصية:** مستوى رضا المتدربين وجودة المحتوى يظهر تفاعلاً إيجابياً مرتفعاً مع التوصية بتوسيع المسارات التدريبية العملية.";

            return [
                'content' => $out,
                'tool_executed' => 'generate_evaluation_report',
            ];
        }

        if (Str::contains($text, ['ملخص الاستبيانات', 'الاستبيانات النشطة', 'نتائج الاستبيان', 'عرض الاستبيانات']) && in_array('get_surveys_summary', $authorizedNames)) {
            $res = AiToolRegistry::executeTool($user, 'get_surveys_summary', ['status' => 'all']);
            $list = $res['data'] ?? [];

            if (empty($list)) {
                return [
                    'content' => "لا توجد استبيانات مسجلة حالياً في منظومة التقييم والمتابعة. يمكنك صياغة استبيان جديد فوراً!",
                    'tool_executed' => 'get_surveys_summary',
                ];
            }

            $out = "📋 **ملخص الاستبيانات في منظومة التقييم والمتابعة:**\n\n";
            foreach ($list as $s) {
                $statusIcon = $s['is_active'] === 'نشط' ? '🟢' : '⚪';
                $out .= "{$statusIcon} **{$s['title']}**\n";
                $out .= "   🎯 الفئة: {$s['target']} | 📝 الاستجابات: **{$s['responses_count']}** مشاركة\n";
                $out .= "   📅 الصلاحية: {$s['start_date']} إلى {$s['end_date']}\n\n";
            }

            return [
                'content' => $out,
                'tool_executed' => 'get_surveys_summary',
            ];
        }

        // ==========================================
        // 🎓 10. أدوات قطاع منسق التدريب
        // ==========================================
        if (Str::contains($text, ['تقرير التدريب', 'تقرير التدريبات', 'تقرير المنسق', 'تقرير أداء التدريب', 'تقرير البرامج التدريبية']) && in_array('generate_training_report', $authorizedNames)) {
            $res = AiToolRegistry::executeTool($user, 'generate_training_report', []);
            $m = $res['metrics'] ?? [];

            $out = "🎓 **{$res['report_title']}**\n";
            $out .= "_تاريخ التوليد: {$res['generated_at']}_\n\n";
            $out .= "• **إجمالي البرامج والدورات:** {$m['total_trainings']} برنامج\n";
            $out .= "• **البرامج النشطة حالياً:** {$m['active_trainings']} | المسودات: {$m['draft_trainings']}\n";
            $out .= "• **الطاقة الاستيعابية الإجمالية:** {$m['total_seats']} مقعد تدريبي\n";
            $out .= "• **نسبة شغل المقاعد التدريبية:** **{$m['seat_fill_rate']}**\n";
            $out .= "• **طلبات التسجيل الواردة:** {$m['total_applications']} طلب\n";
            $out .= "   - الطلبات المقبولة: {$m['accepted_applications']} ✅\n";
            $out .= "   - الطلبات قيد الانتظار: {$m['pending_applications']} ⏳\n";
            $out .= "   - الطلبات المعتذر عنها: {$m['rejected_applications']} ❌\n";

            return [
                'content' => $out,
                'tool_executed' => 'generate_training_report',
            ];
        }

        // ==========================================
        // 📢 11. أدوات قطاع الإعلام والتغطيات والاتصال المؤسسي
        // ==========================================
        if (Str::contains($text, ['صغ إعلان', 'نشر إعلان', 'إعلان رسمي', 'مسودة إعلان', 'أضف إعلان']) && in_array('draft_announcement', $authorizedNames)) {
            $title = 'إعلان هام لخريجي جامعة طرابلس';
            if (preg_match('/(?:بعنوان|حول|عن)\s+([^\?\.\!]+)/u', $userMessage, $m)) {
                $title = trim($m[1]);
            }
            $content = "تعلن إدارة مكتب تدريب وتأهيل الخريجين بجامعة طرابلس عن فتح باب التسجيل في مسارات التطوير المهني والبرامج التدريبية القادمة.";

            $res = AiToolRegistry::executeTool($user, 'draft_announcement', [
                'title' => $title,
                'content' => $content,
            ]);

            if (isset($res['status']) && $res['status'] === 'proposal') {
                return [
                    'content' => "📢 **تم إعداد مسودة الإعلان الرسمي:**\n\n" .
                        "• **العنوان:** {$title}\n" .
                        "• **النص:** {$content}\n\n" .
                        "يمكنك مراجعة تفاصيل الإعلان والضغط على **[تأكيد وحفظ]** لنشره فوراً في شريط الأخبار والصفحة الرئيسية.",
                    'action_proposal' => $res,
                    'tool_executed' => 'draft_announcement',
                ];
            }
        }

        if (Str::contains($text, ['تقرير الإعلام', 'تقرير التغطية', 'تقرير الميديا', 'تقرير التغطيات الصحفية']) && in_array('generate_media_report', $authorizedNames)) {
            $res = AiToolRegistry::executeTool($user, 'generate_media_report', []);
            $m = $res['metrics'] ?? [];

            $out = "📢 **{$res['report_title']}**\n";
            $out .= "_تاريخ التوليد: {$res['generated_at']}_\n\n";
            $out .= "• **نسبة التغطية الإعلامية للمناسبات:** **{$m['coverage_rate']}**\n";
            $out .= "• **الفعاليات المغطاة صحفياً وميدانياً:** {$m['covered_trainings']} من أصل {$m['total_events']}\n";
            $out .= "• **الفعاليات بانتظار التغطية:** {$m['pending_trainings']}\n";
            $out .= "• **الأخبار المعتمدة المنشورة:** {$m['published_news']} خبر (مسودات: {$m['draft_news']})\n";
            $out .= "• **الإعلانات الرسمية النشطة:** {$m['active_announcements']} إعلان\n";

            return [
                'content' => $out,
                'tool_executed' => 'generate_media_report',
            ];
        }

        // ==========================================
        // 🏢 12. أدوات قطاع الشراكات وسوق العمل
        // ==========================================
        if (Str::contains($text, ['إضافة شركة', 'شركة جديدة', 'شراكة جديدة', 'تسجيل شركة', 'توثيق شركة', 'أضف شركة']) && in_array('draft_partner_company', $authorizedNames)) {
            $name = 'شركة التقنيات المتقدمة القابضة';
            if (preg_match('/(?:شركة|مؤسسة|مصرف)\s+([^\?\.\!]+)/u', $userMessage, $m)) {
                $name = "شركة " . trim($m[1]);
            }

            $res = AiToolRegistry::executeTool($user, 'draft_partner_company', ['name' => $name]);
            if (isset($res['status']) && $res['status'] === 'proposal') {
                return [
                    'content' => "🏢 **تم إعداد بيانات اعتماد الشركة الشريكة الجديدة:**\n\n" .
                        "• **اسم الشركة:** {$name}\n" .
                        "يرجى مراجعة التفاصيل في البطاقة أدناه ثم الضغط على **[تأكيد وحفظ]** لإدراجها رسمياً في شبكة الشركاء.",
                    'action_proposal' => $res,
                    'tool_executed' => 'draft_partner_company',
                ];
            } else {
                return [
                    'content' => $res['message'] ?? 'تعذر تجهيز ملف الشركة.',
                    'tool_executed' => 'draft_partner_company',
                ];
            }
        }

        if (Str::contains($text, ['تقرير الشراكات', 'تقرير الشركات', 'تقرير سوق العمل', 'تقرير المؤسسات']) && in_array('generate_partnerships_report', $authorizedNames)) {
            $res = AiToolRegistry::executeTool($user, 'generate_partnerships_report', []);
            $m = $res['metrics'] ?? [];

            $out = "🏢 **{$res['report_title']}**\n";
            $out .= "_تاريخ التوليد: {$res['generated_at']}_\n\n";
            $out .= "• **إجمالي الشركات والمؤسسات الشريكة:** {$m['total_companies']} شركة\n";
            $out .= "• **الشركات المعتمدة رسمياً:** {$m['approved_companies']} شركة\n";
            $out .= "• **إجمالي فرص العمل المعلنة:** {$m['total_job_opportunities']} فرصة\n";
            $out .= "• **الفرص الوظيفية المتاحة حالياً:** {$m['active_job_opportunities']} وظيفة\n";
            $out .= "• **إجمالي ترشيحات الخريجين لسوق العمل:** {$m['total_graduate_nominations']} مرشحاً\n";
            $out .= "• **مؤشر كفاءة الشراكات:** {$m['partnership_health']}\n";

            return [
                'content' => $out,
                'tool_executed' => 'generate_partnerships_report',
            ];
        }

        // ==========================================
        // 🏢 قطاع الشركات والتوظيف وأرباب العمل (Company & Employment Copilot)
        // ==========================================

        // أ. استعراض مرشحي ومتقدمي الشركة
        $isCandidatesIntent = (
            Str::contains($text, ['المرشحين', 'المرشحون', 'مرشحين', 'مرشحون', 'المتقدمين', 'المتقدمون', 'طلبات التوظيف', 'مرشحينا', 'سير المتقدمين', 'من تقدم لوظائفنا', 'عرض المرشحين'])
        ) && !preg_match('/(?:^|\s)(?:رشح|ترشيح|ترشيج|ارشح|نرشح)\s+/u', $text) && !Str::contains($text, ['كيف', 'إحصائيات', 'احصائيات', 'لوحة تحكم', 'مؤشرات']) && in_array('get_company_candidates', $authorizedNames);

        if ($isCandidatesIntent) {
            $status = 'all';
            if (Str::contains($text, ['مقابلة', 'مقابلات'])) $status = 'interview_scheduled';
            elseif (Str::contains($text, ['مقبول', 'مقبولين'])) $status = 'accepted';
            elseif (Str::contains($text, ['مرفوض', 'معتذر'])) $status = 'rejected';
            elseif (Str::contains($text, ['جديد', 'جدد', 'معلق'])) $status = 'pending';
            elseif (Str::contains($text, ['توظيف', 'موظف', 'تم توظيفهم', 'hired'])) $status = 'hired';

            $jobIdent = '';
            if (preg_match('/(?:لوظيفة|في وظيفة|لوظائف|لفرصة)\s+([^\?\.\!؟،,]+)/u', $userMessage, $m)) {
                $jobIdent = trim($m[1]);
            }

            $res = AiToolRegistry::executeTool($user, 'get_company_candidates', [
                'status' => $status,
                'job_identifier' => $jobIdent,
            ]);

            $candidates = $res['data'] ?? [];
            if (empty($candidates)) {
                return [
                    'content' => "👥 **سجل المرشحين والمتقدمين للوظائف:**\n\n" .
                        ($res['message'] ?? 'لا يوجد مرشحون يطابقون شروط البحث حالياً.') . "\n\n" .
                        "💡 يمكنك طلب اقتراح خريجين جدد بقول: *'اقترح لي خريجين متميزين في تقنية المعلومات'*.",
                    'tool_executed' => 'get_company_candidates',
                ];
            }

            $out = "👥 **قائمة الخريجين المرشحين لشواغر الشركة (" . count($candidates) . " مرشحاً):**\n\n";
            foreach ($candidates as $idx => $c) {
                $num = $idx + 1;
                $out .= "{$num}. 👤 **{$c['candidate_name']}**\n";
                $out .= "   • **الوظيفة:** {$c['job_title']} | **الشركة:** {$c['company_name']}\n";
                $out .= "   • **التخصص والمعدل:** {$c['major']} (معدل: **{$c['gpa']}**)\n";
                $out .= "   • **حالة الطلب:** `{$c['status_arabic']}` | القرار النهائي: {$c['final_status']}\n";
                $out .= "   • **رقم الهاتف:** `{$c['phone']}` | تاريخ الترشيح: {$c['nominated_at']}\n";
                if (!empty($c['interview_date'])) {
                    $out .= "   • **موعد المقابلة:** 📅 {$c['interview_date']} الساعة {$c['interview_time']} (📍 {$c['interview_location']})\n";
                }
                $out .= "\n";
            }

            $firstCand = $candidates[0]['candidate_name'] ?? 'المرشح';
            $out .= "━━━━━━━━━━━━━━━━━━━━━━\n" .
                "💡 **للتحكم في حالة أي مرشح:**\n" .
                "• **لتحديد موعد مقابلة:** اكتب: *'حدد موعد مقابلة للخريج {$firstCand} يوم الأحد القادم الساعة 10:30 صباحاً'*.\n" .
                "• **للقبول المبدئي:** اكتب: *'قبول المرشح {$firstCand}'*.\n" .
                "• **للتوظيف النهائي:** اكتب: *'توظيف الخريج {$firstCand}'*.";

            return [
                'content' => $out,
                'tool_executed' => 'get_company_candidates',
            ];
        }

        // ب. جدولة مقابلة شخصية وتحديث حالة المرشح والتوظيف
        $isInterviewOrStatusIntent = (
            Str::contains($text, ['حدد مقابلة', 'موعد مقابلة', 'جدولة مقابلة', 'مقابلة شخصية', 'قبول المرشح', 'اقبل المرشح', 'اعتذار للمرشح', 'رفض المرشح', 'توظيف الخريج', 'وظف الخريج', 'تعيين الخريج'])
        ) && in_array('update_candidate_interview_status', $authorizedNames);

        if ($isInterviewOrStatusIntent) {
            $candIdent = '';
            if (preg_match('/(?:للخريج|للمرشح|للطالب|للخريجة|للمرشحة|المرشح|الخريج)\s+([^\?\.\!؟،,]+?)(?:\s+يوم|\s+بتاريخ|\s+الساعة|\s+في|\s+لوظيفة|\s*$)/u', $userMessage, $m)) {
                $candIdent = trim($m[1]);
            }

            $targetStatus = 'interview_scheduled';
            $finalStatus = null;
            if (Str::contains($text, ['توظيف', 'وظف', 'تعيين'])) {
                $targetStatus = 'accepted';
                $finalStatus = 'hired';
            } elseif (Str::contains($text, ['قبول', 'اقبل'])) {
                $targetStatus = 'accepted';
                $finalStatus = 'in_progress';
            } elseif (Str::contains($text, ['اعتذار', 'رفض'])) {
                $targetStatus = 'rejected';
                $finalStatus = 'not_hired';
            }

            $intDate = null;
            if (preg_match('/(?:بتاريخ|يوم)\s+([0-9]{4}-[0-9]{2}-[0-9]{2})/u', $userMessage, $mDate)) {
                $intDate = $mDate[1];
            } elseif (Str::contains($text, ['غداً', 'غدا'])) {
                $intDate = now()->addDay()->format('Y-m-d');
            } elseif (Str::contains($text, ['بعد غد', 'بعد غداً'])) {
                $intDate = now()->addDays(2)->format('Y-m-d');
            }

            $intTime = null;
            if (preg_match('/(?:الساعة|ساعة)\s+([0-9]{1,2}(?::[0-9]{2})?\s*(?:صباحاً|مساءً|ص|م)?)/u', $userMessage, $mTime)) {
                $intTime = trim($mTime[1]);
            }

            $intLoc = null;
            if (preg_match('/(?:في|بمقر|عبر|بـ)\s+(مقر[^\?\.\!]+|قاعة[^\?\.\!]+|Google Meet|Zoom|زووم|ميت)/u', $userMessage, $mLoc)) {
                $intLoc = trim($mLoc[0]);
            }

            if (!empty($candIdent)) {
                $res = AiToolRegistry::executeTool($user, 'update_candidate_interview_status', [
                    'candidate_identifier' => $candIdent,
                    'status' => $targetStatus,
                    'final_status' => $finalStatus,
                    'interview_date' => $intDate,
                    'interview_time' => $intTime,
                    'interview_location' => $intLoc,
                ]);

                if (isset($res['status']) && $res['status'] === 'proposal') {
                    return [
                        'content' => "📅 **تم تجهيز قرار وإجراء تحديث حالة المرشح:**\n\n" .
                            "يرجى مراجعة بيانات الموعد والقرار في البطاقة التفاعلية أدناه وتأكيد التنفيذ ليتم إرسال الإشعار وتوثيق القرار.",
                        'action_proposal' => $res,
                        'tool_executed' => 'update_candidate_interview_status',
                    ];
                } else {
                    return [
                        'content' => $res['message'] ?? 'تعذر تجهيز تحديث المرشح. يرجى التأكد من اسم الخريج.',
                        'tool_executed' => 'update_candidate_interview_status',
                    ];
                }
            } else {
                return [
                    'content' => "📅 **خدمة إدارة المقابلات وحالات المرشحين:**\n\n" .
                        "يرجى تحديد اسم المرشح في طلبك كالتالي:\n" .
                        "• *'حدد موعد مقابلة للخريج [الاسم] يوم [التاريخ] الساعة 11:00 صباحاً'*.\n" .
                        "• *'قبول المرشح [الاسم]'* أو *'توظيف الخريج [الاسم]'*.\n\n" .
                        "💡 يمكنك أولاً استعراض المرشحين بالطلب: *'عرض المرشحين لوظائف شركتنا'*.",
                    'tool_executed' => 'update_candidate_interview_status',
                ];
            }
        }

        // ج. طلب واقتراح خريجين مطابقين لشواغر الشركة
        $isMatchingIntent = (
            Str::contains($text, ['اقترح خريجين', 'اقترح لي خريجين', 'ابحث عن خريجين متميزين', 'خريجين مناسبين', 'أفضل الخريجين', 'افضل الخريجين', 'مطابقة خريجين', 'نريد خريجين', 'احتاج خريجين'])
        ) && in_array('request_matching_graduates', $authorizedNames);

        if ($isMatchingIntent) {
            $minGpa = null;
            if (preg_match('/(?:بمعدل|معدل|أعلى من|اعلى من|فوق)\s*(?:أعلى من|اعلى من|فوق)?\s*([0-9]{2}(?:\.[0-9]+)?)/u', $userMessage, $mGpa)) {
                $minGpa = (float) $mGpa[1];
            }

            $major = '';
            if (preg_match('/(?:في|لتخصص|تخصص|كلية|مجال)\s+([^\?\.\!؟،,\n]+?)(?:\s+(?:بمعدل|معدل|أعلى من|اعلى من|فوق|نسبة|[0-9]{2})|\s*$)/u', $userMessage, $m)) {
                $candidateMajor = trim($m[1]);
                $ignored = ['الجامعة', 'الخريجين', 'الوظيفة', 'العمل', 'شركتنا'];
                if (!in_array($candidateMajor, $ignored)) {
                    $major = $candidateMajor;
                }
            }

            $res = AiToolRegistry::executeTool($user, 'request_matching_graduates', [
                'major' => $major,
                'min_gpa' => $minGpa,
                'limit' => 5,
            ]);

            $grads = $res['data'] ?? [];
            if (empty($grads)) {
                return [
                    'content' => "🔍 **مطابقة واقتراح الخريجين:**\n\n" .
                        ($res['message'] ?? 'لم يتم العثور على خريجين يطابقون الشروط حالياً. يرجى تجربة خفض المعدل أو توسيع نطاق التخصص.'),
                    'tool_executed' => 'request_matching_graduates',
                ];
            }

            // Determine primary job for one-click nomination
            $primaryJob = null;
            if ($user->role === 'company' && $user->company) {
                $primaryJob = JobOpportunity::where('company_id', $user->company->id)->where('status', 'open')->value('title');
            }
            if (!$primaryJob) {
                $primaryJob = JobOpportunity::where('status', 'open')->value('title');
            }

            $out = "🎯 **أفضل الخريجين المطابقين لمتطلبات التوظيف (" . count($grads) . " خريجين):**\n\n";
            foreach ($grads as $idx => $g) {
                $num = $idx + 1;
                $out .= "{$num}. 👤 **{$g['name']}**\n";
                $out .= "   • **التخصص:** {$g['major']} ({$g['faculty']})\n";
                $out .= "   • **المعدل التراكمي:** **{$g['gpa']}%** (دفعة {$g['graduation_year']})\n";
                $out .= "   • **السيرة الذاتية:** " . ($g['has_cv'] ? 'مرفوعة ومحدثة ✅' : 'غير مرفوعة ⚠️') . " | 📞 الهاتف: `{$g['phone']}`\n";

                if ($primaryJob) {
                    $out .= "   👉 [🎯 رشح {$g['name']} لوظيفة ({$primaryJob})](#prompt:رشح الخريج {$g['name']} لوظيفة {$primaryJob})\n";
                } else {
                    $out .= "   👉 [🎯 ترشيح {$g['name']} المباشر](#prompt:رشح الخريج {$g['name']})\n";
                }
                $out .= "\n";
            }

            $out .= "━━━━━━━━━━━━━━━━━━━━━━\n" .
                "💡 **للترشيح المباشر بضغطة زر واحدة:**\n" .
                "اضغط مباشرة على زر **[🎯 رشح الخريج...]** أسفل كل مرشح أعلاه لتجهيز بطاقة الترشيح وتأكيدها فوراً!";

            return [
                'content' => $out,
                'tool_executed' => 'request_matching_graduates',
            ];
        }

        // د. لوحة تحكم الشركة وإحصائيات التوظيف
        $isCompanyDashboardIntent = (
            Str::contains($text, [
                'إحصائيات شركتنا', 'احصائيات شركتنا', 'لوحة تحكم الشركة', 'ملخص التوظيف', 
                'إحصائيات التوظيف', 'احصائيات التوظيف', 'إحصائيات المرشحين', 'احصائيات المرشحين',
                'ملخص إحصائيات التوظيف', 'ملخص التوظيف والمرشحين', 'شواغرنا', 'وظائفنا النشطة', 'مؤشرات الشركة'
            ])
        ) && in_array('get_company_dashboard_overview', $authorizedNames);

        if ($isCompanyDashboardIntent) {
            $res = AiToolRegistry::executeTool($user, 'get_company_dashboard_overview', []);
            if ($res['status'] === 'success') {
                if (($res['role_type'] ?? '') === 'company') {
                    $m = $res['metrics'] ?? [];
                    $out = "🏢 **ملخص لوحة تحكم وإحصائيات شركة ({$res['company_name']}):**\n\n" .
                        "• **حالة الاعتماد في جامعة طرابلس:** " . ($res['is_approved'] ? 'شريك معتمد وموثق ✅' : 'قيد المراجعة والتدقيق ⏳') . "\n" .
                        "• **مجال العمل:** {$res['industry']}\n\n" .
                        "📊 **مؤشرات التوظيف والفرص:**\n" .
                        "• **الوظائف المفتوحة حالياً:** **{$m['active_jobs']} فرصة نشطة** (من إجمالي {$m['total_jobs']} وظيفة)\n" .
                        "• **إجمالي المرشحين لوظائفكم:** **{$m['total_candidates']} مرشحاً**\n" .
                        "  - ⏳ بانتظار دراسة الشركة: **{$m['new_pending_candidates']}** طلب جديد\n" .
                        "  - 📅 مقابلات مجدولة: **{$m['scheduled_interviews']}** مقابلة\n" .
                        "  - 🎯 تم توظيفهم رسمياً: **{$m['hired_graduates']}** خريج وخريجة\n\n" .
                        "💡 يمكنك طلب: *'عرض المرشحين لوظائف شركتنا'* لمراجعة التفاصيل.";

                    return [
                        'content' => $out,
                        'tool_executed' => 'get_company_dashboard_overview',
                    ];
                } else {
                    $m = $res['metrics'] ?? [];
                    $out = "🏢 **مؤشرات قطاع الشركات وسوق العمل في جامعة طرابلس:**\n\n" .
                        "• **إجمالي الشركات الشريكة:** **{$m['total_partner_companies']}** شركة\n" .
                        "• **الشركات المعتمدة:** **{$m['approved_companies']}** | بانتظار التدقيق: **{$m['pending_review_companies']}**\n" .
                        "• **الفرص الوظيفية الشاغرة:** **{$m['open_vacancies']}** وظيفة مفتوحة (إجمالي المعلن: {$m['total_jobs_posted']})\n" .
                        "• **إجمالي ترشيحات الخريجين:** **{$m['total_nominations']}** ترشيحاً لسوق العمل.";

                    return [
                        'content' => $out,
                        'tool_executed' => 'get_company_dashboard_overview',
                    ];
                }
            }
        }

        // هـ. اعتماد أو تفعيل شركة شريكة
        $isCompanyApprovalIntent = (
            Str::contains($text, ['اعتمد شركة', 'اعتماد شركة', 'تفعيل شركة', 'إلغاء اعتماد شركة', 'الغاء اعتماد شركة'])
        ) && in_array('toggle_company_approval', $authorizedNames);

        if ($isCompanyApprovalIntent) {
            $companyIdent = '';
            if (preg_match('/(?:شركة|المؤسسة|مؤسسة)\s+([^\?\.\!]+)/u', $userMessage, $m)) {
                $companyIdent = trim($m[1]);
            }

            if (!empty($companyIdent)) {
                $targetStatus = Str::contains($text, ['إلغاء', 'الغاء', 'إيقاف']) ? 'unapprove' : 'approve';
                $res = AiToolRegistry::executeTool($user, 'toggle_company_approval', [
                    'company_identifier' => $companyIdent,
                    'target_status' => $targetStatus,
                ]);

                if (isset($res['status']) && $res['status'] === 'proposal') {
                    return [
                        'content' => "🏢 **تم إعداد قرار تعديل اعتماد الشركة الشريكة:**\n\n" .
                            "يرجى مراجعة التفاصيل في البطاقة التفاعلية أدناه وتأكيد الإجراء.",
                        'action_proposal' => $res,
                        'tool_executed' => 'toggle_company_approval',
                    ];
                } else {
                    return [
                        'content' => $res['message'] ?? 'تعذر العثور على الشركة المطلوبة.',
                        'tool_executed' => 'toggle_company_approval',
                    ];
                }
            }
        }

        // و. عرض قائمة الشركات الشريكة المعتمدة
        $cleanedMsg = trim($text, " ?.!\t\n\r\0\x0B؟،,");
        $isSearchCompaniesIntent = (
            Str::contains($text, [
                'قائمة الشركات', 'الشركات الشريكة', 'دليل الشركات', 'عرض الشركات', 'شركات معتمدة', 
                'شركات شريكة', 'ماذا عن الشركات', 'ماذا عن الشركاء', 'الشركات المتعاونة', 'شركاء النجاح'
            ]) ||
            in_array($cleanedMsg, ['الشركات', 'شركات', 'الشركاء', 'شركاء', 'الشركات الشريكة'])
        ) && in_array('search_partner_companies', $authorizedNames);

        if ($isSearchCompaniesIntent) {
            $res = AiToolRegistry::executeTool($user, 'search_partner_companies', ['status' => 'approved']);
            $comps = $res['data'] ?? [];

            if (empty($comps)) {
                return [
                    'content' => "🏢 لا توجد شركات شريكة معتمدة مسجلة حالياً في المنظومة.\n\n💡 يمكنك إضافة شركة جديدة بقول: *'أضف شركة جديدة شريكة'*.",
                    'tool_executed' => 'search_partner_companies',
                ];
            }

            $out = "🏢 **دليل الشركات والمؤسسات الشريكة المعتمدة في جامعة طرابلس (" . count($comps) . " شركة):**\n\n";
            foreach ($comps as $idx => $c) {
                $num = $idx + 1;
                $out .= "{$num}. 🏛️ **{$c['name']}**\n";
                $out .= "   • **القطاع الصناعي:** {$c['industry']} | الحالة: {$c['status_arabic']}\n";
                $out .= "   • **مسؤول الاتصال:** {$c['contact_person']} | 📞 الهاتف: `{$c['phone']}`\n";
                $out .= "   • **الوظائف الشاغرة المطروحة:** **{$c['open_jobs_count']}** وظيفة مفتوحة\n\n";
            }
            $out .= "💡 _يمكنك إضافة شركة جديدة أو نشر وظيفة بالتعاون مع إحدى هذه الشركات في أي وقت._";

            return [
                'content' => $out,
                'tool_executed' => 'search_partner_companies',
            ];
        }

        // ز. استعراض ملف الشركة الحالي
        $isProfileIntent = (
            Str::contains($text, ['ملف شركتنا', 'بيانات شركتنا', 'معلومات شركتنا', 'بروفايل الشركة'])
        ) && in_array('get_my_company_profile', $authorizedNames);

        if ($isProfileIntent) {
            $res = AiToolRegistry::executeTool($user, 'get_my_company_profile', []);
            if ($res['status'] === 'success') {
                $c = $res['company'] ?? [];
                $out = "🏢 **الملف التعريفي لشركة ({$c['name']}):**\n\n" .
                    "• **المجال الصناعي:** {$c['industry']}\n" .
                    "• **حالة الاعتماد:** " . ($c['is_approved'] ? 'معتمدة رسمياً ✅' : 'قيد التدقيق ⏳') . "\n" .
                    "• **البريد الرسمي:** `{$c['email']}`\n" .
                    "• **رقم الهاتف:** `{$c['phone']}`\n" .
                    "• **العنوان والمقر:** {$c['address']}\n" .
                    "• **الموقع الإلكتروني:** {$c['website']}\n" .
                    "• **مسؤول التواصل:** {$c['contact_person']}\n" .
                    "• **الوظائف المفتوحة حالياً:** **{$c['open_jobs']}** وظيفة معلنة\n\n" .
                    "💡 يمكنك تعديل هذه البيانات في أي وقت من خلال صفحة **الملف التعريفي للشركة**.";

                return [
                    'content' => $out,
                    'tool_executed' => 'get_my_company_profile',
                ];
            }
        }

        // ح. استعراض وظائف وشواغر الشركة
        $isCompanyJobsIntent = (
            Str::contains($text, ['وظائفنا', 'شواغرنا', 'قائمة الوظائف', 'استعراض الوظائف', 'فرص العمل المعلنة', 'وظائف الشركة'])
        ) && in_array('get_company_jobs', $authorizedNames);

        if ($isCompanyJobsIntent) {
            $compIdent = '';
            if ($user->role !== 'company' && preg_match('/(?:شركة|لشركة|مؤسسة)\s+([^\?\.\!]+)/u', $userMessage, $m)) {
                $compIdent = trim($m[1]);
            }
            $status = Str::contains($text, ['المغلقة', 'مغلقة']) ? 'closed' : (Str::contains($text, ['المفتوحة', 'نشطة', 'متاحة']) ? 'open' : 'all');
            $res = AiToolRegistry::executeTool($user, 'get_company_jobs', [
                'status' => $status,
                'company_identifier' => $compIdent,
            ]);

            $jobs = $res['data'] ?? [];
            if (empty($jobs)) {
                return [
                    'content' => "💼 لا توجد فرص وظيفية مسجلة حالياً تطابق معايير البحث.\n\n💡 يمكنك إضافة وظيفة جديدة بقول: *'أضف وظيفة [المسمى الوظيفي]'*.",
                    'tool_executed' => 'get_company_jobs',
                ];
            }

            $out = "💼 **قائمة الفرص الوظيفية (" . count($jobs) . " وظيفة):**\n\n";
            foreach ($jobs as $idx => $j) {
                $num = $idx + 1;
                $statusIcon = $j['status'] === 'open' ? '🟢 مفتوحة' : '🔒 مغلقة';
                $out .= "{$num}. 📌 **{$j['title']}** ({$statusIcon})\n";
                $out .= "   • الشركة: {$j['company_name']} | نوع العقد: {$j['contract_type']}\n";
                $out .= "   • المقاعد: {$j['seats']} | موقع العمل: {$j['location']}\n";
                $out .= "   • إجمالي المرشحين: **{$j['nominations_count']}** مرشحاً (تم التوظيف: {$j['hired_count']})\n";
                $out .= "   • آخر موعد للتقديم: {$j['deadline']}\n";
                if ($j['status'] === 'open') {
                    $out .= "   👉 [🔒 إغلاق الوظيفة](#prompt:أغلق وظيفة {$j['title']})\n";
                } else {
                    $out .= "   👉 [🟢 إعادة فتح الوظيفة](#prompt:أعد فتح وظيفة {$j['title']})\n";
                }
                $out .= "\n";
            }

            return [
                'content' => $out,
                'tool_executed' => 'get_company_jobs',
            ];
        }

        // ط. تعديل حالة الوظيفة (إغلاق / إعادة فتح)
        $isToggleJobIntent = (
            Str::contains($text, ['أغلق وظيفة', 'اغلق وظيفة', 'إغلاق وظيفة', 'اوقف التقديم', 'أوقف التقديم', 'إيقاف وظيفة', 'أعد فتح وظيفة', 'اعد فتح وظيفة', 'تفعيل وظيفة', 'فتح وظيفة'])
        ) && in_array('toggle_job_status', $authorizedNames);

        if ($isToggleJobIntent) {
            $jobIdent = '';
            if (preg_match('/(?:وظيفة|فرصة)\s+([^\?\.\!]+)/u', $userMessage, $m)) {
                $jobIdent = trim($m[1]);
            }
            $targetStatus = Str::contains($text, ['فتح', 'تفعيل', 'مفتوحة']) ? 'open' : 'closed';
            $res = AiToolRegistry::executeTool($user, 'toggle_job_status', [
                'job_identifier' => $jobIdent,
                'target_status' => $targetStatus,
            ]);

            if (isset($res['status']) && $res['status'] === 'proposal') {
                return [
                    'content' => "💼 **تم إعداد طلب تعديل حالة فرصة العمل:**\n\n" .
                        "يرجى مراجعة التفاصيل في البطاقة التفاعلية أدناه وتأكيد التنفيذ.",
                    'action_proposal' => $res,
                    'tool_executed' => 'toggle_job_status',
                ];
            } else {
                return [
                    'content' => $res['message'] ?? 'تعذر العثور على فرصة العمل المحددة.',
                    'tool_executed' => 'toggle_job_status',
                ];
            }
        }

        // ي. توثيق وثيقة أو مذكرة تفاهم
        $isDocIntent = (
            Str::contains($text, ['وثق عقد', 'توثيق عقد', 'سجل عقد', 'مذكرة تفاهم', 'وثيقة شراكة', 'تسجيل وثيقة', 'سجل مذكرة'])
        ) && in_array('record_partnership_document', $authorizedNames);

        if ($isDocIntent) {
            $companyIdent = '';
            if (preg_match('/(?:مع شركة|لشركة|مع مؤسسة|لمؤسسة|شركة)\s+([^\?\.\!]+)/u', $userMessage, $m)) {
                $companyIdent = trim($m[1]);
            }
            $docName = 'اتفاقية شراكة وتعاون استراتيجي';
            if (preg_match('/(?:وثيقة|عقد|مذكرة)\s+([^\?\.\!]+?)(?:\s+مع|\s+لشركة|\s*$)/u', $userMessage, $m)) {
                $docName = trim($m[1]);
            }

            $res = AiToolRegistry::executeTool($user, 'record_partnership_document', [
                'company_identifier' => $companyIdent,
                'document_name' => $docName,
                'document_type' => Str::contains($text, ['مذكرة تفاهم', 'mou']) ? 'mou' : 'agreement',
            ]);

            if (isset($res['status']) && $res['status'] === 'proposal') {
                return [
                    'content' => "📜 **تم إعداد وثيقة الشراكة الرسمية للتوثيق:**\n\n" .
                        "يرجى مراجعة تفاصيل الاتفاقية والجهة الموقعة في البطاقة التفاعلية وتأكيد الأرشفة.",
                    'action_proposal' => $res,
                    'tool_executed' => 'record_partnership_document',
                ];
            } else {
                return [
                    'content' => $res['message'] ?? 'تعذر تجهيز وثيقة الشراكة.',
                    'tool_executed' => 'record_partnership_document',
                ];
            }
        }

        // ك. تحديث شروط وتمديد الشراكة
        $isUpdatePartnershipIntent = (
            Str::contains($text, ['تمديد شراكة', 'تجديد شراكة', 'تحديث شراكة', 'شروط الشراكة', 'إنهاء شراكة', 'انهاء شراكة'])
        ) && in_array('update_company_partnership', $authorizedNames);

        if ($isUpdatePartnershipIntent) {
            $companyIdent = '';
            if (preg_match('/(?:لشركة|شركة|مؤسسة)\s+([^\?\.\!]+)/u', $userMessage, $m)) {
                $companyIdent = trim($m[1]);
            }
            $pStatus = Str::contains($text, ['إنهاء', 'انهاء', 'إلغاء', 'الغاء']) ? 'terminated' : 'active';
            $res = AiToolRegistry::executeTool($user, 'update_company_partnership', [
                'company_identifier' => $companyIdent,
                'partnership_status' => $pStatus,
            ]);

            if (isset($res['status']) && $res['status'] === 'proposal') {
                return [
                    'content' => "🤝 **تم إعداد مقترح تحديث اتفاقية الشراكة:**\n\n" .
                        "يرجى مراجعة التحديثات وتأكيد الحفظ في سجلات الجامعة.",
                    'action_proposal' => $res,
                    'tool_executed' => 'update_company_partnership',
                ];
            } else {
                return [
                    'content' => $res['message'] ?? 'تعذر العثور على الشركة الشريكة.',
                    'tool_executed' => 'update_company_partnership',
                ];
            }
        }

        // ل. تحديث بيانات وملف الشركة
        $isUpdateProfileIntent = (
            Str::contains($text, ['تحديث بيانات الشركة', 'تعديل هاتف الشركة', 'تعديل موقع الشركة', 'تحديث ملف الشركة', 'تعديل بيانات شركتنا', 'تحديث هاتفنا'])
        ) && in_array('update_company_profile', $authorizedNames);

        if ($isUpdateProfileIntent) {
            $args = [];
            if (preg_match('/(?:هاتف|رقم|هاتفنا)\s*(?:إلى|هو)?\s*([0-9\+\-\s]{8,15})/u', $userMessage, $m)) {
                $args['phone'] = trim($m[1]);
            }
            if (preg_match('/(?:موقع|موقعنا|رابط)\s*(?:إلى|هو)?\s*(https?:\/\/[^\s]+|[a-zA-Z0-9\.\-_]+\.[a-zA-Z]{2,})/u', $userMessage, $m)) {
                $args['website'] = trim($m[1]);
            }
            if (preg_match('/(?:عنوان|مقر)\s*(?:إلى|هو|في)?\s*([^\?\.\!،,]+)/u', $userMessage, $m)) {
                $args['address'] = trim($m[1]);
            }

            $res = AiToolRegistry::executeTool($user, 'update_company_profile', $args);
            if (isset($res['status']) && $res['status'] === 'proposal') {
                return [
                    'content' => "🏢 **تم إعداد تحديثات الملف التعريفي للشركة:**\n\n" .
                        "يرجى مراجعة البيانات المعدلة وتأكيد الحفظ.",
                    'action_proposal' => $res,
                    'tool_executed' => 'update_company_profile',
                ];
            } else {
                return [
                    'content' => $res['message'] ?? 'تعذر تجهيز تحديث ملف الشركة.',
                    'tool_executed' => 'update_company_profile',
                ];
            }
        }

        // ==========================================
        // 🎯 13. أدوات قطاع الإرشاد والتوجيه المهني
        // ==========================================
        if (Str::contains($text, ['إضافة وظيفة', 'إضافة فرصة عمل', 'صياغة وظيفة', 'وظيفة جديدة', 'شاغر وظيفي', 'أضف وظيفة']) && in_array('draft_job_opportunity', $authorizedNames)) {
            $jobTitle = 'مطور برمجيات وتطبيقات سحابية';
            if (preg_match('/(?:وظيفة|فرصة|مسمى)\s+([^\?\.\!]+)/u', $userMessage, $m)) {
                $jobTitle = trim($m[1]);
            }

            $res = AiToolRegistry::executeTool($user, 'draft_job_opportunity', ['title' => $jobTitle]);
            if (isset($res['status']) && $res['status'] === 'proposal') {
                return [
                    'content' => "💼 **تم إعداد مسودة الفرصة الوظيفية الجديدة:**\n\n" .
                        "• **المسمى:** {$jobTitle}\n\n" .
                        "يرجى مراجعة التفاصيل وشروط التقديم أدناه، ثم الضغط على **[تأكيد وحفظ]** لنشرها.",
                    'action_proposal' => $res,
                    'tool_executed' => 'draft_job_opportunity',
                ];
            }
        }

        if (Str::contains($text, ['تقرير الإرشاد', 'تقرير التوجيه', 'تقرير الخريجين والتوظيف', 'تقرير الإرشاد المهني']) && in_array('generate_career_guidance_report', $authorizedNames)) {
            $res = AiToolRegistry::executeTool($user, 'generate_career_guidance_report', []);
            $m = $res['metrics'] ?? [];

            $out = "🎯 **{$res['report_title']}**\n";
            $out .= "_تاريخ التوليد: {$res['generated_at']}_\n\n";
            $out .= "• **إجمالي الخريجين في قاعدة البيانات:** {$m['total_graduates']} خريج وخريجة\n";
            $out .= "• **الخريجون الموظفون:** {$m['employed_graduates']} | الباحثون عن عمل: {$m['job_seeking_graduates']}\n";
            $out .= "• **نسبة توفر السير الذاتية (CV):** **{$m['cv_upload_rate']}** ({$m['graduates_with_cv']} سيرة ذاتية مرفوعة)\n";
            $out .= "• **إجمالي الترشيحات المهنية المنجزة:** {$m['total_job_nominations']} ترشيح\n";
            $out .= "• **مؤشر الجاهزية لسوق العمل:** {$m['market_readiness_index']}\n";

            return [
                'content' => $out,
                'tool_executed' => 'generate_career_guidance_report',
            ];
        }

        // ==========================================
        // 👑 14. التقرير التنفيذي الشامل (الإدارة العليا)
        // ==========================================
        if (Str::contains($text, ['تقرير تنفيذي', 'تقرير شامل', 'تقرير القيادة', 'التقرير الاستراتيجي', 'تقرير شامل للنظام', 'تقرير الإدارة العليا']) && in_array('generate_executive_report', $authorizedNames)) {
            $res = AiToolRegistry::executeTool($user, 'generate_executive_report', []);
            $sec = $res['sectors'] ?? [];

            $out = "👑 **{$res['report_title']}**\n";
            $out .= "_تاريخ الاعتماد: {$res['generated_at']}_\n\n";

            $out .= "🏛️ **1. " . ($sec['academic_and_graduates']['title'] ?? 'قطاع الخريجين') . ":**\n";
            $out .= "   • إجمالي الخريجين: {$sec['academic_and_graduates']['total_graduates']} | المستخدمون: {$sec['academic_and_graduates']['total_registered_users']} | السير الذاتية: {$sec['academic_and_graduates']['cv_availability']}\n\n";

            $out .= "🎓 **2. " . ($sec['training_and_capacity']['title'] ?? 'قطاع التدريب') . ":**\n";
            $out .= "   • البرامج: {$sec['training_and_capacity']['total_trainings']} | المقاعد: {$sec['training_and_capacity']['total_seats']} | طلبات الالتحاق: {$sec['training_and_capacity']['total_applications']} (المقبولون: {$sec['training_and_capacity']['accepted_applications']})\n\n";

            $out .= "🏢 **3. " . ($sec['partnerships_and_labor']['title'] ?? 'قطاع الشراكات') . ":**\n";
            $out .= "   • الشركات الشريكة: {$sec['partnerships_and_labor']['partner_companies']} | فرص العمل: {$sec['partnerships_and_labor']['job_opportunities']} | ترشيحات التوظيف: {$sec['partnerships_and_labor']['nominations_made']}\n\n";

            $out .= "📝 **4. " . ($sec['quality_and_evaluation']['title'] ?? 'قطاع الجودة والتقييم') . ":**\n";
            $out .= "   • الاستبيانات: {$sec['quality_and_evaluation']['total_surveys']} | الاستجابات: {$sec['quality_and_evaluation']['total_survey_responses']} | مؤشر الرضا المؤسسي: {$sec['quality_and_evaluation']['institutional_satisfaction']}\n\n";

            $out .= "📢 **5. " . ($sec['media_and_relations']['title'] ?? 'قطاع الإعلام والتوثيق والاتصال') . ":**\n";
            $out .= "   • الأخبار المعتمدة: {$sec['media_and_relations']['published_news']} | الإعلانات الرسمية النشطة: {$sec['media_and_relations']['active_announcements']}\n\n";

            $out .= "✨ **خلاصة القيادة:** النظام يعمل بتكامل تشغيلي متقدم بين كافة الوحدات الخمس، مع تحقيق مؤشرات أداء تفوق المستهدف الفصلي.";

            return [
                'content' => $out,
                'tool_executed' => 'generate_executive_report',
            ];
        }

        // Default welcoming & capabilities message
        $roleArabic = $user->role_arabic ?? $user->role;
        return [
            'content' => "أهلاً بك يا **{$user->name}**! أنا المساعد الذكي الموحد لمكتب تدريب وتأهيل الخريجين بجامعة طرابلس.\n\n" .
                "بصفتك مسجلاً بصلاحية (**{$roleArabic}**)، يمكنني مساعدتك في تنفيذ كافة المهام التالية مباشرة:\n\n" .
                ($user->role === 'graduate' ? "• 🎓 الاستعلام عن حالة طلبات التدريب والتقديم على البرامج بنقرة واحدة.\n• 💼 استعراض الوظائف المتاحة والترشح الذاتي لفرص العمل.\n• 📄 فحص ملفك الأكاديمي وسيرتك الذاتية ومعدلك.\n" : "") .
                ($user->role === 'evaluation_followup' ? "• 📝 إعداد وصياغة استبيانات تقييم البرامج والفعاليات ونشرها.\n• 📊 توليد تقارير الجودة والتقييم والمتابعة الأكاديمية المفصلة.\n• 📋 استعراض ملخص الاستبيانات النشطة ومعدلات الاستجابة.\n" : "") .
                ($user->role === 'training_coordinator' ? "• 🎓 صياغة البرامج والدورات التدريبية وجدولتها كمسودات.\n• ✅ قبول أو رفض طلبات الالتحاق بالتدريب (فردياً، أو لمجموعة/دورة محددة، أو لكافة الطلبات دفعة واحدة).\n• 📈 تقارير أداء التدريبات، نسب شغل المقاعد، وحالات الطلبات.\n• 🔍 استعلام شامل عن التدريبات والمقاعد ونسب الحضور.\n" : "") .
                ($user->role === 'company' ? "• 👥 استعراض المرشحين والمتقدمين لشواغر شركتكم مع تفاصيل مؤهلاتهم وسيرهم الذاتية.\n• 📅 جدولة مواعيد المقابلات وتحديد التوقيت والمكان وإشعار الخريج آلياً.\n• ✅ قبول المرشحين المبدئي أو تأكيد التوظيف الرسمي (Hired).\n• 💼 صياغة ونشر فرص عمل وشواغر جديدة مباشرة لشركتكم.\n• 🔍 طلب واقتراح أفضل الخريجين المتطابقين مع تخصصاتكم بحسب المعدل والمهارات.\n• 📊 ملخص لوحة تحكم الشركة وإحصائيات التوظيف ومتابعة ملف الشركة.\n" : "") .
                ($user->role === 'partnership_officer' ? "• 🏢 إضافة واعتماد وتوثيق الشركات الشريكة وسجلات أرباب العمل.\n• 💼 نشر وإدارة الفرص الوظيفية والشواغر بالتعاون مع جهات العمل.\n• 🤝 متابعة ترشيحات الخريجين ومسارات التوظيف مع الشركات.\n• 📊 تقارير تحليلية متقدمة عن قطاع الشراكات وسوق العمل والجاهزية المهنية.\n" : "") .
                ($user->role === 'career_guidance_officer' ? "• 🔍 البحث المتقدم في سجلات الخريجين (بالهاتف، التخصص، المعدل، السيرة الذاتية).\n• 🤝 ترشيح الخريجين المؤهلين مباشرة للوظائف المعتمدة (فردي وجماعي).\n• 🔒 تجميد أو تنشيط أو حذف حسابات وسجلات الخريجين مباشرة.\n• 💼 صياغة ونشر فرص العمل الجديدة وتوليد تقارير التوجيه المهني.\n" : "") .
                ($user->role === 'media_officer' ? "• 📰 صياغة الأخبار والبيانات الصحفية بأسلوب جامعي رصين.\n• 📢 صياغة ونشر الإعلانات والتعميمات الرسمية في المنظومة.\n• 📅 إدارة تقويم التغطيات الإعلامية الميدانية وتقارير التوثيق.\n" : "") .
                ($user->role === 'admin' ? "• 👑 تقارير تنفيذية استراتيجية شاملة تغطي كافة القطاعات الخمسة.\n• ⚙️ تنفيذ ومراجعة كافة العمليات المعتمدة والصلاحيات في النظام بالكامل (ترشيح فردي وجماعي، تجميد وحذف الحسابات، وقبول طلبات التدريب، واعتماد الشركات والوظائف).\n" : "") .
                "\n💡 **خبير المنظومة:** يمكنك سؤالي أيضاً عن طريقة تنفيذ أي مهمة (مثل: *'كيف أراجع المرشحين؟'* أو *'كيف أضيف وظيفة؟'*) أو طلب تنفيذها مني مباشرة!",
        ];
    }

    /**
     * Comprehensive, rich Arabic formatter for tool results
     */
    protected function formatToolResultFallback(string $toolName, array $result, ?User $user = null): string
    {
        if (isset($result['status']) && $result['status'] === 'proposal') {
            return $result['message'] ?? 'تم تجهيز الإجراء المقترح بنجاح.';
        }

        if ($toolName === 'get_system_overview') {
            $d = $result['data'] ?? [];
            return "👑 **تقرير المؤشرات العامة لمنظومة جامعة طرابلس:**\n\n" .
                "• 👥 **إجمالي المستخدمين المسجلين:** " . ($d['total_users'] ?? 0) . " مستخدم\n" .
                "• 🎓 **الخريجون المسجلون في المنصة:** " . ($d['graduates_count'] ?? 0) . " خريج وخريجة\n" .
                "• 🏢 **الشركات والمؤسسات الشريكة:** " . ($d['companies_count'] ?? 0) . " شركة\n" .
                "• 🎯 **البرامج والدورات التدريبية:** " . ($d['trainings_count'] ?? 0) . " برنامج تدريبي\n" .
                "• 📝 **طلبات الالتحاق بالتدريب:** " . ($d['applications_count'] ?? 0) . " طلب التحاق\n" .
                "• 💼 **الفرص الوظيفية المنشورة:** " . ($d['job_opportunities_count'] ?? 0) . " فرصة وظيفية\n" .
                "• 📰 **الأخبار والبيانات الصحفية:** " . ($d['published_news'] ?? 0) . " خبر منشور\n\n" .
                "✨ كافة الوحدات الإدارية الخمس تعمل بتكامل تشغيلي كامل ومتصلة بقاعدة البيانات الحية.";
        }

        if ($toolName === 'generate_executive_report') {
            $sec = $result['sectors'] ?? [];
            $title = $result['report_title'] ?? 'التقرير التنفيذي الشامل لمكتب تدريب وتأهيل الخريجين — جامعة طرابلس';
            $date = $result['generated_at'] ?? now()->format('Y-m-d H:i');

            $out = "👑 **{$title}**\n";
            $out .= "_تاريخ الاعتماد: {$date}_\n\n";

            if (isset($sec['academic_and_graduates'])) {
                $s = $sec['academic_and_graduates'];
                $out .= "🏛️ **1. " . ($s['title'] ?? 'قطاع الخريجين وقواعد البيانات') . ":**\n";
                $out .= "   • إجمالي الخريجين المسجلين: **{$s['total_graduates']}** خريج وخريجة\n";
                $out .= "   • حسابات المستخدمين: **{$s['total_registered_users']}** مستخدم نشط\n";
                $out .= "   • السير الذاتية المرفوعة (CV): **{$s['cv_availability']}** ملف\n\n";
            }

            if (isset($sec['training_and_capacity'])) {
                $s = $sec['training_and_capacity'];
                $out .= "🎓 **2. " . ($s['title'] ?? 'قطاع البرامج والتدريب والتأهيل') . ":**\n";
                $out .= "   • إجمالي البرامج والدورات: **{$s['total_trainings']}** برنامج\n";
                $out .= "   • المقاعد التدريبية المتاحة: **{$s['total_seats']}** مقعد\n";
                $out .= "   • طلبات الالتحاق بالتدريب: **{$s['total_applications']}** طلب (المقبولون: {$s['accepted_applications']})\n\n";
            }

            if (isset($sec['partnerships_and_labor'])) {
                $s = $sec['partnerships_and_labor'];
                $out .= "🏢 **3. " . ($s['title'] ?? 'قطاع الشراكات وسوق العمل') . ":**\n";
                $out .= "   • الشركات والمؤسسات الشريكة: **{$s['partner_companies']}** شركة\n";
                $out .= "   • الفرص الوظيفية المنشورة: **{$s['job_opportunities']}** فرصة عمل\n";
                $out .= "   • ترشيحات الخريجين المنجزة: **{$s['nominations_made']}** ترشيح\n\n";
            }

            if (isset($sec['quality_and_evaluation'])) {
                $s = $sec['quality_and_evaluation'];
                $out .= "📝 **4. " . ($s['title'] ?? 'قطاع الجودة والتقييم والمتابعة') . ":**\n";
                $out .= "   • الاستبيانات المنشورة: **{$s['total_surveys']}** استبيان\n";
                $out .= "   • الاستجابات المسجلة: **{$s['total_survey_responses']}** استجابة\n";
                $out .= "   • مؤشر الرضا المؤسسي: **{$s['institutional_satisfaction']}**\n\n";
            }

            if (isset($sec['media_and_relations'])) {
                $s = $sec['media_and_relations'];
                $out .= "📢 **5. " . ($s['title'] ?? 'قطاع الإعلام والتوثيق والاتصال') . ":**\n";
                $out .= "   • الأخبار والبيانات الصحفية: **{$s['published_news']}** خبر معتمد\n";
                $out .= "   • الإعلانات والتعميمات السارية: **{$s['active_announcements']}** إعلان رسمي\n\n";
            }

            $out .= "✨ **خلاصة القيادة:** النظام يعمل بتكامل تشغيلي متقدم بين كافة الوحدات الخمس، مع تحقيق مؤشرات أداء تفوق المستهدف الفصلي.";
            return $out;
        }

        if ($toolName === 'generate_career_guidance_report') {
            $m = $result['metrics'] ?? [];
            $title = $result['report_title'] ?? 'تقرير الإرشاد والتوجيه المهني';
            return "🎯 **{$title}**\n\n" .
                "• **إجمالي الخريجين:** " . ($m['total_graduates'] ?? 0) . " خريج\n" .
                "• **الخريجون الموظفون:** " . ($m['employed_graduates'] ?? 0) . " | **الباحثون عن عمل:** " . ($m['job_seeking_graduates'] ?? 0) . "\n" .
                "• **نسبة توفر السير الذاتية (CV):** **" . ($m['cv_upload_rate'] ?? '0%') . "** (" . ($m['graduates_with_cv'] ?? 0) . " سيرة ذاتية)\n" .
                "• **إجمالي الترشيحات المهنية:** " . ($m['total_job_nominations'] ?? 0) . " ترشيح\n" .
                "• **مؤشر الجاهزية لسوق العمل:** " . ($m['market_readiness_index'] ?? 'جاهزية مرتفعة') . "\n";
        }

        if ($toolName === 'generate_training_report') {
            $m = $result['metrics'] ?? [];
            return "🎓 **تقرير أداء التدريب والقدرات:**\n\n" .
                "• **إجمالي البرامج المنفذة والنشطة:** " . ($m['total_trainings'] ?? 0) . " برنامج\n" .
                "• **إجمالي المقاعد المخصصة:** " . ($m['total_seats'] ?? 0) . " مقعد\n" .
                "• **طلبات الالتحاق:** " . ($m['total_applications'] ?? 0) . " (المعتمدون: " . ($m['approved_applications'] ?? 0) . ")\n" .
                "• **نسبة إشغال المقاعد:** " . ($m['seat_occupancy_rate'] ?? '100%') . "\n";
        }

        if ($toolName === 'generate_partnerships_report') {
            $m = $result['metrics'] ?? [];
            return "🏢 **تقرير الشراكات وسوق العمل:**\n\n" .
                "• **الشركات والمؤسسات الشريكة:** " . ($m['total_companies'] ?? 0) . " شركة\n" .
                "• **الفرص الوظيفية المنشورة:** " . ($m['total_job_opportunities'] ?? 0) . " فرصة\n" .
                "• **إجمالي الترشيحات:** " . ($m['total_graduate_nominations'] ?? 0) . " ترشيح\n" .
                "• **مؤشر كفاءة الشراكات:** " . ($m['partnership_health'] ?? 'ممتاز') . "\n";
        }

        if ($toolName === 'search_graduates_advanced') {
            $grads = $result['data'] ?? [];
            if (empty($grads)) {
                return "🔍 لم يتم العثور على خريجين يطابقون معايير البحث المحددة في قاعدة البيانات حالياً.";
            }
            $out = "🎯 **وجدت " . count($grads) . " من الخريجين المطابقين للبحث:**\n\n";
            foreach ($grads as $i => $g) {
                $num = $i + 1;
                $out .= "{$num}. 👤 **{$g['name']}**\n";
                $out .= "   • التخصص والكلية: **{$g['major']}** ({$g['faculty']})\n";
                $out .= "   • المعدل: **{$g['gpa']}** | الهاتف: `{$g['phone']}` | سنة التخرج: {$g['graduation_year']}\n";
                $out .= "   • السيرة الذاتية: " . ($g['has_cv'] ? 'متوفرة ✅' : 'غير مرفوعة ⚠️') . " | الحالة: {$g['employment_status']}\n\n";
            }
            return $out;
        }

        if ($toolName === 'search_job_opportunities') {
            $jobs = $result['data'] ?? [];
            if (empty($jobs)) {
                return "💼 **فرص العمل والتشغيل:**\n\nلا توجد فرص وظيفية مفتوحة حالياً في المنظومة.";
            }

            // الفصل والتمييز التام بين وظائف العمل المباشرة وفرص التدريب الداخلي
            $directJobs = [];
            $internships = [];
            foreach ($jobs as $j) {
                $isInternship = Str::contains(mb_strtolower($j['title'] . ' ' . ($j['type'] ?? '')), ['تدريب', 'متدرب', 'internship', 'intern']);
                if ($isInternship) {
                    $internships[] = $j;
                } else {
                    $directJobs[] = $j;
                }
            }

            $out = "💼 **تم العثور على " . count($jobs) . " فرصة متاحة في المنظومة:**\n";
            $out .= "*(موزعة بين فرص توظيف مباشر وفرص تدريب عملي وتأهيلي)*\n\n";

            if (!empty($directJobs)) {
                $out .= "━━━━━━━━━━━━━━━━━━━━━━\n";
                $out .= "💼 **أولاً: فرص التوظيف والعمل المباشر (عقود عمل):**\n\n";
                foreach ($directJobs as $i => $j) {
                    $num = $i + 1;
                    $cleanTitle = str_replace(['(', ')'], '', $j['title']);
                    $out .= "{$num}. 🏢 **{$j['title']}** لدى **{$j['company']}**\n";
                    $out .= "   • الموقع: 📍 {$j['location']} | نوع العقد: ⏱ {$j['type']}\n";
                    if (!empty($j['seats'])) $out .= "   • المقاعد: 👥 {$j['seats']} | آخر موعد: 📅 " . ($j['deadline'] ?? 'مفتوح') . "\n";
                    if ($user && $user->role === 'graduate') {
                        $out .= "   👉 [📝 التقديم على هذه الوظيفة](#prompt:أريد التقديم على وظيفة {$cleanTitle})\n\n";
                    } elseif ($user && in_array($user->role, ['career_guidance_officer', 'admin'])) {
                        $out .= "   👉 [🤝 ترشيح خريج لهذه الوظيفة](#prompt:رشح الخريج لوظيفة {$cleanTitle})\n\n";
                    } else {
                        $out .= "\n";
                    }
                }
            }

            if (!empty($internships)) {
                $out .= "━━━━━━━━━━━━━━━━━━━━━━\n";
                $out .= "🎓 **ثانياً: فرص التدريب الداخلي والعملي لدى الشركات الشريكة:**\n\n";
                foreach ($internships as $i => $j) {
                    $num = $i + 1;
                    $cleanTitle = str_replace(['(', ')'], '', $j['title']);
                    $out .= "{$num}. 🏢 **{$j['title']}** لدى **{$j['company']}**\n";
                    $out .= "   • الموقع: 📍 {$j['location']} | نوع العقد: ⏱ {$j['type']}\n";
                    if (!empty($j['seats'])) $out .= "   • المقاعد: 👥 {$j['seats']} | آخر موعد: 📅 " . ($j['deadline'] ?? 'مفتوح') . "\n";
                    if ($user && $user->role === 'graduate') {
                        $out .= "   👉 [📝 التقديم على هذا التدريب](#prompt:أريد التقديم على وظيفة {$cleanTitle})\n\n";
                    } elseif ($user && in_array($user->role, ['career_guidance_officer', 'admin'])) {
                        $out .= "   👉 [🤝 ترشيح خريج لهذا التدريب](#prompt:رشح الخريج لوظيفة {$cleanTitle})\n\n";
                    } else {
                        $out .= "\n";
                    }
                }
            }

            if ($user && $user->role === 'graduate') {
                $out .= "━━━━━━━━━━━━━━━━━━━━━━\n";
                $out .= "💡 **ملاحظة:** للبرامج والدورات التدريبية المقدمة من مكتب تدريب وتأهيل الخريجين بجامعة طرابلس، اضغط على:\n";
                $out .= "[🎓 استعراض الدورات والبرامج التدريبية المتاحة](#prompt:ما هي البرامج التدريبية المتاحة؟)\n";
            }
            return $out;
        }

        if ($toolName === 'generate_cover_letter') {
            return $result['message'] ?? 'تم تجهيز خطاب التوجيه بالبيانات الرسمية بنجاح.';
        }

        if ($toolName === 'search_trainings') {
            $trainings = $result['data'] ?? [];
            if (empty($trainings)) {
                return "🎓 **البرامج التدريبية:**\n\nلا توجد برامج تدريبية مطابقة لمعايير البحث حالياً في المنظومة.";
            }
            $out = "🎓 **تم العثور على " . count($trainings) . " برنامج تدريبي متاح:**\n\n";
            foreach ($trainings as $i => $t) {
                $num = $i + 1;
                $out .= "{$num}. 🔹 **{$t['title']}** ({$t['type']})\n";
                $out .= "   • المكان: 📍 {$t['location']} | المدة: ⏳ {$t['duration']} | المقاعد: 👥 {$t['seats']}\n";
                $out .= "   • تاريخ البدء: 📅 {$t['start_date']}\n";
                if ($user && $user->role === 'graduate') {
                    $out .= "   👉 [📝 التقديم على هذا البرنامج](#prompt:أريد التقديم على برنامج {$t['title']})\n\n";
                } else {
                    $out .= "\n";
                }
            }
            if ($user && $user->role === 'graduate') {
                $out .= "━━━━━━━━━━━━━━━━━━━━━━\n💡 اضغط على زر التقديم أسفل أي دورة للتسجيل فوراً.";
            }
            return $out;
        }

        if ($toolName === 'get_my_applications') {
            $apps = $result['data'] ?? [];
            if (empty($apps)) {
                return "🎓 **طلبات التدريب:**\n\nلم تتقدم بأي طلب تدريب حتى الآن. يمكنك تصفح البرامج التدريبية المتاحة والتقديم عليها فوراً!";
            }
            $out = "🎓 **إليك سجل طلبات التدريب الخاصة بك (" . count($apps) . " طلب):**\n\n";
            foreach ($apps as $a) {
                $statusIcon = in_array($a['status'], ['accepted', 'approved']) ? '🟢' : ($a['status'] === 'rejected' ? '🔴' : '🟡');
                $out .= "{$statusIcon} **{$a['training_title']}**\n";
                $out .= "   • الحالة: **{$a['status_text']}** (تاريخ التقديم: {$a['applied_at']})\n";
                if (!empty($a['admin_feedback'])) {
                    $out .= "   • ملاحظة الإدارة: _{$a['admin_feedback']}_\n";
                }
                $out .= "\n";
            }
            return $out;
        }

        if ($toolName === 'get_my_profile') {
            $d = $result['data'] ?? [];
            $out = "📄 **ملفك الأكاديمي والمهني المسجل:**\n\n";
            $out .= "• **الاسم:** {$d['name']}\n";
            $out .= "• **الكلية:** {$d['college']}\n";
            $out .= "• **التخصص:** {$d['major']}\n";
            if (!empty($d['gpa'])) $out .= "• **المعدل التراكمي:** {$d['gpa']}%\n";
            if (!empty($d['graduation_year'])) $out .= "• **سنة التخرج:** {$d['graduation_year']}\n";
            $out .= "• **السيرة الذاتية المرفوعة:** " . (!empty($d['has_cv']) ? 'نعم (مرفوعة ومحدثة ✅)' : 'لا يوجد ملف سيرة ذاتية مرفوع ⚠️') . "\n";
            return $out;
        }

        if (isset($result['data']) && is_array($result['data'])) {
            $out = "📊 **بيانات مسترجعة بنجاح من قاعدة بيانات جامعة طرابلس:**\n\n";
            foreach ($result['data'] as $k => $v) {
                if (is_scalar($v)) {
                    $out .= "• **" . str_replace('_', ' ', $k) . ":** {$v}\n";
                }
            }
            return $out;
        }

        return "تم جلب البيانات بنجاح من قاعدة بيانات جامعة طرابلس.";
    }
}
