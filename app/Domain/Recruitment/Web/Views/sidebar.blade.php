@php
$statusColors = [
    'approval' => ['bg' => '#fef9c3', 'text' => '#854d0e', 'border' => '#fde047'],
    'approved' => ['bg' => '#dcfce7', 'text' => '#166534', 'border' => '#4ade80'],
    'review'   => ['bg' => '#dbeafe', 'text' => '#1e40af', 'border' => '#60a5fa'],
    'closed'   => ['bg' => '#f3f4f6', 'text' => '#374151', 'border' => '#9ca3af'],
    'rejected' => ['bg' => '#fee2e2', 'text' => '#991b1b', 'border' => '#f87171'],
];
$sc = $statusColors[$data->status] ?? $statusColors['approval'];
@endphp

<div class="space-y-4 p-3 text-sm">

    {{-- Status badge --}}
    <div class="flex items-center gap-2">
        <span class="px-3 py-1 rounded-full font-bold text-xs uppercase"
              style="background:{{ $sc['bg'] }};color:{{ $sc['text'] }};border:1px solid {{ $sc['border'] }}">
            {{ $data->status }}
        </span>
        @if($data->rejection)
            <span class="text-red-500 text-xs italic">{{ $data->rejection }}</span>
        @endif
    </div>

    {{-- Key info grid --}}
    <div class="grid grid-cols-2 gap-2 text-xs">
        <div><span class="label-sm block">Creador</span><span class="font-medium">{{ $data->creator }}</span></div>
        <div><span class="label-sm block">Aprobador</span><span class="font-medium">{{ $data->approver }}</span></div>
        <div><span class="label-sm block">Perfil</span><span class="font-medium">{{ $data->profile ?? '—' }}</span></div>
        <div><span class="label-sm block">División / Área</span><span class="font-medium">{{ $data->division ?? '—' }} / {{ $data->area ?? '—' }}</span></div>
        <div><span class="label-sm block">Ciudad</span><span class="font-medium">{{ $data->city ?? '—' }}</span></div>
        <div><span class="label-sm block">Contrato</span><span class="font-medium">{{ $data->contract ?? '—' }}</span></div>
        <div><span class="label-sm block">Salario</span><span class="font-medium">{{ $data->srange ?? '—' }}</span></div>
        <div><span class="label-sm block">Inicio Esperado</span><span class="font-medium">{{ $data->start_date ?? '—' }}</span></div>
        <div><span class="label-sm block">Causa</span><span class="font-medium">{{ $data->cause ?? '—' }}</span></div>
        <div><span class="label-sm block">Fecha Creación</span><span class="font-medium">{{ $data->date }}</span></div>
    </div>

    {{-- Conversion bar --}}
    <div>
        <span class="label-sm block mb-1">Conversión — {{ $data->hired_count }}/{{ $data->qty }} contratados ({{ $data->conversion_pct }}%)</span>
        <div class="relative h-3 bg-gray-200 rounded overflow-hidden">
            <div class="absolute inset-y-0 left-0 bg-sigma-primary rounded"
                 style="width:{{ $data->conversion_pct }}%"></div>
        </div>
    </div>

    @if($data->approved_at)
    <div class="text-xs text-gray-500">Aprobado: {{ $data->approved_at }}</div>
    @endif
    @if($data->closed_at)
    <div class="text-xs text-gray-500">Cerrado: {{ $data->closed_at }}</div>
    @endif

    {{-- Add candidate button --}}
    @if(in_array($data->status, ['approved', 'review']))
    <div class="pt-2">
        <button class="btn-sm btn-primary w-full"
                hx-get="{{ route('recruitment.candidates.create', $data->id) }}"
                hx-target="#modal-body-2"
                hx-swap="innerHTML">
            <i class="ri-user-add-line mr-1"></i> Agregar Candidato
        </button>
    </div>
    @endif

</div>
