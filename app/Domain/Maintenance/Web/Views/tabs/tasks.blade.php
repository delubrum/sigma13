<div class="mb-5">
    @if ($canEdit)
        <div class="flex items-center justify-end mb-3">
            <button 
                @click="app.modal.isOpen = true; app.modal.title = 'New Task'"
                class="px-3 py-1.5 rounded-md text-sm font-medium bg-black text-white"
                hx-get="{{ route('maintenance.modal') }}?modal=task&id={{ $id->id }}"
                hx-target="#modal-body"
                hx-indicator="#loading">
                <i class="ri-add-line text-xs"></i>
                <span>New Task</span>
            </button>
        </div>
    @endif

    <div class="overflow-x-auto border rounded-lg shadow-sm" style="border-color:var(--b)">
        <table class="w-full text-xs text-left" style="color:var(--tx)">
            <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700" style="background:var(--bg2)">
                <tr>
                    <th scope="col" class="px-4 py-3">Date</th>
                    <th scope="col" class="px-4 py-3">Operator</th>
                    <th scope="col" class="px-4 py-3">Complexity</th>
                    <th scope="col" class="px-4 py-3">Attends</th>
                    <th scope="col" class="px-4 py-3">Time (m)</th>
                    <th scope="col" class="px-4 py-3 max-w-sm">Notes</th>
                    <th scope="col" class="px-4 py-3 text-right">File</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tasks as $task)
                    <tr class="border-b last:border-b-0 hover:bg-gray-50" style="border-color:var(--b)">
                        <td class="px-4 py-3 whitespace-nowrap">{{ $task->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $task->user->username ?? '—' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $task->complexity ?? '—' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $task->attends ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $task->duration ?? 0 }}</td>
                        <td class="px-4 py-3 break-words max-w-xs">{{ $task->notes ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            @php
                                $directorio = "uploads/mnt/pics/{$task->id}/";
                                $files = glob(public_path($directorio).'*');
                            @endphp
                            @if(!empty($files))
                                @php
                                    sort($files);
                                    $file = $files[0];
                                    $fileName = basename($file);
                                @endphp
                                <a class="font-medium text-blue-600 hover:underline inline-flex items-center gap-1" target="_blank" href="{{ asset($directorio.$fileName) }}">
                                    <i class="ri-file-search-line line"></i>
                                </a>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500 italic">No tasks assigned yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
