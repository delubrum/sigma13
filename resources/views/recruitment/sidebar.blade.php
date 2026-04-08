{{-- Sidebar del modulo de recruitment --}}
@php
    /** @var \App\Data\Recruitment\Sidebar $data */
    $statusStyle = match(strtolower($data->status)) {
        'approval' => 'color:var(--warning); border-color:var(--warning-muted); background:var(--warning-bg)',
        'approved' => 'color:var(--success); border-color:var(--success-muted); background:var(--success-bg)',
        'closed'   => 'color:var(--tx2);     border-color:var(--b);             background:var(--bg2)',
        'rejected' => 'color:var(--danger);  border-color:var(--danger-muted);  background:var(--danger-bg)',
        default    => 'color:var(--tx2);     border-color:var(--b);             background:var(--bg2)',
    };
@endphp

<div class="p-4 space-y-4">

    {{-- Status badge --}}
    <div class="flex items-center justify-between gap-2">
        <span class="px-3 py-1 rounded-full text-xs font-bold border capitalize" style="{{ $statusStyle }}">
            {{ $data->status }}
        </span>
    </div>

    {{-- Personas --}}
    <x-sidebar-section icon="ri-user-line" label="Personas">
        <x-sidebar-row label="Requestor" :value="$data->user ?? '—'" id="sidebarUser" />
        <x-sidebar-row label="Assignee"  :value="$data->assignee ?? '—'" id="sidebarAssignee" />
        <x-sidebar-row label="Approver"  :value="$data->approver ?? '—'" />
    </x-sidebar-section>

    {{-- Details --}}
    <x-sidebar-section icon="ri-briefcase-line" label="Requirement">
        <x-sidebar-row label="City"     :value="$data->city ?? '—'" />
        <x-sidebar-row label="Quantity" :value="$data->qty ?? '—'" />
        <x-sidebar-row label="Reason"   :value="$data->reason ?? '—'" />
    </x-sidebar-section>

    {{-- Tiempos --}}
    <x-sidebar-section icon="ri-time-line" label="Tiempos">
        <x-sidebar-row label="Created"  :value="$data->createdAt ?? '—'" />
        <x-sidebar-row label="Approved" :value="$data->approvedAt ?? '—'" />
        <x-sidebar-row label="Closed"   :value="$data->closedAt ?? '—'" />
    </x-sidebar-section>

</div>
