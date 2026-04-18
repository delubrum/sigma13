<div class="p-4 space-y-4">

    {{-- Status badge --}}
    <div class="flex justify-center">
        @php
            $statusStyle = match($data->color) {
                'green'  => 'color:var(--success); border-color:var(--success-muted); background:var(--success-bg)',
                'yellow' => 'color:var(--warning); border-color:var(--warning-muted); background:var(--warning-bg)',
                'red'    => 'color:var(--danger);  border-color:var(--danger-muted);  background:var(--danger-bg)',
                'purple' => 'color:var(--info);    border-color:var(--info-muted);    background:var(--info-bg)',
                default  => 'color:var(--tx2);     border-color:var(--b);             background:var(--bg2)',
            };
        @endphp
        <span class="px-4 py-1 rounded-full text-xs font-bold border-2 shadow-sm uppercase"
              style="{{ $statusStyle }}">
            {{ $data->subtitle }}
        </span>
    </div>

    {{-- Basic info --}}
    <x-sidebar-section icon="ri-information-line" label="Basic Information">
        @foreach ($data->properties as $prop)
            <x-sidebar-row :label="$prop->label" :value="$prop->value" :url="$prop->url" :icon="$prop->linkIcon" />
        @endforeach
    </x-sidebar-section>

    {{-- Description --}}
    @if($data->description)
        <x-sidebar-section icon="ri-file-text-line" label="Description">
            <p class="text-sm leading-relaxed break-words" style="color:var(--tx)">
                {{ $data->description }}
            </p>
        </x-sidebar-section>
    @endif


    {{-- Asset --}}
    <x-sidebar-section icon="ri-tools-line" label="Asset">
        @if($data->canEdit && count($data->assets) > 0)
            <select name="asset_id" data-widget="slimselect"
                    hx-post="{{ route('global.patch', ['helpdesk', $data->id]) }}"
                    hx-trigger="change"
                    hx-vals='{"field": "asset_id"}'>
                <option value="">— Sin activo —</option>
                @foreach($data->assets as $a)
                    <option value="{{ $a['id'] }}" @selected($a['id'] == $data->assetId)>
                        {{ $a['label'] }}
                    </option>
                @endforeach
            </select>
        @else
            <x-sidebar-row label="Activo" :value="$data->assetLabel ?: '—'" />
        @endif
    </x-sidebar-section>

    {{-- Priority + Assignment --}}
    <div class="grid grid-cols-2 gap-3">
        <x-sidebar-section icon="ri-flag-line" label="Priority">
            @if($data->canEdit)
                <select name="priority" data-widget="slimselect"
                        hx-post="{{ route('global.patch', ['helpdesk', $data->id]) }}"
                        hx-trigger="change"
                        hx-vals='{"field": "priority"}'>
                    <option value="">—</option>
                    <option value="High"   @selected($data->priority === 'High')>High</option>
                    <option value="Medium" @selected($data->priority === 'Medium')>Medium</option>
                    <option value="Low"    @selected($data->priority === 'Low')>Low</option>
                </select>
            @else
                <x-sidebar-row label="" :value="$data->priority ?: '—'" />
            @endif
        </x-sidebar-section>

        <x-sidebar-section icon="ri-user-star-line" label="Assignment">
            @if($data->canEdit && count($data->technicians) > 0)
                <select name="assignee_id" data-widget="slimselect"
                        hx-post="{{ route('global.patch', ['helpdesk', $data->id]) }}"
                        hx-trigger="change"
                        hx-vals='{"field": "assignee_id"}'>
                    <option value="">—</option>
                    @foreach($data->technicians as $u)
                        <option value="{{ $u['id'] }}" @selected($u['id'] == $data->assigneeId)>
                            {{ $u['name'] }}
                        </option>
                    @endforeach
                </select>
            @else
                <x-sidebar-row label="" :value="$data->assigneeName ?: '—'" />
            @endif
        </x-sidebar-section>
    </div>

    {{-- SGC + Cause --}}
    <div class="grid grid-cols-2 gap-3">
        <x-sidebar-section icon="ri-settings-3-line" label="SGC">
            <select name="sgc_code" data-widget="slimselect"
                    hx-post="{{ route('global.patch', ['helpdesk', $data->id]) }}"
                    hx-trigger="change"
                    hx-vals='{"field": "sgc_code"}'>
                <option value="">—</option>
                @foreach(['Corrective','Preventive','Production','Infrastructure'] as $opt)
                    <option value="{{ $opt }}" @selected($data->sgcCode === $opt)>{{ $opt }}</option>
                @endforeach
            </select>
        </x-sidebar-section>

        <x-sidebar-section icon="ri-error-warning-line" label="Cause">
            <select name="root_cause" data-widget="slimselect"
                    hx-post="{{ route('global.patch', ['helpdesk', $data->id]) }}"
                    hx-trigger="change"
                    hx-vals='{"field": "root_cause"}'>
                <option value="">—</option>
                @foreach(['N/A','Habituales','Falla Eléctrica','Falta Capacitación','Desgaste','Mal Uso'] as $opt)
                    <option value="{{ $opt }}" @selected($data->rootCause === $opt)>{{ $opt }}</option>
                @endforeach
            </select>
        </x-sidebar-section>
    </div>

    {{-- Resolution notes --}}
    <x-sidebar-section icon="ri-sticky-note-line" label="Resolution Notes">
        <textarea name="resolution_notes" rows="3"
                  class="w-full px-3 py-2 rounded-lg text-sm font-semibold outline-none transition-all border resize-none"
                  style="background:var(--bg2); border-color:var(--b); color:var(--tx)"
                  hx-post="{{ route('global.patch', ['helpdesk', $data->id]) }}"
                  hx-trigger="change"
                  hx-vals='{"field": "resolution_notes"}'>{{ $data->resolutionNotes }}</textarea>
    </x-sidebar-section>

</div>
