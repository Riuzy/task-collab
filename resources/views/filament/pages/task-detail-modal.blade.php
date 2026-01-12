<div class="space-y-6">
    <div>
        <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Deskripsi Tugas</h4>
        <p class="mt-2 text-gray-700 bg-gray-50 p-4 rounded-lg border border-gray-100">
            {{ $task->description }}
        </p>
    </div>
    <div class="space-y-4">
        @forelse($task->progress as $log)
            @php
                // Cek apakah ini feedback dari Admin
                $isAdminFeedback = str_contains($log->content, '[FEEDBACK ADMIN]');
            @endphp

            <div
                class="p-3 rounded-lg border {{ $isAdminFeedback ? 'bg-amber-50 border-amber-200' : 'bg-white border-gray-200' }}">
                <div class="flex justify-between items-center mb-1">
                    <span class="text-xs font-bold {{ $isAdminFeedback ? 'text-amber-700' : 'text-blue-600' }}">
                        {{ $log->user->name }} {{ $isAdminFeedback ? '(Admin)' : '' }}
                    </span>
                    <span class="text-[10px] text-gray-400">
                        {{ $log->created_at->format('d M Y H:i') }} WIB
                    </span>
                </div>
                <p class="text-sm {{ $isAdminFeedback ? 'text-amber-900 font-medium' : 'text-gray-600' }}">
                    {{ $log->content }}
                </p>
            </div>
        @empty
            <p class="text-sm text-gray-400 italic text-center py-4">Belum ada progres.</p>
        @endforelse
    </div>