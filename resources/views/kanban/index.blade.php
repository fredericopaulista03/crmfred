<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kanban Board') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="kanban()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex overflow-x-auto space-x-4 pb-4">
                @foreach($columns as $column)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow w-80 flex-shrink-0 p-4 flex flex-col">
                        <h3 class="font-bold text-gray-700 dark:text-gray-300 mb-4">{{ $column->title }}</h3>
                        
                        <div class="space-y-3 flex-1 min-h-[100px]" 
                             @dragover.prevent 
                             @drop.prevent="drop($event, {{ $column->id }})">
                            @foreach($column->cards as $card)
                                <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded shadow-sm cursor-move border dark:border-gray-600"
                                     draggable="true"
                                     @dragstart="dragStart($event, {{ $card->id }})">
                                    <h4 class="font-semibold text-sm dark:text-white">{{ $card->title }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] 
                                            {{ $card->priority === 'high' ? 'bg-red-100 text-red-800' : 
                                               ($card->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                            {{ ucfirst($card->priority) }}
                                        </span>
                                    </p>
                                </div>
                            @endforeach
                        </div>

                        <!-- Add Card Form -->
                        <form action="{{ route('kanban.cards.store') }}" method="POST" class="mt-4">
                            @csrf
                            <input type="hidden" name="kanban_column_id" value="{{ $column->id }}">
                            <input type="text" name="title" placeholder="Add card..." class="w-full text-sm rounded border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500">
                        </form>
                    </div>
                @endforeach

                <!-- Add Column -->
                <div class="w-80 flex-shrink-0">
                    <form action="{{ route('kanban.columns.store') }}" method="POST">
                        @csrf
                        <input type="text" name="title" placeholder="New Column" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function kanban() {
            return {
                draggingCardId: null,
                dragStart(event, cardId) {
                    this.draggingCardId = cardId;
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', cardId);
                },
                drop(event, columnId) {
                    if (!this.draggingCardId) return;
                    
                    fetch('{{ route('kanban.cards.reorder') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            card_id: this.draggingCardId,
                            kanban_column_id: columnId,
                            order: 0 // Simplified: just moving to column for now
                        })
                    }).then(() => {
                        window.location.reload();
                    });
                }
            }
        }
    </script>
</x-app-layout>
