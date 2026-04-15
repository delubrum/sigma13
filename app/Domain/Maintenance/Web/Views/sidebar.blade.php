<div class="p-4 space-y-4">

    <div class="flex justify-center">
        @php
            $statusLabel = $data->subtitle;
            $color = $data->color;
            $statusStyle = match($color) {
                'green'  => 'color:var(--success); border-color:var(--success-muted); background:var(--success-bg)',
                'yellow' => 'color:var(--warning); border-color:var(--warning-muted); background:var(--warning-bg)',
                'red'    => 'color:var(--danger);  border-color:var(--danger-muted);  background:var(--danger-bg)',
                'purple' => 'color:var(--info);    border-color:var(--info-muted);    background:var(--info-bg)',
                default  => 'color:var(--tx2);     border-color:var(--b);             background:var(--bg2)',
            };
        @endphp
        <span class="px-4 py-1 rounded-full text-xs font-bold border-2 shadow-sm uppercase"
              style="{{ $statusStyle }}">
            {{ $statusLabel }}
        </span>
    </div>

    <x-sidebar-section icon="ri-information-line" label="Detalles del Ticket">
        @foreach ($data->properties as $prop)
            <x-sidebar-row :label="$prop->label" :value="$prop->value" />
        @endforeach
    </x-sidebar-section>

    @php
        $status = strtolower($data->model->status ?? 'open');
    @endphp

    <x-sidebar-section icon="ri-shield-flash-line" label="Acciones">
        <div class="flex flex-col gap-2">
            @if(in_array($status, ['open', 'rejected']))
                <button class="w-full text-left px-3 py-2 rounded border bg-blue-500 text-white text-[10px] font-black uppercase"
                        hx-post="{{ route('maintenance.process', ['id' => $data->id, 'action' => 'attend']) }}"
                        hx-confirm="¿Atender este ticket?">
                    <i class="ri-play-line"></i> Atender
                </button>
            @endif

            @if($status === 'started')
                <button class="w-full text-left px-3 py-2 rounded border bg-purple-500 text-white text-[10px] font-black uppercase"
                        hx-post="{{ route('maintenance.process', ['id' => $data->id, 'action' => 'finish']) }}"
                        hx-confirm="¿Finalizar atención?">
                    <i class="ri-check-line"></i> Terminar Atención
                </button>
            @endif

            @if(in_array($status, ['attended', 'started']))
                <button class="w-full text-left px-3 py-2 rounded border bg-green-500 text-white text-[10px] font-black uppercase"
                        hx-post="{{ route('maintenance.process', ['id' => $data->id, 'action' => 'close']) }}"
                        hx-confirm="¿Cerrar este ticket?">
                    <i class="ri-checkbox-circle-line"></i> Cerrar Ticket
                </button>
            @endif

            @if($status !== 'closed' && $status !== 'rejected')
                <button class="w-full text-left px-3 py-2 rounded border bg-red-500 text-white text-[10px] font-black uppercase"
                        hx-post="{{ route('maintenance.process', ['id' => $data->id, 'action' => 'reject']) }}"
                        hx-confirm="¿Rechazar este ticket?">
                    <i class="ri-close-circle-line"></i> Rechazar
                </button>
            @endif
        </div>
    </x-sidebar-section>

    @if($data->model && $data->model->description)
    <x-sidebar-section icon="ri-file-text-line" label="Descripción">
        <div class="text-xs p-2 rounded border" style="background:var(--bg); border-color:var(--b); color:var(--tx2)">
            {{ $data->model->description }}
        </div>
    </x-sidebar-section>
    @endif

</div>
