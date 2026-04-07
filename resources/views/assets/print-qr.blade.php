<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: 100mm 25mm; margin: 0; }
        * { box-sizing: border-box; -webkit-print-color-adjust: exact !important; }
        html, body {
            margin: 0 !important; padding: 0 !important;
            width: 100mm; height: 25mm;
            overflow: hidden; font-family: Arial, sans-serif; background: white;
        }
        body { display: flex; flex-direction: row; }
        .etiqueta-individual { width: 50mm; height: 25mm; padding: 1.5mm 2mm; display: flex; align-items: center; gap: 1mm; }
        .left-side { flex: 1; display: flex; flex-direction: column; justify-content: center; height: 100%; min-width: 0; }
        .logo { height: 3.5mm; object-fit: contain; align-self: flex-start; margin-bottom: 0.5mm; }
        .title-activos { font-size: 5pt; color: #000; margin-bottom: 1mm; letter-spacing: 0.5px; }
        .info { display: flex; flex-direction: column; gap: 0.5mm; }
        .info-row { font-size: 7pt; font-weight: bold; line-height: 1; color: #000; white-space: nowrap; }
        .assignee-row { margin-top: 1.5mm; font-size: 5pt; border-top: 0.2mm solid #000; padding-top: 1mm; font-weight: bold; text-transform: uppercase; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .qr-container { width: 15mm; height: 15mm; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .qr-container img { width: 100%; height: 100%; }
    </style>
</head>
<body>
    @for($i = 0; $i < 2; $i++)
    <div class="etiqueta-individual">
        <div class="left-side">
            <img src="{{ asset('images/logobw.png') }}" class="logo">
            <div class="title-activos">INVENTARIO ACTIVOS</div>
            <div class="info">
                <div class="info-row">SAP: {{ $data->sap }}</div>
                <div class="info-row">SER: {{ $data->serial }}</div>
                <div class="assignee-row">{{ $data->assignee ?? 'N/A' }}</div>
            </div>
        </div>
        <div class="qr-container" id="qr-{{ $i }}"></div>
    </div>
    @endfor

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        const url = '{{ $data->qrUrl }}';
        [0, 1].forEach(i => {
            new QRCode(document.getElementById('qr-' + i), {
                text: url,
                width: 57,
                height: 57,
                correctLevel: QRCode.CorrectLevel.L
            });
        });
        window.onload = () => setTimeout(() => { window.print(); window.close(); }, 800);
    </script>
</body>
</html>