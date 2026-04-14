<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print — {{ $wo->code }}</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; }

        .noprint { display: block; }
        .toolbar { padding: 10px; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #ddd; }
        .btn { background: #111; color: #fff; border: none; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 6px; }

        .inspector-row { text-align: center; padding: 8px; }
        .inspector-row input { border: 1px solid #ccc; padding: 4px 10px; border-radius: 4px; font-size: 13px; }

        .boarding-pass {
            position: relative; width: 350px; height: auto;
            background: #fff; box-shadow: 0 5px 30px rgba(0,0,0,0.2);
            overflow: hidden; text-transform: uppercase;
            margin: 12px auto;
        }
        .boarding-pass small { display: block; font-size: 11px; color: #000; margin-bottom: 2px; font-weight: bold; }
        .boarding-pass strong { font-size: 16px; display: block; }
        .boarding-pass header { background: #fff; padding: 12px 20px; height: 53px; }
        .boarding-pass header .logo { float: left; width: 130px; height: 31px; }
        .boarding-pass header .flight { float: right; color: #000; text-align: right; }
        .boarding-pass header .flight small { font-size: 8px; }
        .boarding-pass header .flight strong { font-size: 18px; }
        .boarding-pass .infos { display: flex; border-top: 1px solid #000; }
        .boarding-pass .infos .places,
        .boarding-pass .infos .times { width: 50%; padding: 10px 0; }
        .boarding-pass .infos .places { border-right: 1px solid #000; }
        .boarding-pass .infos .box { padding: 10px 20px; width: 100%; }
        .boarding-pass .infos .box small { font-size: 12px; }
        .boarding-pass .strap { position: relative; border-top: 1px solid #000; }
        .boarding-pass .strap .box { padding: 23px 0 20px 20px; }
        .boarding-pass .strap .box div { margin-bottom: 15px; }
        .boarding-pass .strap .box div small { font-size: 12px; }
        .boarding-pass .strap .box div strong { font-size: 13px; }
        .boarding-pass .strap .qrcode { position: absolute; top: 20px; right: 35px; width: 80px; height: 80px; }

        @page { size: 234px 290px; margin: 0; }
        @media print {
            .noprint { display: none !important; }
            .boarding-pass { page-break-inside: avoid; margin-bottom: 100px; box-shadow: none; border: 1px solid transparent; -webkit-print-color-adjust: exact; }
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

<script>
document.addEventListener('input', e => {
    if (e.target.id === 'inspectorName') {
        document.querySelectorAll('.inspectorTicket').forEach(el => {
            el.textContent = e.target.value || '__________________________';
        });
    }
});
</script>

@foreach ($labels as $item)
<div class="boarding-pass">
    <header>
        <div class="logo" style="display:flex;align-items:center;gap:6px">
            <div style="font-size:9px;font-weight:bold;color:black;line-height:1.3">F03-PROP-02 V01<br>12/07/23</div>
        </div>
        <div class="flight">
            <small>Part Number</small>
            <strong>{{ $item->code }}</strong>
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
        </div>
    </section>

    <section class="strap">
        <div class="box">
            <div>
                <small>Inspector</small>
                <strong class="inspectorTicket">__________________________</strong>
            </div>
            <div>
                <small>Date</small>
                <strong>{{ now()->format('Y-m-d') }}</strong>
            </div>
        </div>
        @if ($qrUrl)
        <div class="qrcode">
            <img src="{{ $qrUrl }}" alt="QR" width="80" height="80">
        </div>
        @endif
    </section>
</div>
@endforeach

</body>
</html>
