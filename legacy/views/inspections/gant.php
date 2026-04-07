<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cronograma B&N Compacto</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #ffffff;
            color: #000;
            padding: 20px;
        }

        .container { max-width: 100%; margin: 0 auto; }

        h1 { 
            font-size: 20px; 
            border-bottom: 2px solid #000; 
            padding-bottom: 10px; 
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        /* Controles */
        .controls {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .control-group { display: flex; flex-direction: column; gap: 4px; }
        
        label { font-size: 11px; font-weight: bold; text-transform: uppercase; }

        select {
            padding: 5px;
            border: 1px solid #000;
            font-size: 13px;
            background: #fff;
        }

        /* Gantt Table */
        .gantt-container {
            overflow-x: auto;
            border: 1px solid #000;
        }

        .gantt-header {
            display: flex;
            background: #000;
            color: #fff;
            font-size: 11px;
        }

        .gantt-label-col {
            min-width: 220px;
            padding: 10px;
            border-right: 1px solid #fff;
        }

        .gantt-timeline-header { display: flex; flex: 1; }

        .gantt-month {
            flex: 1;
            padding: 10px 5px;
            text-align: center;
            border-right: 1px solid #444;
            min-width: 100px;
        }

        .gantt-row {
            display: flex;
            border-bottom: 1px solid #ddd;
            min-height: 45px;
        }

        .item-info {
            min-width: 220px;
            padding: 8px;
            border-right: 1px solid #000;
            background: #f9f9f9;
        }

        .item-name { font-weight: bold; font-size: 13px; display: block; }
        .item-freq { font-size: 10px; color: #666; text-transform: uppercase; }

        .gantt-timeline {
            display: flex;
            flex: 1;
            position: relative;
        }

        .gantt-month-col {
            flex: 1;
            border-right: 1px solid #eee;
            position: relative;
        }

        /* Marcadores */
        .marker {
            position: absolute;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 12px;
            height: 12px;
            z-index: 5;
            cursor: help;
        }

        .marker.created {
            background: #000; /* Cuadrado negro */
        }

        .marker.scheduled {
            background: #fff;
            border: 2px solid #000;
            border-radius: 50%; /* Círculo blanco/negro */
        }

        .marker.today-marker {
            background: transparent;
            border: 2px solid #000;
            width: 16px;
            height: 16px;
            border-radius: 50%;
        }

        .today-line {
            position: absolute;
            width: 0;
            border-left: 1px dashed #000;
            top: 0;
            bottom: 0;
            z-index: 1;
            pointer-events: none;
        }

        /* Leyenda */
        .legend {
            margin-top: 20px;
            display: flex;
            gap: 20px;
            font-size: 12px;
            padding: 10px;
            border: 1px solid #eee;
            width: fit-content;
        }

        .legend-item { display: flex; align-items: center; gap: 8px; }
        .shape { width: 12px; height: 12px; border: 1px solid #000; }
        .shape.black { background: #000; }
        .shape.circle { border-radius: 50%; }
        .shape.dashed { border: none; border-left: 1px dashed #000; height: 15px; width: 1px; }

        .tooltip {
            position: absolute;
            background: #000;
            color: #fff;
            padding: 5px 10px;
            font-size: 11px;
            border-radius: 3px;
            pointer-events: none;
            display: none;
            z-index: 100;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Cronograma de Inspecciones</h1>

        <div class="controls">
            <div class="control-group">
                <label>Frecuencia</label>
                <select id="filterFrequency">
                    <option value="all">Todas</option>
                    <option value="Weekly">Semanal</option>
                    <option value="Monthly">Mensual</option>
                    <option value="Quarterly">Trimestral</option>
                </select>
            </div>
            <div class="control-group">
                <label>Vista</label>
                <select id="monthRange">
                    <option value="3">3 meses</option>
                    <option value="6" selected>6 meses</option>
                    <option value="12">12 meses</option>
                </select>
            </div>
        </div>

        <div class="gantt-container" id="ganttContainer"></div>

        <div class="legend">
            <div class="legend-item"><div class="shape black"></div> Creada</div>
            <div class="legend-item"><div class="shape circle"></div> Programada</div>
            <div class="legend-item"><div class="shape dashed"></div> Hoy</div>
        </div>
    </div>

    <div id="tooltip" class="tooltip"></div>

    <script>
        // TUS DATOS ORIGINALES RECONSTRUIDOS
        const automations = [
            { id: 1, type: 'Preventivo Motor', frequency: 'Monthly', anchor_date: '2026-03-10', created_inspections: ['2026-01-10', '2026-02-10'] },
            { id: 2, type: 'Inspección Eléctrica', frequency: 'Quarterly', anchor_date: '2026-04-15', created_inspections: ['2026-01-15'] },
            { id: 3, type: 'Calibración Equipos', frequency: 'Semiannual', anchor_date: '2026-08-01', created_inspections: ['2026-02-01'] },
            { id: 4, type: 'Revisión Seguridad', frequency: 'Weekly', anchor_date: '2026-02-17', created_inspections: ['2026-01-06', '2026-01-13', '2026-01-20', '2026-01-27', '2026-02-03', '2026-02-10'] },
            { id: 5, type: 'Mantenimiento HVAC', frequency: 'Annual', anchor_date: '2027-01-10', created_inspections: ['2026-01-10'] }
        ];

        function renderGantt() {
            const container = document.getElementById('ganttContainer');
            const range = parseInt(document.getElementById('monthRange').value);
            const freqFilter = document.getElementById('filterFrequency').value;
            
            const today = new Date();
            const months = [];
            let tempDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);

            for (let i = 0; i < range; i++) {
                months.push({
                    label: tempDate.toLocaleDateString('es', { month: 'short', year: 'numeric' }),
                    m: tempDate.getMonth(),
                    y: tempDate.getFullYear(),
                    days: new Date(tempDate.getFullYear(), tempDate.getMonth() + 1, 0).getDate()
                });
                tempDate.setMonth(tempDate.getMonth() + 1);
            }

            let html = `<div class="gantt-header">
                <div class="gantt-label-col">Inspección / Frecuencia</div>
                <div class="gantt-timeline-header">`;
            months.forEach(m => html += `<div class="gantt-month">${m.label}</div>`);
            html += `</div></div>`;

            const filtered = automations.filter(a => freqFilter === 'all' || a.frequency === freqFilter);

            filtered.forEach(a => {
                html += `<div class="gantt-row">
                    <div class="item-info">
                        <span class="item-name">${a.type}</span>
                        <span class="item-freq">${a.frequency}</span>
                    </div>
                    <div class="gantt-timeline">`;
                
                months.forEach((m, idx) => {
                    // Línea de hoy
                    if (today.getMonth() === m.m && today.getFullYear() === m.y) {
                        const pos = (today.getDate() / m.days) * 100;
                        html += `<div class="today-line" style="left: calc(${(idx / months.length) * 100}% + ${(pos / months.length)}%)"></div>`;
                    }

                    html += `<div class="gantt-month-col">`;
                    
                    // Renderizar Creadas
                    a.created_inspections.forEach(d => {
                        const dt = new Date(d + "T00:00:00");
                        if (dt.getMonth() === m.m && dt.getFullYear() === m.y) {
                            html += `<div class="marker created" style="left: ${(dt.getDate()/m.days)*100}%" 
                                     onmouseover="tip(event, 'Creada: ${d}')" onmouseout="untip()"></div>`;
                        }
                    });

                    // Renderizar Programada
                    const ad = new Date(a.anchor_date + "T00:00:00");
                    if (ad.getMonth() === m.m && ad.getFullYear() === m.y) {
                        html += `<div class="marker scheduled" style="left: ${(ad.getDate()/m.days)*100}%" 
                                 onmouseover="tip(event, 'Programada: ${a.anchor_date}')" onmouseout="untip()"></div>`;
                    }

                    html += `</div>`;
                });
                html += `</div></div>`;
            });

            container.innerHTML = html;
        }

        // Tooltip simple
        const tooltip = document.getElementById('tooltip');
        function tip(e, text) {
            tooltip.innerText = text;
            tooltip.style.display = 'block';
            tooltip.style.left = e.pageX + 10 + 'px';
            tooltip.style.top = e.pageY - 25 + 'px';
        }
        function untip() { tooltip.style.display = 'none'; }

        document.getElementById('filterFrequency').addEventListener('change', renderGantt);
        document.getElementById('monthRange').addEventListener('change', renderGantt);
        
        window.onload = renderGantt;
    </script>
</body>
</html>