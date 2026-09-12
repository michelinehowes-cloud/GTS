<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiChatMessage extends Model
{
    use HasFactory;

    protected $table = 'ai_chat_messages';

    protected $fillable = [
        'user_id',
        'session_id',
        'role', // user, assistant, system, tool
        'content',
        'tool_calls',
        'tool_results',
        'meta_data',
    ];

    protected $casts = [
        'tool_calls' => 'array',
        'tool_results' => 'array',
        'meta_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
