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
            'message' => 'required_without:attachment',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        $conversation = Conversation::findOrFail($request->conversation_id);
        
        $messageType = 'text';
        $messageBody = $request->message ?? '';
        $mediaUrl = null;

        // Handle file attachment
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('chat-attachments', 'public');
            $mediaUrl = asset('storage/' . $path);
            
            // Determine message type based on file mime type
            $mimeType = $file->getMimeType();
            if (str_starts_with($mimeType, 'image/')) {
                $messageType = 'image';
            } elseif (str_starts_with($mimeType, 'audio/')) {
                $messageType = 'audio';
            } elseif (str_starts_with($mimeType, 'video/')) {
                $messageType = 'image'; // Using image type for video
            } else {
                $messageType = 'document';
            }
            
            // If no message text, use filename
            if (empty($messageBody)) {
                $messageBody = $file->getClientOriginalName();
            }
        }

        // Send via Evolution API only if it's a text message
        if ($messageType === 'text') {
            $response = $this->evolutionApi->sendText($conversation->contact_number, $messageBody);
        }
        // For media, you might need to use sendMedia method
        // $this->evolutionApi->sendMedia($conversation->contact_number, $mediaUrl, $messageType, $messageBody);

        // Save to database
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'user',
            'sender_id' => auth()->id(),
            'type' => $messageType,
            'body' => $messageBody,
            'media_url' => $mediaUrl,
            'status' => 'sent',
        ]);

        $conversation->update(['last_message_at' => now()]);

        // Send to N8N webhook if configured
        $n8nWebhookUrl = \App\Models\Setting::get('n8n_webhook_url');
        if ($n8nWebhookUrl) {
            try {
                \Http::post($n8nWebhookUrl, [
                    'event' => 'message.sent',
                    'instance' => \App\Models\Setting::get('evolution_instance_name', 'default'),
                    'conversation_id' => $conversation->id,
                    'contact_number' => $conversation->contact_number,
                    'contact_name' => $conversation->contact_name,
                    'message' => $messageBody,
                    'message_type' => $messageType,
                    'media_url' => $mediaUrl,
                    'sender' => auth()->user()->name,
                    'sender_id' => auth()->id(),
                    'timestamp' => now()->toIso8601String(),
                ]);
            } catch (\Exception $e) {
                \Log::warning('Failed to send message to N8N webhook', [
                    'error' => $e->getMessage(),
                    'webhook_url' => $n8nWebhookUrl
                ]);
            }
        }

        return back();
    }
}
