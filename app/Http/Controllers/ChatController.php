<?php

namespace App\Http\Controllers;

use App\Events\MessageSentEvent;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
    public function chat(Request $request, User $friend): Response
    {
        return Inertia::render('Chat/Inbox', [
            'currentUser' => $request->user(),
            'friend' => $friend,
        ]);
    }

    public function messages(User $friend)
    {
        return ChatMessage::query()
            ->where(function ($query) use ($friend) {
                $query->where('sender_id', auth()->id())
                    ->where('receiver_id', $friend->id);
            })
            ->orWhere(function ($query) use ($friend) {
                $query->where('sender_id', $friend->id)
                    ->where('receiver_id', auth()->id());
            })
            ->with(['sender', 'receiver'])
            ->orderBy('id')
            ->get();
    }

    public function sendMessages(Request $request, User $friend)
    {
        $message = ChatMessage::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $friend->id,
            'text' => $request->message
        ]);

        broadcast(new MessageSentEvent($message));

        return $message;
    }
}
