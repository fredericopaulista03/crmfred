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

        // Mark webhook as processed
        $webhookLog->update([
            'status' => 'processed',
            'processed' => true
        ]);

        \Log::info('Message processed successfully', [
            'log_id' => $webhookLog->id,
            'conversation_id' => $conversation->id,
            'message_type' => $messageType
        ]);
    }

    protected function handleMessageUpdate($data, $webhookLog)
    {
        // Handle message status updates (delivered, read)
        // Implementation depends on Evolution API structure
    }
}
