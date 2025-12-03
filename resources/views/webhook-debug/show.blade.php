<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('webhook-debug.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">
                ← Voltar
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Webhook #{{ $log->id }} - {{ $log->event_type }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Status Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">ID</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">#{{ $log->id }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Evento</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $log->event_type }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                            <p class="text-lg font-semibold">
                                @if($log->status === 'processed')
                                    <span class="text-green-600 dark:text-green-400">✓ Processado</span>
                                @elseif($log->status === 'error')
                                    <span class="text-red-600 dark:text-red-400">✗ Erro</span>
                                @elseif($log->status === 'ignored')
                                    <span class="text-yellow-600 dark:text-yellow-400">⊘ Ignorado</span>
                                @else
                                    <span class="text-gray-600 dark:text-gray-400">○ Recebido</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Data/Hora</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $log->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>

                    @if($log->error_message)
                        <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded">
                            <p class="text-sm font-semibold text-red-800 dark:text-red-200 mb-2">Mensagem de Erro:</p>
                            <p class="text-sm text-red-700 dark:text-red-300 font-mono">{{ $log->error_message }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payload Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Payload Completo</h3>
                        <button onclick="copyPayload()" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm">
                            Copiar JSON
                        </button>
                    </div>
                    
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre id="payload-json" class="text-sm text-green-400 font-mono">{{ json_encode(json_decode($log->payload), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                </div>
            </div>

            <!-- Parsed Data (if available) -->
            @php
                $data = json_decode($log->payload, true);
            @endphp

            @if(isset($data['data']))
                <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Dados Extraídos</h3>
                        
                        <div class="space-y-3">
                            @if(isset($data['data']['key']['remoteJid']))
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Número do Contato:</p>
                                    <p class="text-md font-mono text-gray-900 dark:text-gray-100">{{ $data['data']['key']['remoteJid'] }}</p>
                                </div>
                            @endif

                            @if(isset($data['data']['pushName']))
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Nome:</p>
                                    <p class="text-md font-semibold text-gray-900 dark:text-gray-100">{{ $data['data']['pushName'] }}</p>
                                </div>
                            @endif

                            @if(isset($data['data']['message']))
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Mensagem:</p>
                                    <div class="mt-2 p-3 bg-gray-50 dark:bg-gray-900 rounded">
                                        <pre class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ json_encode($data['data']['message'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function copyPayload() {
            const payload = document.getElementById('payload-json').textContent;
            navigator.clipboard.writeText(payload).then(() => {
                alert('Payload copiado para a área de transferência!');
            });
        }
    </script>
</x-app-layout>
