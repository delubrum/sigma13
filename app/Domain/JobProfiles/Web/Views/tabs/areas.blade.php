<div class="overflow-auto max-h-[65vh]">
    <h3 class="text-xs font-semibold flex items-center gap-1.5 mb-3" style="color:var(--tx2)">
        <i class="ri-layout-line text-base"></i>
        <span class="uppercase tracking-wider">Responsabilidades SGI</span>
    </h3>

    <table class="w-full text-xs" style="border-collapse:collapse">
        <thead>
            <tr>
                <th class="text-left px-3 py-2 font-semibold uppercase tracking-wider text-[10px]" style="background:var(--bg2); color:var(--tx2); border-bottom:1px solid var(--b); width:160px">Área</th>
                <th class="text-left px-3 py-2 font-semibold uppercase tracking-wider text-[10px]" style="background:var(--bg2); color:var(--tx2); border-bottom:1px solid var(--b); width:200px">Objetivo</th>
                <th class="text-left px-3 py-2 font-semibold uppercase tracking-wider text-[10px]" style="background:var(--bg2); color:var(--tx2); border-bottom:1px solid var(--b)">Descripción</th>
            </tr>
        </thead>
        <tbody>
            @php
            $sgi = [
                ['area' => 'Gestión De La Calidad', 'obj' => 'Llevar a cabo las actividades establecidas en el S.G.C. de la empresa.', 'desc' => [
                    'Mejorar continuamente su desempeño; ser abierto a observaciones de compañeros para ser eficiente y optimizar procedimientos y métodos.',
                    'Cumplir con los procedimientos de estandarización del Sistema de Gestión de Calidad establecidos.',
                    'Cumplir cualquier actividad inherente a su cargo y jerarquía asignada por el jefe inmediato para asegurar el normal desarrollo de las actividades.',
                    'Toda iniciativa de nuevas acciones o procedimientos debe notificarse al equipo de mejora continua y aprobarse por el líder inmediato antes de su puesta en marcha.',
                    'Los líderes de áreas deben dejar por escrito cualquier cambio en procesos o procedimientos para garantizar control y evitar errores.',
                ]],
                ['area' => 'Gestión Ambiental', 'obj' => 'Contribuir con el cumplimiento de la Gestión ambiental de la empresa.', 'desc' => [
                    'Prevenir o minimizar los impactos ambientales derivados de sus actividades.',
                    'Contribuir con el desarrollo de los Programas o Iniciativas de sensibilización ambiental organizadas por la empresa.',
                    'Reportar cualquier evento que afecte el medio ambiente a su líder inmediato.',
                    'Cumplir con los procedimientos, instructivos y normas ambientales establecidas.',
                    'Separar adecuadamente los residuos generados.',
                    'Hacer un uso eficiente de los recursos (agua, energía, etc.).',
                    'Cumplir y promover la política integral de la organización en su entorno laboral.',
                ]],
                ['area' => 'Gestión De La Seguridad Y Salud En El Trabajo', 'obj' => 'Cumplimiento de las directrices de SST establecidas por la organización.', 'desc' => [
                    'Conocer y tener clara la política de Seguridad y Salud en el Trabajo.',
                    'Procurar el cuidado integral de su salud y suministrar información clara, completa y veraz sobre su estado de salud.',
                    'Cumplir las normas de seguridad e higiene propias de la empresa.',
                    'Participar en la prevención de riesgos laborales mediante las actividades que se realicen en la empresa.',
                    'Informar oportunamente las condiciones de riesgo detectadas al jefe inmediato.',
                    'Reportar inmediatamente todo accidente de trabajo o incidente.',
                    'Participar y contribuir al cumplimiento de los objetivos del SG-SST.',
                ]],
                ['area' => 'Gestión De Seguridad De La Información', 'obj' => 'Conocer y cumplir las directrices asociadas a la seguridad de la información establecidas por la empresa.', 'desc' => [
                    'Mantener la confidencialidad de la información a la que tenga acceso en el desempeño de sus funciones.',
                    'Cumplir con la política de seguridad de la información y los procedimientos asociados.',
                    'Reportar cualquier incidente o vulnerabilidad de seguridad de la información al área responsable.',
                ]],
            ];
            @endphp
            @foreach($sgi as $row)
                <tr>
                    <td class="px-3 py-2 font-semibold align-top" style="border-bottom:1px solid var(--b); color:var(--tx)">{{ $row['area'] }}</td>
                    <td class="px-3 py-2 align-top" style="border-bottom:1px solid var(--b); color:var(--tx2)">{{ $row['obj'] }}</td>
                    <td class="px-3 py-2 align-top" style="border-bottom:1px solid var(--b); color:var(--tx2)">
                        <ul class="list-disc pl-4 space-y-1">
                            @foreach($row['desc'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
