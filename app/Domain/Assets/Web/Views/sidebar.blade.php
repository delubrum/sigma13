<div class="relative h-52 overflow-hidden" style="background:var(--bg3)">

    <div id="asset_photo_preview" 
         class="w-full h-full"
         hx-get="{{ route('assets.sidebar.photo', $data->id) }}"
         hx-trigger="refresh-sidebar-photo from:body, refresh">
        @include('assets::sidebar-photo', ['data' => $data])
    </div>

    <input type="file"
           class="absolute inset-0 opacity-0 cursor-pointer z-10"
           hx-post="{{ route('shared.upload', ['route' => 'assets', 'id' => $data->id, 'collection' => 'profile']) }}"
           hx-encoding="multipart/form-data"
           hx-trigger="change"
           hx-indicator="#global-loader"
           hx-on::after-request="if(event.detail.successful) htmx.trigger('#asset_photo_preview', 'refresh')"
           accept="image/*"
           name="file">

    <div id="asset_upload_loader"
         class="htmx-indicator absolute inset-0 flex flex-col items-center justify-center z-20"
         style="background:color-mix(in srgb, var(--bg) 80%, transparent)">
        <i class="ri-loader-4-line animate-spin text-3xl" style="color:var(--ac)"></i>
        <span class="text-[10px] font-bold uppercase mt-2" style="color:var(--tx)">Procesando...</span>
    </div>

    <button onclick="window.open('{{ route('assets.print-qr', $data->id) }}', '_blank')"
            class="absolute bottom-3 right-3 z-30 p-2 rounded-xl shadow-lg transition-all hover:scale-110 active:scale-95"
            style="background:var(--ac); color:var(--ac-inv)">
        <i class="ri-qr-code-line text-xl"></i>
    </button>
</div>

<div class="p-4 space-y-4">

    <div class="flex justify-center">
        @php
            $statusLabel = match($data->status) {
                'available' => 'Disponible',
                'assigned'  => 'Asignado',
                'maintenance' => 'Mantenimiento',
                'retired'   => 'Retirado',
                default     => Str::ucfirst($data->status),
            };
            $statusStyle = match($data->status) {
                'available' => 'color:var(--success); border-color:var(--success-muted); background:var(--success-bg)',
                'assigned'  => 'color:var(--info);    border-color:var(--info-muted);    background:var(--info-bg)',
                'maintenance' => 'color:var(--warning); border-color:var(--warning-muted); background:var(--warning-bg)',
                default     => 'color:var(--danger);  border-color:var(--danger-muted);  background:var(--danger-bg)',
            };
        @endphp
        <span class="px-4 py-1 rounded-full text-xs font-bold border-2 shadow-sm"
              style="{{ $statusStyle }}">
            {{ $statusLabel }}
        </span>
    </div>

    <x-sidebar-section icon="ri-information-line" label="Información Básica">
        <x-sidebar-row label="Serial"    :value="$data->serial" />
        <x-sidebar-row label="SAP"       :value="$data->sap" />
        <x-sidebar-row label="Hostname"  :value="$data->hostname" />
        <x-sidebar-row label="Modo Trabajo" :value="$data->workMode" />
        <x-sidebar-row label="Ubicación"  :value="$data->location" />
        <x-sidebar-row label="Teléfono"     :value="$data->phone" />
    </x-sidebar-section>

    <x-sidebar-section icon="ri-shield-user-line" label="Asignación Actual">
        <x-sidebar-row label="Asignado a" :value="$data->assignee" id="sidebarAssignedTo" />
        <x-sidebar-row label="Fecha"        :value="$data->assignedAt" />
    </x-sidebar-section>

</div>

<script>
QRCode.toCanvas(
    document.getElementById('asset-qr-{{ $data->id }}'),
    '{{ $data->qrUrl }}',
    { width: 144, errorCorrectionLevel: 'L' }
);
</script>