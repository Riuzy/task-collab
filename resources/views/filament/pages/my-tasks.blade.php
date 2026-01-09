<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($this->getTasks() as $task)
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-bold text-gray-900">{{ $task->title }}</h3>
                    <span @class([
                        'px-2 py-1 text-xs font-medium rounded-lg',
                        'bg-gray-100 text-gray-600' => $task->status === 'pending',
                        'bg-blue-100 text-blue-600' => $task->status === 'in_progress',
                        'bg-green-100 text-green-600' => $task->status === 'completed',
                    ])>
                        {{ strtoupper($task->status) }}
                    </span>
                </div>
                <p class="text-gray-600 text-sm mb-4">{{ $task->description }}</p>
                <div class="text-xs text-gray-400">
                    Dibuat: {{ $task->created_at->format('d M Y') }}
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>