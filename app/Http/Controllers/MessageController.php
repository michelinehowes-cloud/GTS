<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        
        // جلب الرسائل الأخيرة لكل محادثة (مجمعة حسب الشخص الآخر)
        $messages = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['sender', 'receiver'])
            ->latest()
            ->get();

        // تجميع المحادثات
        $conversations = [];
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
            }
        }

        return view('messages.index', compact('conversations'));
    }

    public function show($id)
    {
        $userId = auth()->id();
        $otherUser = User::findOrFail($id);

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

        return view('messages.show', compact('otherUser', 'messages'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $id,
            'content' => $request->input('content')
        ]);

        return back()->with('success', 'تم إرسال الرسالة بنجاح.');
    }
}
