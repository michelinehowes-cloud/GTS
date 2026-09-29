<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        
        // جلب الرسائل الأخيرة لكل محادثة (مجمعة حسب الشخص الآخر)
        $messages = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['sender.company', 'sender.graduateData', 'receiver.company', 'receiver.graduateData'])
            ->latest()
            ->get();

        // تجميع المحادثات
        $conversations = [];
        $totalUnreadCount = 0;
        foreach ($messages as $msg) {
            $otherUserId = $msg->sender_id == $userId ? $msg->receiver_id : $msg->sender_id;
            
            if (!isset($conversations[$otherUserId])) {
                $conversations[$otherUserId] = [
                    'user' => $msg->sender_id == $userId ? $msg->receiver : $msg->sender,
                    'last_message' => $msg,
                    'unread_count' => 0
                ];
            }
            
            if ($msg->receiver_id == $userId && is_null($msg->read_at)) {
                $conversations[$otherUserId]['unread_count']++;
                $totalUnreadCount++;
            }
        }

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

        return view('messages.index', compact('conversations'));
    }

    public function show(Request $request, $id)
    {
        $userId = auth()->id();
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

        $id = $otherUser->id; // Normalize to user id

        $messages = Message::where(function($query) use ($userId, $id) {
                $query->where('sender_id', $userId)->where('receiver_id', $id);
            })
            ->orWhere(function($query) use ($userId, $id) {
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

        return view('messages.show', compact('otherUser', 'messages'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

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

        $id = $receiver->id; // Normalize

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $id,
            'content' => $request->input('content')
        ]);

        // إشعار المستلم
        try {
            $sender = auth()->user();
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
}
