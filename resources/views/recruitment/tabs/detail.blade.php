{{-- Tab Único: Detalle Base (Homologación SIGMA — sin tabbar si count(tabs)==1) --}}
@php
    /** @var \App\Models\Recruitment $recruitment */
    /** @var \Illuminate\Database\Eloquent\Collection $assignees */
@endphp

<div class="space-y-6">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Status and Critical Information --}}
        <div class="space-y-4">
            <x-sidebar-section icon="ri-information-line" label="Información Básica">
                <x-sidebar-row label="City"         :value="$recruitment->city ?? '—'" />
                <x-sidebar-row label="Work Mode"    :value="$recruitment->work_mode ?? '—'" />
                <x-sidebar-row label="Quantity"     :value="$recruitment->qty ?? '—'" />
                <x-sidebar-row label="Contract"     :value="$recruitment->contract ?? '—'" />
                <x-sidebar-row label="Cause"        :value="$recruitment->cause ?? '—'" />
                <x-sidebar-row label="Salary Range" :value="$recruitment->srange ?? '—'" />
                <x-sidebar-row label="Replaces"     :value="$recruitment->replaces ?? '—'" />
                <x-sidebar-row label="Start Date"   :value="$recruitment->start_date?->format('Y-m-d') ?? '—'" />
            </x-sidebar-section>

            @if(is_array($recruitment->resources) && count($recruitment->resources) > 0)
            <x-sidebar-section icon="ri-tools-line" label="Recursos Solicitados">
                <div class="space-y-1 text-xs px-2">
                    @foreach($recruitment->resources as $res)
                        <div class="flex items-center gap-2">
                            <i class="ri-arrow-right-s-line text-tx2"></i>
                            <span class="font-bold">{{ is_array($res) ? ($res['name'] ?? 'N/A') : $res }}</span>
                        </div>
                    @endforeach
                </div>
            </x-sidebar-section>
            @endif

            <x-sidebar-section icon="ri-file-text-line" label="Others / Details">
                <p class="text-xs leading-relaxed whitespace-pre-line bg-sigma-bg2 p-3 rounded-lg border border-sigma-b" style="color:var(--tx)">{{ $recruitment->others ?? 'No additional details provided.' }}</p>
            </x-sidebar-section>
        </div>

        {{-- Configuration and Assignment --}}
        <div class="space-y-4">
            <x-sidebar-section icon="ri-shield-user-line" label="Reclutador Responsable">
                <select data-widget="slimselect"
                        hx-post="{{ route('recruitment.update', $id) }}"
                        hx-trigger="change"
                        hx-vals='{"field": "assignee_id"}'
                        name="value">
                    <option value="">— Sin asignar —</option>
                    @foreach($assignees as $t)
                        <option value="{{ $t->id }}" @selected($t->id === $recruitment->assignee_id)>
                            {{ $t->name }}
                        </option>
                    @endforeach
                </select>
            </x-sidebar-section>

            <div class="grid grid-cols-2 gap-4">
                <x-sidebar-section icon="ri-mail-line" label="Approver">
                    <input type="text" 
                           class="w-full text-xs p-2 rounded border border-sigma-b bg-sigma-bg focus:outline-none focus:border-blue-500"
                           value="{{ $recruitment->approver }}"
                           name="value"
                           hx-post="{{ route('recruitment.update', $id) }}"
                           hx-trigger="change"
                           hx-vals='{"field": "approver"}'>
                </x-sidebar-section>

                <x-sidebar-section icon="ri-check-line" label="Estado Rápido">
                    <select data-widget="slimselect"
                            hx-post="{{ route('recruitment.update', $id) }}"
                            hx-trigger="change"
                            name="field">
                        <option value="">— Actions —</option>
                        <option value="approved_at">Aprobar</option>
                        <option value="closed_at">Cerrar</option>
                    </select>
                </x-sidebar-section>
            </div>
            
        </div>
    </div>

</div>
