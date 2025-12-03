<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Webhook Debug - Evolution API') }}
            </h2>
            <form action="{{ route('webhook-debug.clear') }}" method="POST" onsubmit="return confirm('Tem certeza que deseja limpar todos os logs?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm">
                    Limpar Todos os Logs
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold">Logs de Webhooks Recebidos</h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Total: {{ $logs->total() }}</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Evento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data/Hora</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($logs as $log)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono">
                                            #{{ $log->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                {{ $log->event_type }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($log->status === 'processed')
                                                <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                    ✓ Processado
                                                </span>
                                            @elseif($log->status === 'error')
                                                <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                                    ✗ Erro
                                                </span>
                                            @elseif($log->status === 'ignored')
                                                <span class="px-2 py-1 text-xs font-semibold rounded bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                                    ⊘ Ignorado
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                                    ○ Recebido
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('webhook-debug.show', $log->id) }}" 
                                               class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                                Ver Detalhes →
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            Nenhum webhook recebido ainda. Configure o webhook na Evolution API e envie uma mensagem de teste.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                <h4 class="font-semibold text-blue-900 dark:text-blue-200 mb-3">📋 Como usar o Debug:</h4>
                <ol class="list-decimal list-inside space-y-2 text-sm text-blue-800 dark:text-blue-300">
                    <li>Configure o webhook na Evolution API apontando para: <code class="bg-blue-100 dark:bg-blue-900 px-2 py-1 rounded">{{ url('/api/webhook/evolution') }}</code></li>
                    <li>Envie uma mensagem de teste para o número conectado</li>
                    <li>O webhook aparecerá aqui automaticamente</li>
                    <li>Clique em "Ver Detalhes" para visualizar o payload completo</li>
                    <li>Verifique se o status está como "Processado" (verde)</li>
                    <li>Se houver erro, o payload completo estará disponível para debug</li>
                </ol>
            </div>
        </div>
    </div>
</x-app-layout>
