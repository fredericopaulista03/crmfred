<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\WebhookLog;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function evolution(Request $request)
    {
        $data = $request->all();
        
        // Log the webhook for debugging
        $webhookLog = WebhookLog::create([
            'event_type' => $data['event'] ?? 'unknown',
            'payload' => json_encode($data),
            'status' => 'received',
        ]);

        \Log::info('Evolution Webhook Received', [
            'log_id' => $webhookLog->id,
            'event' => $data['event'] ?? 'unknown',
            'data' => $data
        ]);

        try {
            // Handle different event types
            if (isset($data['event'])) {
                switch ($data['event']) {
                    case 'messages.upsert':
                        $this->handleIncomingMessage($data, $webhookLog);
                        break;
                    case 'messages.update':
                        $this->handleMessageUpdate($data, $webhookLog);
                        break;
                    default:
                        $webhookLog->update([
                            'status' => 'ignored',
                            'error_message' => 'Event type not handled: ' . $data['event']
                        ]);
                }
            }

            return response()->json(['status' => 'success', 'log_id' => $webhookLog->id]);
        } catch (\Exception $e) {
            $webhookLog->update([
                'status' => 'error',
                'error_message' => $e->getMessage()
            ]);

            \Log::error('Webhook Processing Error', [
                'log_id' => $webhookLog->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'log_id' => $webhookLog->id
            ], 500);
        }
    }

    protected function handleIncomingMessage($data, $webhookLog)
    {
        // Check if message is from us (fromMe = true)
        if (isset($data['data']['key']['fromMe']) && $data['data']['key']['fromMe']) {
            $webhookLog->update([
                'status' => 'ignored',
                'error_message' => 'Message sent by us (fromMe=true)'
            ]);
            return; // Ignore messages sent by us
        }

        $messageData = $data['data'];
        
        // Get contact number - try multiple possible locations
        $contactNumber = $data['sender'] ?? $messageData['key']['remoteJid'] ?? null;
        
        // Clean the contact number (remove @s.whatsapp.net if present)
        if ($contactNumber) {
            $contactNumber = str_replace('@s.whatsapp.net', '', $contactNumber);
        }

        if (!$contactNumber) {
            $webhookLog->update([
                'status' => 'error',
                'error_message' => 'Contact number not found in payload'
            ]);
            return;
        }

        // Find or create conversation
        $conversation = Conversation::firstOrCreate(
            ['contact_number' => $contactNumber],
            [
                'contact_name' => $messageData['pushName'] ?? 'Desconhecido',
                'last_message_at' => now(),
            ]
        );

        // Update contact name if it changed
        if (isset($messageData['pushName']) && $messageData['pushName'] !== $conversation->contact_name) {
            $conversation->update(['contact_name' => $messageData['pushName']]);
        }

        // Extract message content based on messageType
        $messageType = $messageData['messageType'] ?? 'text';
        $messageBody = '';
        $mediaUrl = null;

        // Handle different message types
        if (isset($messageData['message']['conversation'])) {
            $messageType = 'text';
            $messageBody = $messageData['message']['conversation'];
        } elseif (isset($messageData['message']['extendedTextMessage'])) {
            $messageType = 'text';
            $messageBody = $messageData['message']['extendedTextMessage']['text'] ?? '';
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
        } elseif (isset($messageData['message']['videoMessage'])) {
            $messageType = 'image'; // Using image type for video for now
            $messageBody = $messageData['message']['videoMessage']['caption'] ?? '';
            $mediaUrl = $messageData['message']['videoMessage']['url'] ?? null;
        }

        // Save message
        $message = Message::create([
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

        // Mark webhook as processed
        $webhookLog->update([
            'status' => 'processed',
            'processed' => true
        ]);

        \Log::info('Message processed successfully', [
            'log_id' => $webhookLog->id,
            'conversation_id' => $conversation->id,
            'message_id' => $message->id,
            'message_type' => $messageType,
            'contact_number' => $contactNumber,
            'contact_name' => $messageData['pushName'] ?? 'Unknown'
        ]);
    }

    protected function handleMessageUpdate($data, $webhookLog)
    {
        // Handle message status updates (delivered, read)
        // Implementation depends on Evolution API structure
    }
}
