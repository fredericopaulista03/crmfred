<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\EvolutionApiService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected $evolutionApi;

    public function __construct(EvolutionApiService $evolutionApi)
    {
        $this->evolutionApi = $evolutionApi;
    }

    public function index()
    {
        $conversations = Conversation::orderBy('last_message_at', 'desc')->get();
        $selectedConversation = null;
        $messages = [];

        if ($conversations->isNotEmpty()) {
            $selectedConversation = $conversations->first();
            $messages = $selectedConversation->messages()->orderBy('created_at', 'asc')->get();
        }

        return view('chat.index', compact('conversations', 'selectedConversation', 'messages'));
    }

    public function show($id)
    {
        $conversations = Conversation::orderBy('last_message_at', 'desc')->get();
        $selectedConversation = Conversation::findOrFail($id);
        $messages = $selectedConversation->messages()->orderBy('created_at', 'asc')->get();

        // Mark as read
        $selectedConversation->update(['unread_count' => 0]);

        return view('chat.index', compact('conversations', 'selectedConversation', 'messages'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'message' => 'required',
        ]);

        $conversation = Conversation::findOrFail($request->conversation_id);

        // Send via Evolution API
        $response = $this->evolutionApi->sendText($conversation->contact_number, $request->message);

        // Save to database
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'user',
            'sender_id' => auth()->id(),
            'type' => 'text',
            'body' => $request->message,
            'status' => 'sent',
        ]);

        $conversation->update(['last_message_at' => now()]);

        return back();
    }
}
