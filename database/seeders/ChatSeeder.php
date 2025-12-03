<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\KanbanCard;
use App\Models\KanbanColumn;
use App\Models\Message;
use Illuminate\Database\Seeder;

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample conversations
        $conv1 = Conversation::create([
            'contact_number' => '5511999999999',
            'contact_name' => 'João Silva',
            'status' => 'open',
            'last_message_at' => now()->subMinutes(5),
            'unread_count' => 2,
        ]);

        $conv2 = Conversation::create([
            'contact_number' => '5511888888888',
            'contact_name' => 'Maria Santos',
            'status' => 'open',
            'last_message_at' => now()->subHours(1),
            'unread_count' => 0,
        ]);

        // Create sample messages for conv1
        Message::create([
            'conversation_id' => $conv1->id,
            'sender_type' => 'contact',
            'type' => 'text',
            'body' => 'Olá, tudo bem?',
            'status' => 'delivered',
            'created_at' => now()->subMinutes(10),
        ]);

        Message::create([
            'conversation_id' => $conv1->id,
            'sender_type' => 'user',
            'sender_id' => 1,
            'type' => 'text',
            'body' => 'Sim, tudo ótimo! Como posso ajudar?',
            'status' => 'read',
            'created_at' => now()->subMinutes(8),
        ]);

        Message::create([
            'conversation_id' => $conv1->id,
            'sender_type' => 'contact',
            'type' => 'text',
            'body' => 'Gostaria de saber mais sobre os produtos.',
            'status' => 'delivered',
            'created_at' => now()->subMinutes(5),
        ]);

        // Create sample messages for conv2
        Message::create([
            'conversation_id' => $conv2->id,
            'sender_type' => 'contact',
            'type' => 'text',
            'body' => 'Bom dia!',
            'status' => 'delivered',
            'created_at' => now()->subHours(2),
        ]);

        Message::create([
            'conversation_id' => $conv2->id,
            'sender_type' => 'user',
            'sender_id' => 1,
            'type' => 'text',
            'body' => 'Bom dia! Em que posso ajudar?',
            'status' => 'read',
            'created_at' => now()->subHours(1),
        ]);

        // Create Kanban columns and cards
        $column1 = KanbanColumn::create([
            'title' => 'A Fazer',
            'order' => 0,
            'color' => '#3b82f6',
        ]);

        $column2 = KanbanColumn::create([
            'title' => 'Em Progresso',
            'order' => 1,
            'color' => '#f59e0b',
        ]);

        $column3 = KanbanColumn::create([
            'title' => 'Concluído',
            'order' => 2,
            'color' => '#10b981',
        ]);

        KanbanCard::create([
            'kanban_column_id' => $column1->id,
            'user_id' => 1,
            'title' => 'Configurar Evolution API',
            'description' => 'Integrar WhatsApp com Evolution API',
            'priority' => 'high',
            'due_date' => now()->addDays(3),
            'order' => 0,
        ]);

        KanbanCard::create([
            'kanban_column_id' => $column1->id,
            'user_id' => 1,
            'title' => 'Criar dashboard de vendas',
            'priority' => 'medium',
            'order' => 1,
        ]);

        KanbanCard::create([
            'kanban_column_id' => $column2->id,
            'user_id' => 1,
            'title' => 'Implementar sistema de notificações',
            'priority' => 'medium',
            'order' => 0,
        ]);

        KanbanCard::create([
            'kanban_column_id' => $column3->id,
            'user_id' => 1,
            'title' => 'Setup inicial do projeto',
            'priority' => 'low',
            'order' => 0,
        ]);
    }
}
