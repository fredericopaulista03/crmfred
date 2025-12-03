<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Configurações') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Evolution API Settings -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        Evolution API - Configuração WhatsApp
                    </h3>

                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            <!-- API URL -->
                            <div>
                                <label for="evolution_api_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    URL da API Evolution
                                </label>
                                <input type="url" 
                                       name="evolution_api_url" 
                                       id="evolution_api_url" 
                                       value="{{ old('evolution_api_url', $settings['evolution_api_url']) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="https://api.evolution.com"
                                       required>
                                @error('evolution_api_url')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- API Token -->
                            <div>
                                <label for="evolution_api_token" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Token da API
                                </label>
                                <input type="text" 
                                       name="evolution_api_token" 
                                       id="evolution_api_token" 
                                       value="{{ old('evolution_api_token', $settings['evolution_api_token']) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="seu-token-aqui"
                                       required>
                                @error('evolution_api_token')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Instance Name -->
                            <div>
                                <label for="evolution_instance_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Nome da Instância
                                </label>
                                <input type="text" 
                                       name="evolution_instance_name" 
                                       id="evolution_instance_name" 
                                       value="{{ old('evolution_instance_name', $settings['evolution_instance_name']) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="minha-instancia"
                                       required>
                                @error('evolution_instance_name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center space-x-4 pt-4">
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Salvar Configurações
                                </button>
                            </div>
                        </form>

                        <!-- Test Connection Button -->
                        <form action="{{ route('settings.test') }}" method="POST" class="mt-4">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Testar Conexão
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Webhook Information -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Informações do Webhook
                    </h3>

                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                            Configure este URL no painel da Evolution API para receber mensagens:
                        </p>
                        <div class="flex items-center space-x-2">
                            <input type="text" 
                                   value="{{ $settings['webhook_url'] }}" 
                                   readonly 
                                   class="flex-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm font-mono text-gray-900 dark:text-gray-100"
                                   id="webhook-url">
                            <button type="button" 
                                    onclick="copyWebhookUrl()"
                                    class="inline-flex items-center px-3 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                Copiar
                            </button>
                        </div>
                    </div>

                    <div class="mt-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <h4 class="font-semibold text-blue-900 dark:text-blue-200 mb-2">📋 Instruções de Configuração:</h4>
                        <ol class="list-decimal list-inside space-y-1 text-sm text-blue-800 dark:text-blue-300">
                            <li>Acesse o painel da Evolution API</li>
                            <li>Vá em Configurações → Webhooks</li>
                            <li>Cole a URL do webhook acima</li>
                            <li>Ative os eventos: <code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">messages.upsert</code> e <code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">messages.update</code></li>
                            <li>Salve as configurações</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyWebhookUrl() {
            const input = document.getElementById('webhook-url');
            input.select();
            document.execCommand('copy');
            
            // Show feedback
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Copiado!';
            
            setTimeout(() => {
                button.innerHTML = originalText;
            }, 2000);
        }
    </script>
</x-app-layout>
