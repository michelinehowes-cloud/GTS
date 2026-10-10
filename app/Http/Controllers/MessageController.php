<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * التحقق مما إذا كان المستخدم يملك صلاحية مراسلة الطرف الآخر
     */
    public function canMessageUser(User $sender, User $receiver): bool
    {
        // لا يمكن للمستخدم مراسلة نفسه
        if ($sender->id === $receiver->id) {
            return false;
        }

        // قاعدة الخريج:
        // الخريج لا يستطيع مراسلة أي أحد باستثناء من لديه صلاحية إدارة الخريجين (career_guidance_officer أو من لديه صلاحية graduates.view)
        // ومدير النظام لا يمكن للخريج الوصول إليه نهائياً
        if ($sender->role === 'graduate') {
            if ($receiver->isAdmin()) {
                return false;
            }

            return $receiver->role === 'career_guidance_officer'
                || $receiver->hasPermission('graduates.view')
                || $receiver->hasPermission('graduates.edit')
                || $receiver->canManageGraduates();
        }

        // قاعدة مدير النظام:
        // مدير النظام لا يراسل الخريجين مباشرة (التواصل مع الخريجين مخصص حصراً لإدارة الخريجين)
        if ($sender->isAdmin() && $receiver->role === 'graduate') {
            return false;
        }

        // قاعدة الشركات:
        // الشركات تراسل الخريجين، مسؤولي الشراكات، الإرشاد المهني، منسقي التدريب، والإدارة
        if ($sender->role === 'company') {
            return in_array($receiver->role, [
                'graduate',
                'admin',
                'partnership_officer',
                'career_guidance_officer',
                'training_coordinator',
                'staff'
            ]);
        }

        return true;
    }

    /**
     * تجميع المحادثات الخاصة بالمستخدم الحالي
     */
    protected function getConversationsData($userId): array
    {
        $messages = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['sender.company', 'sender.graduateData', 'receiver.company', 'receiver.graduateData'])
            ->latest()
            ->get();

        $conversations = [];
        $totalUnreadCount = 0;
        foreach ($messages as $msg) {
            $otherUserId = $msg->sender_id == $userId ? $msg->receiver_id : $msg->sender_id;

            if (!isset($conversations[$otherUserId])) {
                $other = $msg->sender_id == $userId ? $msg->receiver : $msg->sender;
                if (!$other) continue;

                $conversations[$otherUserId] = [
                    'user' => $other,
                    'last_message' => $msg,
                    'unread_count' => 0
                ];
            }

            if ($msg->receiver_id == $userId && is_null($msg->read_at)) {
                $conversations[$otherUserId]['unread_count']++;
                $totalUnreadCount++;
            }
        }

        return [$conversations, $totalUnreadCount];
    }

    /**
     * جلب جهات الاتصال المسموح بمراسلتها للمستخدم الحالي
     */
    protected function getAvailableContacts(User $currentUser)
    {
        $userId = $currentUser->id;
        $contactsQuery = User::where('id', '!=', $userId)
            ->where('is_active', true)
            ->with(['company', 'graduateData']);

        if ($currentUser->role === 'graduate') {
            // الخريج يراسل حصراً من يملك صلاحية إدارة الخريجين (ويستثنى مدير النظام وأي دور آخر)
            $eligibleUserIds = User::where('role', '!=', 'admin')
                ->where('role', '!=', 'graduate')
                ->where('is_active', true)
                ->get()
                ->filter(function ($u) {
                    return $u->role === 'career_guidance_officer'
                        || $u->hasPermission('graduates.view')
                        || $u->hasPermission('graduates.edit')
                        || $u->canManageGraduates();
                })
                ->pluck('id');

            $contactsQuery->whereIn('id', $eligibleUserIds);
        } elseif ($currentUser->isAdmin()) {
            // مدير النظام لا يراسل الخريجين مباشرة
            $contactsQuery->where('role', '!=', 'graduate');
        } elseif ($currentUser->role === 'company') {
            // الشركة تراسل الخريجين ومسؤولي الشراكات والإرشاد
            $contactsQuery->whereIn('role', [
                'graduate',
                'admin',
                'partnership_officer',
                'career_guidance_officer',
                'training_coordinator',
                'staff'
            ]);
        }

        return $contactsQuery->orderBy('name')->get();
    }

    /**
     * صفحة صندوق الرسائل الرئيسية
     */
    public function index(Request $request)
    {
        $currentUser = auth()->user();
        $userId = $currentUser->id;

        list($conversations, $totalUnreadCount) = $this->getConversationsData($userId);

        // استجابة JSON للطلبات من الشريط الجانبي (AJAX / Drawer)
        if ($request->ajax() || $request->wantsJson()) {
            $formattedConversations = [];
            foreach ($conversations as $otherUserId => $data) {
                $other = $data['user'];
                $last = $data['last_message'];
                $unread = $data['unread_count'];

                $displayName = $other ? $other->name : 'مستخدم';
                $isCompany = false;
                $companyName = null;
                $avatar = null;

                if ($other && $other->role === 'company' && $other->company) {
                    $displayName = $other->company->name;
                    $isCompany = true;
                    $companyName = $other->company->name;
                    if (!empty($other->company->logo)) {
                        $avatar = asset('storage/' . $other->company->logo);
                    }
                } elseif ($other && $other->graduateData && !empty($other->graduateData->profile_image)) {
                    $avatar = asset('storage/' . $other->graduateData->profile_image);
                }

                $formattedConversations[] = [
                    'user_id' => $otherUserId,
                    'name' => $displayName,
                    'role' => $other ? $other->role : 'user',
                    'is_company' => $isCompany,
                    'avatar' => $avatar,
                    'initial' => mb_substr($displayName, 0, 1, 'UTF-8'),
                    'last_message' => $last ? \Illuminate\Support\Str::limit($last->content, 65) : '',
                    'last_message_time' => $last ? $last->created_at->diffForHumans() : '',
                    'unread_count' => $unread,
                ];
            }

            return response()->json([
                'status' => 'success',
                'unread_total' => $totalUnreadCount,
                'conversations' => $formattedConversations,
            ]);
        }

        $availableContacts = $this->getAvailableContacts($currentUser);

        return view('messages.index', compact('conversations', 'availableContacts', 'totalUnreadCount'));
    }

    /**
     * صفحة محادثة محددة
     */
    public function show(Request $request, $id)
    {
        $currentUser = auth()->user();
        $userId = $currentUser->id;

        $otherUser = User::with(['company', 'graduateData'])->find($id);

        // Fallback: If $id is a company_id rather than a user_id
        if (!$otherUser) {
            $company = \App\Models\Company::find($id);
            if ($company && $company->user_id) {
                $otherUser = User::with(['company', 'graduateData'])->find($company->user_id);
            }
        }

        if (!$otherUser) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'المستخدم غير موجود.'], 404);
            }
            abort(404, 'المستخدم غير موجود.');
        }

        // فحص صلاحية المراسلة
        if (!$this->canMessageUser($currentUser, $otherUser)) {
            $errorMsg = 'غير مصرح لك ببدء محادثة مع هذا المستخدم.';
            if ($currentUser->role === 'graduate') {
                $errorMsg = 'غير مصرح للخريج بمراسلة هذا المستخدم. التواصل متاح فقط وحصرياً مع مسؤولي إدارة شؤون الخريجين.';
            } elseif ($currentUser->isAdmin() && $otherUser->role === 'graduate') {
                $errorMsg = 'لا يمكن لمدير النظام مراسلة الخريجين مباشرة؛ التواصل مع الخريجين محصور بإدارة الخريجين.';
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $errorMsg], 403);
            }
            return redirect()->route('messages.index')->with('error', $errorMsg);
        }

        $id = $otherUser->id; // Normalize to user id

        $messages = Message::where(function ($query) use ($userId, $id) {
                $query->where('sender_id', $userId)->where('receiver_id', $id);
            })
            ->orWhere(function ($query) use ($userId, $id) {
                $query->where('sender_id', $id)->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // تحديد الرسائل كمقروءة
        Message::where('sender_id', $id)
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // استجابة JSON للطلبات من شريط المحادثات
        if ($request->ajax() || $request->wantsJson()) {
            $displayName = $otherUser->name;
            $isCompany = false;
            $avatar = null;

            if ($otherUser->role === 'company' && $otherUser->company) {
                $displayName = $otherUser->company->name;
                $isCompany = true;
                if (!empty($otherUser->company->logo)) {
                    $avatar = asset('storage/' . $otherUser->company->logo);
                }
            } elseif ($otherUser->graduateData && !empty($otherUser->graduateData->profile_image)) {
                $avatar = asset('storage/' . $otherUser->graduateData->profile_image);
            }

            $formattedMessages = [];
            foreach ($messages as $msg) {
                $formattedMessages[] = [
                    'id' => $msg->id,
                    'sender_id' => $msg->sender_id,
                    'is_me' => $msg->sender_id == $userId,
                    'content' => nl2br(e($msg->content)),
                    'time' => $msg->created_at->format('h:i A'),
                    'time_ago' => $msg->created_at->diffForHumans(),
                    'date' => $msg->created_at->format('Y-m-d'),
                ];
            }

            return response()->json([
                'status' => 'success',
                'other_user' => [
                    'id' => $otherUser->id,
                    'name' => $displayName,
                    'role' => $otherUser->role,
                    'is_company' => $isCompany,
                    'avatar' => $avatar,
                    'initial' => mb_substr($displayName, 0, 1, 'UTF-8'),
                ],
                'messages' => $formattedMessages,
            ]);
        }

        list($conversations, $totalUnreadCount) = $this->getConversationsData($userId);
        $availableContacts = $this->getAvailableContacts($currentUser);

        return view('messages.show', compact('otherUser', 'messages', 'conversations', 'availableContacts', 'totalUnreadCount'));
    }

    /**
     * إرسال رسالة جديدة
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:2000'
        ]);

        $sender = auth()->user();
        $receiver = User::find($id);

        if (!$receiver) {
            $company = \App\Models\Company::find($id);
            if ($company && $company->user_id) {
                $receiver = User::find($company->user_id);
            }
        }

        if (!$receiver) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'المستلم غير موجود.'], 404);
            }
            return back()->with('error', 'المستلم غير موجود.');
        }

        // فحص صلاحية المراسلة
        if (!$this->canMessageUser($sender, $receiver)) {
            $errorMsg = 'غير مصرح لك بإرسال رسائل لهذا المستخدم.';
            if ($sender->role === 'graduate') {
                $errorMsg = 'غير مصرح للخريج بمراسلة هذا المستخدم. التواصل متاح فقط وحصرياً مع مسؤولي إدارة شؤون الخريجين.';
            } elseif ($sender->isAdmin() && $receiver->role === 'graduate') {
                $errorMsg = 'لا يمكن لمدير النظام مراسلة الخريجين مباشرة؛ التواصل مع الخريجين محصور بإدارة الخريجين.';
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $errorMsg], 403);
            }
            return back()->with('error', $errorMsg);
        }

        $id = $receiver->id; // Normalize

        $message = Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $id,
            'content' => $request->input('content')
        ]);

        // إشعار المستلم
        try {
            $senderDisplayName = ($sender->role === 'company' && $sender->company)
                ? $sender->company->name
                : $sender->name;

            \App\Models\Notification::create([
                'title' => 'رسالة جديدة من ' . $senderDisplayName,
                'message' => \Illuminate\Support\Str::limit($request->input('content'), 80),
                'type' => 'message',
                'user_id' => $id,
                'sender_id' => $sender->id,
                'model_type' => Message::class,
                'model_id' => $message->id,
                'data' => [
                    'url' => route('messages.show', $sender->id),
                    'sender_name' => $senderDisplayName,
                ],
                'sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create notification for message: ' . $e->getMessage());
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'تم إرسال الرسالة بنجاح',
                'data' => [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'is_me' => true,
                    'content' => nl2br(e($message->content)),
                    'time' => $message->created_at->format('h:i A'),
                    'time_ago' => $message->created_at->diffForHumans(),
                    'date' => $message->created_at->format('Y-m-d'),
                ]
            ]);
        }

        return back()->with('success', 'تم إرسال الرسالة بنجاح.');
    }

    /**
     * حذف محادثة بالكامل (كافة الرسائل المتبادلة بين الطرفين)
     */
    public function destroyConversation(Request $request, $id)
    {
        $currentUser = auth()->user();
        $userId = $currentUser->id;

        $otherUser = User::find($id);
        if (!$otherUser) {
            $company = \App\Models\Company::find($id);
            if ($company && $company->user_id) {
                $otherUser = User::find($company->user_id);
            }
        }

        if (!$otherUser) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'المستخدم غير موجود.'], 404);
            }
            return redirect()->route('messages.index')->with('error', 'المستخدم غير موجود.');
        }

        $otherId = $otherUser->id;

        // حذف كافة الرسائل المتبادلة بين المستخدمين
        $deletedCount = Message::where(function ($query) use ($userId, $otherId) {
            $query->where('sender_id', $userId)->where('receiver_id', $otherId);
        })->orWhere(function ($query) use ($userId, $otherId) {
            $query->where('sender_id', $otherId)->where('receiver_id', $userId);
        })->delete();

        // حذف إشعارات الرسائل المرتبطة
        \App\Models\Notification::where(function ($q) use ($userId, $otherId) {
            $q->where('user_id', $userId)->where('sender_id', $otherId);
        })->orWhere(function ($q) use ($userId, $otherId) {
            $q->where('user_id', $otherId)->where('sender_id', $userId);
        })->where('type', 'message')->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'تم حذف المحادثة بنجاح.'
            ]);
        }

        return redirect()->route('messages.index')->with('success', 'تم حذف المحادثة وجميع الرسائل بنجاح.');
    }

    /**
     * حذف رسالة مفردة
     */
    public function destroyMessage(Request $request, $id)
    {
        $currentUser = auth()->user();
        $message = Message::findOrFail($id);

        if ((int)$message->sender_id !== (int)$currentUser->id && (int)$message->receiver_id !== (int)$currentUser->id && !$currentUser->isAdmin()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'غير مصرح لك بحذف هذه الرسالة.'], 403);
            }
            abort(403, 'غير مصرح لك بحذف هذه الرسالة.');
        }

        $message->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'تم حذف الرسالة بنجاح.'
            ]);
        }

        return back()->with('success', 'تم حذف الرسالة بنجاح.');
    }
}

