<x-app-layout>
    <div class="flex overflow-hidden bg-gray-900" style="height: 90vh;">
        <!-- Sidebar - Conversations List -->
        <div class="w-96 bg-gray-800 border-r border-gray-700 flex flex-col h-full">
            <!-- Header -->
            <div class="bg-gray-800 p-4 border-b border-gray-700 flex-shrink-0">
                <h2 class="text-white font-semibold text-lg">Conversas</h2>
                <p class="text-gray-400 text-sm">{{ $conversations->count() }} conversas</p>
            </div>

            <!-- Conversations List -->
            <div class="flex-1 overflow-y-auto">
                @forelse($conversations as $conversation)
                    <a href="{{ route('chat.show', $conversation->id) }}" 
                       class="block p-4 border-b border-gray-700 hover:bg-gray-700 transition {{ $selectedConversation && $selectedConversation->id === $conversation->id ? 'bg-gray-700' : '' }}">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-full bg-green-600 flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($conversation->contact_name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-baseline">
                                    <p class="text-white font-medium truncate">{{ $conversation->contact_name ?? $conversation->contact_number }}</p>
                                    <span class="text-xs text-gray-400">{{ $conversation->last_message_at?->format('H:i') }}</span>
                                </div>
                                <p class="text-sm text-gray-400 truncate">{{ $conversation->contact_number }}</p>
                                @if($conversation->unread_count > 0)
                                    <span class="inline-block mt-1 px-2 py-0.5 bg-green-600 text-white text-xs rounded-full">
                                        {{ $conversation->unread_count }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-gray-400">
                        <p>Nenhuma conversa ainda</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 flex flex-col h-full">
            @if($selectedConversation)
                <!-- Chat Header -->
                <div class="bg-gray-800 p-4 border-b border-gray-700 flex items-center justify-between flex-shrink-0">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($selectedConversation->contact_name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-white font-medium">{{ $selectedConversation->contact_name ?? $selectedConversation->contact_number }}</p>
                            <p class="text-xs text-green-400">{{ $selectedConversation->status === 'open' ? 'Online' : 'Offline' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Messages Area -->
                <div class="flex-1 overflow-y-auto p-4 bg-gray-900" style="background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZGVmcz48cGF0dGVybiBpZD0iYSIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBwYXR0ZXJuVHJhbnNmb3JtPSJyb3RhdGUoNDUpIj48cGF0aCBkPSJNLTEwIDMwaDYwdjJoLTYweiIgZmlsbD0iIzFhMWExYSIgZmlsbC1vcGFjaXR5PSIuMSIvPjwvcGF0dGVybj48L2RlZnM+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0idXJsKCNhKSIvPjwvc3ZnPg==');">
                    <div class="space-y-4">
                        @forelse($messages as $message)
                            <div class="flex {{ $message->sender_type === 'user' ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-md">
                                    <div class="rounded-lg p-3 {{ $message->sender_type === 'user' ? 'bg-green-700 text-white' : 'bg-gray-800 text-white' }}">
                                        @if($message->type === 'image' && $message->media_url)
                                            <img src="{{ $message->media_url }}" alt="Image" class="rounded mb-2 max-w-xs">
                                        @endif
                                        @if($message->type === 'audio' && $message->media_url)
                                            <audio controls class="mb-2">
                                                <source src="{{ $message->media_url }}">
                                            </audio>
                                        @endif
                                        @if($message->body)
                                            <p class="text-sm break-words">{{ $message->body }}</p>
                                        @endif
                                        <div class="flex items-center justify-end space-x-1 mt-1">
                                            <span class="text-xs opacity-70">{{ $message->created_at->format('H:i') }}</span>
                                            @if($message->sender_type === 'user')
                                                <svg class="w-4 h-4 {{ $message->status === 'read' ? 'text-blue-400' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-400 py-8">
                                <p>Nenhuma mensagem ainda</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Message Input -->
                <div class="bg-gray-800 p-4 border-t border-gray-700 flex-shrink-0" x-data="{ showEmoji: false, selectedFile: null }">
                    <form action="{{ route('chat.send') }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-3">
                        @csrf
                        <input type="hidden" name="conversation_id" value="{{ $selectedConversation->id }}">
                        
                        <!-- Emoji Button -->
                        <div class="relative">
                            <button type="button" 
                                    @click="showEmoji = !showEmoji"
                                    class="text-gray-400 hover:text-white transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </button>

                            <!-- Emoji Picker -->
                            <div x-show="showEmoji" 
                                 @click.away="showEmoji = false"
                                 x-transition
                                 class="absolute bottom-12 left-0 bg-gray-700 rounded-lg shadow-lg p-3 grid grid-cols-8 gap-2 w-80 max-h-64 overflow-y-auto z-50">
                                <template x-for="emoji in ['😀','😃','😄','😁','😆','😅','🤣','😂','🙂','🙃','😉','😊','😇','🥰','😍','🤩','😘','😗','😚','😙','🥲','😋','😛','😜','🤪','😝','🤑','🤗','🤭','🤫','🤔','🤐','🤨','😐','😑','😶','😏','😒','🙄','😬','🤥','😌','😔','😪','🤤','😴','😷','🤒','🤕','🤢','🤮','🤧','🥵','🥶','🥴','😵','🤯','🤠','🥳','🥸','😎','🤓','🧐','😕','😟','🙁','☹️','😮','😯','😲','😳','🥺','😦','😧','😨','😰','😥','😢','😭','😱','😖','😣','😞','😓','😩','😫','🥱','😤','😡','😠','🤬','😈','👿','💀','☠️','💩','🤡','👹','👺','👻','👽','👾','🤖','😺','😸','😹','😻','😼','😽','🙀','😿','😾']">
                                    <button type="button" 
                                            @click="document.querySelector('input[name=message]').value += emoji; showEmoji = false"
                                            class="text-2xl hover:bg-gray-600 rounded p-1 transition"
                                            x-text="emoji"></button>
                                </template>
                            </div>
                        </div>

                        <!-- Attachment Button -->
                        <div class="relative">
                            <input type="file" 
                                   name="attachment" 
                                   id="attachment" 
                                   class="hidden"
                                   accept="image/*,video/*,audio/*,.pdf,.doc,.docx"
                                   @change="selectedFile = $event.target.files[0]">
                            <button type="button" 
                                    @click="document.getElementById('attachment').click()"
                                    class="text-gray-400 hover:text-white transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Message Input -->
                        <div class="flex-1 relative">
                            <input type="text" 
                                   name="message" 
                                   placeholder="Digite sua mensagem..." 
                                   class="w-full bg-gray-700 text-white rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-600 border-0"
                                   :required="!selectedFile">
                            
                            <!-- File Preview -->
                            <div x-show="selectedFile" 
                                 class="absolute -top-16 left-0 bg-gray-700 rounded-lg p-2 flex items-center space-x-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-sm text-white" x-text="selectedFile ? selectedFile.name : ''"></span>
                                <button type="button" 
                                        @click="selectedFile = null; document.getElementById('attachment').value = ''"
                                        class="text-red-400 hover:text-red-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Send Button -->
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white rounded-full p-2 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </button>
                    </form>
                </div>
            @else
                <div class="flex-1 flex items-center justify-center bg-gray-900 text-gray-400">
                    <div class="text-center">
                        <svg class="w-24 h-24 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <p class="text-lg">Selecione uma conversa para começar</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
