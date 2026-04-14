<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print ES — {{ $wo->code }}</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; }

        .noprint { display: block; }
        .toolbar { padding: 10px; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #ddd; }
        .btn { background: #111; color: #fff; border: none; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 6px; }
        .inspector-row { text-align: center; padding: 8px; }
        .inspector-row input { border: 1px solid #ccc; padding: 4px 10px; border-radius: 4px; font-size: 13px; }
        .warning-box { background: #fff3cd; border: 1px solid #856404; color: #856404; padding: 10px 15px; margin: 8px; border-radius: 6px; font-size: 12px; }

        .boarding-pass {
            position: relative; width: 350px; background: #fff;
            box-shadow: 0 5px 30px rgba(0,0,0,0.2); overflow: hidden;
            text-transform: uppercase; margin: 12px auto;
        }
        .boarding-pass small { display: block; font-size: 10px; color: #000; margin-bottom: 2px; font-weight: bold; }
        .boarding-pass strong { font-size: 13px; display: block; }
        .boarding-pass header { background: #fff; padding: 5px; height: 53px; }
        .boarding-pass header .logo { float: left; width: 130px; height: 31px; display: flex; align-items: center; }
        .boarding-pass header .flight { float: right; color: #000; text-align: right; }
        .boarding-pass header .flight small { font-size: 8px; }
        .boarding-pass header .flight strong { font-size: 16px; }
        .boarding-pass .infos { display: flex; border-top: 1px solid #000; }
        .boarding-pass .infos .places,
        .boarding-pass .infos .times { width: 50%; padding: 5px 0; }
        .boarding-pass .infos .places { border-right: 1px solid #000; }
        .boarding-pass .infos .box { padding: 5px; width: 100%; }
        .warning-badge { background: #ff9800; color: white; padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; display: inline-block; margin-top: 4px; }
        .no-data-box { background: #fff3cd; border: 2px dashed #856404; padding: 8px; text-align: center; color: #856404; font-weight: bold; }
        .section-split { display: flex; border-top: 1px solid #000; }
        .section-split .left, .section-split .right { width: 50%; padding: 5px; text-align: center; }
        .section-split .left { border-right: 1px solid #000; }

        @page { size: 234px 290px; margin: 0; }
        @media print {
            .noprint { display: none !important; }
            .boarding-pass { page-break-inside: avoid; margin-bottom: 100px; box-shadow: none; -webkit-print-color-adjust: exact; }
            .warning-badge { display: inline-block !important; }
        }
    </style>
</head>
<body>

<div class="noprint toolbar">
    <button class="btn" onclick="window.print()">
        <i class="ri-printer-line"></i> Imprimir
    </button>
</div>
<div class="noprint inspector-row">
    <label><b>Inspector:</b></label>
    <input id="inspectorName" placeholder="Nombre del inspector">
</div>

@if (!empty($noDataMarcas))
<div class="noprint warning-box">
    <b><i class="ri-alert-line"></i> Sin datos ES:</b>
    {{ implode(', ', $noDataMarcas) }}
</div>
@endif

<script>
document.addEventListener('input', e => {
    if (e.target.id === 'inspectorName') {
        document.querySelectorAll('.inspectorTicket').forEach(el => {
            el.textContent = e.target.value || '__________________';
        });
    }
});

// Client-side QR generation from ES consecutive numbers
window.addEventListener('load', () => {
    document.querySelectorAll('[data-qr-text]').forEach(el => {
        const text = el.dataset.qrText;
        if (!text || text === 'NO_DATA') return;
        QRCode.toCanvas(el, text, { width: 100, margin: 0 }, err => {
            if (err) el.parentElement.innerHTML = '<small style="color:red">QR error</small>';
        });
    });
});
</script>

@php
    $ticketsGenerated = 0;
@endphp

@foreach ($itemCodes as $idx => $itemCode)
@php
    $item   = $itemsMap[$itemCode] ?? null;
    $marca  = $marcas[$idx] ?? '';
    $rowConsecutivos = $consecutivos[$marca] ?? ['NO_DATA'];
    $rowOrdenes      = $ordenNames[$marca] ?? [''];
    if (!$item) continue;
@endphp

@foreach ($rowConsecutivos as $cIdx => $consecutivo)
@php
    $hasData    = $consecutivo !== 'NO_DATA';
    $ordenNombre = $rowOrdenes[$cIdx] ?? '';
    $ticketsGenerated++;
@endphp
<div class="boarding-pass">
    <header>
        <div class="logo">
            <div style="font-size:9px;font-weight:bold;color:black;line-height:1.3">F03-PROP-02 V01<br>12/07/23</div>
        </div>
        <div class="flight">
            <small>Part Number</small>
            <strong>{{ $item->code }}</strong>
            @if (!$hasData)
                <span class="warning-badge">SIN DATOS ES</span>
            @endif
        </div>
    </header>

    <section class="infos">
        <div class="places">
            <div class="box">
                <small>Project</small>
                <strong><em>{{ $wo->project }}</em></strong>
            </div>
            <div class="box">
                <small>Order</small>
                <strong>{{ $wo->code }}</strong>
            </div>
        </div>
        <div class="times">
            <div class="box">
                <small>Description</small>
                <strong><em>{{ $item->description }}</em></strong>
            </div>
            <div class="box">
                <small>Finish &amp; UC</small>
                <strong><em>{{ $item->fuc }}</em></strong>
            </div>
            @if ($ordenNombre)
            <div class="box">
                <small>{{ $ordenNombre }}</small>
            </div>
            @endif
        </div>
    </section>

    <section class="section-split">
        <div class="left">
            <small>Inspector</small>
            <strong class="inspectorTicket">__________________</strong>
            <small>Date: {{ now()->format('Y-m-d') }}</small>
            @if ($qrUrl)
                <img src="{{ $qrUrl }}" width="100" height="100" style="margin-top:4px">
            @endif
        </div>
        <div class="right" style="padding-top:40px">
            @if ($hasData)
                <small>ES: <b>{{ $consecutivo }}</b></small>
                <canvas data-qr-text="{{ $consecutivo }}" width="100" height="100"></canvas>
            @else
                <div class="no-data-box">
                    <small>MARCA: <b>{{ $marca }}</b></small><br>
                    <small style="font-size:9px">SIN CONSECUTIVO ES</small>
                </div>
            @endif
        </div>
    </section>
</div>
@endforeach
@endforeach

@if ($ticketsGenerated === 0)
<div style="padding:20px;color:red;text-align:center" class="noprint">
    <b>No se pudieron generar tickets.</b>
</div>
@else
<div style="padding:10px;color:green;text-align:center" class="noprint">
    <b><i class="ri-checkbox-circle-line"></i> {{ $ticketsGenerated }} etiqueta(s) generada(s)</b>
</div>
@endif

</body>
</html>
