@php
    /** @var \App\Models\Recruitment $recruitment */
    /** @var \Illuminate\Support\Collection $candidates */
@endphp

<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-sigma-b pb-4">
        <div>
            <h3 class="text-sm font-semibold text-tx">Candidates List</h3>
            <p class="text-[10px] text-tx2">Manage candidates assigned to this recruitment process.</p>
        </div>
        <button class="px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-widest flex items-center gap-1 transition-all"
                style="background:var(--ac); color:var(--ac-inv)"
                onclick="alert('Not fully implemented in this preview')">
            <i class="ri-user-add-line text-sm"></i>
            <span>Add Candidate</span>
        </button>
    </div>

    @if($candidates->isEmpty())
        <div class="p-8 text-center border-2 border-dashed border-sigma-b rounded-xl">
            <i class="ri-group-line text-4xl text-tx2 mb-2 block"></i>
            <h4 class="text-sm font-medium text-tx mb-1">No candidates yet</h4>
            <p class="text-xs text-tx2 max-w-sm mx-auto">There are no candidates associated with this recruitment. Click "Add Candidate" to start tracking applicants.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($candidates as $candidate)
                @php
                    $statusColor = match(strtolower($candidate->status)) {
                        'new' => 'color:var(--info); border-color:var(--info-muted); background:var(--info-bg)',
                        'interviewing' => 'color:var(--warning); border-color:var(--warning-muted); background:var(--warning-bg)',
                        'hired' => 'color:var(--success); border-color:var(--success-muted); background:var(--success-bg)',
                        'rejected' => 'color:var(--danger); border-color:var(--danger-muted); background:var(--danger-bg)',
                        default => 'color:var(--tx2); border-color:var(--b); background:var(--bg2)',
                    };
                @endphp
                <div class="bg-sigma-bg p-4 rounded-xl border border-sigma-b shadow-sm relative hover:border-blue-400 transition-colors group">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex gap-3 items-center w-full">
                            <div class="w-10 h-10 rounded-full bg-sigma-bg2 flex items-center justify-center border border-sigma-b text-tx2 shrink-0">
                                <i class="ri-user-smile-line text-lg"></i>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-tx truncate">{{ $candidate->name }}</h4>
                                <p class="text-[10px] text-tx2 truncate">{{ $candidate->email ?? 'No email' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between items-center pb-2 border-b border-dashed border-sigma-b">
                            <span class="text-tx2">Status</span>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider border" style="{{ $statusColor }}">
                                {{ $candidate->status ?? 'new' }}
                            </span>
                        </div>

                        <div>
                            <span class="text-tx2 block mb-1">Concept</span>
                            <p class="text-tx text-[10px] leading-relaxed line-clamp-2">{{ $candidate->concept ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
