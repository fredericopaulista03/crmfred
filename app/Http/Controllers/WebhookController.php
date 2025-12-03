<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function evolution(Request $request)
    {
        $data = $request->all();
        \Log::info('Evolution Webhook Received', $data);

        // Handle different event types
        if (isset($data['event'])) {
            switch ($data['event']) {
                case 'messages.upsert':
                    $this->handleIncomingMessage($data);
                    break;
                case 'messages.update':
                    $this->handleMessageUpdate($data);
                    break;
            }
        }

        return response()->json(['status' => 'success']);
    }

    protected function handleIncomingMessage($data)
    {
        if (!isset($data['data']['key']['fromMe']) || $data['data']['key']['fromMe']) {
            return; // Ignore messages sent by us
        }

        $messageData = $data['data'];
        $contactNumber = $messageData['key']['remoteJid'] ?? null;

        if (!$contactNumber) return;

        // Find or create conversation
        $conversation = Conversation::firstOrCreate(
            ['contact_number' => $contactNumber],
            [
                'contact_name' => $messageData['pushName'] ?? 'Unknown',
                'last_message_at' => now(),
            ]
        );

        // Extract message content
        $messageType = 'text';
        $messageBody = '';
        $mediaUrl = null;

        if (isset($messageData['message']['conversation'])) {
            $messageBody = $messageData['message']['conversation'];
        } elseif (isset($messageData['message']['extendedTextMessage'])) {
            $messageBody = $messageData['message']['extendedTextMessage']['text'];
        } elseif (isset($messageData['message']['imageMessage'])) {
            $messageType = 'image';
            $messageBody = $messageData['message']['imageMessage']['caption'] ?? '';
            $mediaUrl = $messageData['message']['imageMessage']['url'] ?? null;
        } elseif (isset($messageData['message']['audioMessage'])) {
            $messageType = 'audio';
            $mediaUrl = $messageData['message']['audioMessage']['url'] ?? null;
        } elseif (isset($messageData['message']['documentMessage'])) {
            $messageType = 'document';
            $messageBody = $messageData['message']['documentMessage']['fileName'] ?? '';
            $mediaUrl = $messageData['message']['documentMessage']['url'] ?? null;
        }

        // Save message
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'contact',
            'type' => $messageType,
            'body' => $messageBody,
            'media_url' => $mediaUrl,
            'status' => 'delivered',
        ]);

        // Update conversation
        $conversation->update([
            'last_message_at' => now(),
            'unread_count' => $conversation->unread_count + 1,
        ]);
    }

    protected function handleMessageUpdate($data)
    {
        // Handle message status updates (delivered, read)
        // Implementation depends on Evolution API structure
    }
}
