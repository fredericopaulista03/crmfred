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
            'event_type' => $data['event'] ?? 'messages.upsert',
            'payload' => json_encode($data),
            'status' => 'received',
        ]);

        \Log::info('Evolution Webhook Received', [
            'log_id' => $webhookLog->id,
            'event' => $data['event'] ?? 'auto-detected',
            'data' => $data
        ]);

        try {
            // If event field exists, use it. Otherwise, auto-detect based on payload
            $event = $data['event'] ?? null;
            
            // Auto-detect: if has sender and message, it's a message.upsert
            if (!$event && isset($data['sender']) && isset($data['message'])) {
                $event = 'messages.upsert';
            }

            // Handle different event types
            if ($event) {
                switch ($event) {
                    case 'messages.upsert':
                        $this->handleIncomingMessage($data, $webhookLog);
                        break;
                    case 'messages.update':
                        $this->handleMessageUpdate($data, $webhookLog);
                        break;
                    default:
                        $webhookLog->update([
                            'status' => 'ignored',
                            'error_message' => 'Event type not handled: ' . $event
                        ]);
                }
            } else {
                $webhookLog->update([
                    'status' => 'error',
                    'error_message' => 'Could not determine event type from payload'
                ]);
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

        // Support both full payload and simplified N8N payload
        $messageData = $data['data'] ?? [];
        
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

        // Get contact name
        $contactName = $messageData['pushName'] ?? $data['pushName'] ?? 'Desconhecido';

        // Find or create conversation
        $conversation = Conversation::firstOrCreate(
            ['contact_number' => $contactNumber],
            [
                'contact_name' => $contactName,
                'last_message_at' => now(),
            ]
        );

        // Update contact name if it changed
        if ($contactName !== 'Desconhecido' && $contactName !== $conversation->contact_name) {
            $conversation->update(['contact_name' => $contactName]);
        }

        // Extract message content - support both structures
        $messageType = 'text';
        $messageBody = '';
        $mediaUrl = null;

        // Check if message is in root level (N8N simplified format)
        if (isset($data['message']) && is_string($data['message'])) {
            $messageType = 'text';
            $messageBody = $data['message'];
        }
        // Check if message is in data.message (full Evolution API format)
        elseif (isset($messageData['message'])) {
            $message = $messageData['message'];
            
            if (isset($message['conversation'])) {
                $messageType = 'text';
                $messageBody = $message['conversation'];
            } elseif (isset($message['extendedTextMessage'])) {
                $messageType = 'text';
                $messageBody = $message['extendedTextMessage']['text'] ?? '';
            } elseif (isset($message['imageMessage'])) {
                $messageType = 'image';
                $messageBody = $message['imageMessage']['caption'] ?? '';
                $mediaUrl = $message['imageMessage']['url'] ?? null;
            } elseif (isset($message['audioMessage'])) {
                $messageType = 'audio';
                $mediaUrl = $message['audioMessage']['url'] ?? null;
            } elseif (isset($message['documentMessage'])) {
                $messageType = 'document';
                $messageBody = $message['documentMessage']['fileName'] ?? '';
                $mediaUrl = $message['documentMessage']['url'] ?? null;
            } elseif (isset($message['videoMessage'])) {
                $messageType = 'image'; // Using image type for video for now
                $messageBody = $message['videoMessage']['caption'] ?? '';
                $mediaUrl = $message['videoMessage']['url'] ?? null;
            }
        }

        // If still no message body, try to get from messageType field
        if (empty($messageBody) && isset($messageData['messageType'])) {
            $messageType = $messageData['messageType'];
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
            'contact_name' => $contactName,
            'message_body' => $messageBody
        ]);
    }

    protected function handleMessageUpdate($data, $webhookLog)
    {
        // Handle message status updates (delivered, read)
        // Implementation depends on Evolution API structure
    }
}
