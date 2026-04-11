@if($data->photoUrl)
    <img src="{{ $data->photoUrl }}"
         class="w-full h-full object-cover animate-fade-in shadow-inner">
@else
    <div class="flex flex-col items-center justify-center h-full gap-2 bg-sigma-bg3/50">
        <canvas id="asset-qr-{{ $data->id }}" class="w-36 h-36 opacity-60"></canvas>
        <span class="text-[9px] font-black uppercase tracking-widest italic"
              style="color:var(--tx2)">Tocar para subir foto</span>
    </div>
    
    <script>
        QRCode.toCanvas(
            document.getElementById('asset-qr-{{ $data->id }}'),
            '{{ $data->qrUrl }}',
            { width: 144, errorCorrectionLevel: 'L' }
        );
    </script>
@endif
