@php
/** @var array<string, mixed> $packingData */
$jsonPacking = json_encode($packingData);
$cbm = $model;
@endphp

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

<div id="cbm-root" 
     x-data="{ 
        selectedCrateIdx: null,
        data: {{ $jsonPacking }},
        toggleCrate(idx) {
            if (this.selectedCrateIdx === idx) {
                this.selectedCrateIdx = null;
                window.cbmEngine.renderGlobal();
            } else {
                this.selectedCrateIdx = idx;
                window.cbmEngine.renderCrateDetail(idx);
            }
        },
        resetToMain() {
            this.selectedCrateIdx = null;
            window.cbmEngine.renderGlobal();
        },
        exportPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('p', 'mm', 'a4');
            
            doc.setFontSize(14);
            doc.setFont('helvetica', 'bold');
            doc.text('LOAD PLAN: {{ $cbm->project }}', 12, 12);
            
            doc.setFontSize(8);
            doc.setFont('helvetica', 'normal');
            doc.text('Date: ' + new Date().toLocaleDateString() + ' | ES Metals Logistics', 12, 17);

            doc.autoTable({
                startY: 20,
                head: [['Container', 'Total Weight', 'Excel Qty', 'Packed Qty', 'Efficiency']],
                body: [['40ft Std', '{{ number_format($packingData['totalWeight'] ?? 0, 1) }} lbs', '{{ (int) $cbm->total_items }}', '{{ (int) $packingData['totalItemsPacked'] ?? 0 }}', '{{ round($packingData['utility'] ?? 0, 1) }}%']],
                styles: { fontSize: 8, cellPadding: 2 },
                headStyles: { fillColor: [0, 0, 0] }
            });

            const rows = [];
            this.data.items.forEach((crate, i) => {
                const groups = {};
                crate.parts.forEach(p => {
                    const k = `${p.l}x${p.b}x${p.h}`;
                    groups[k] = (groups[k] || 0) + 1;
                });
                const details = Object.entries(groups).map(([dim, q]) => `${q}x (${dim})`).join(' | ');

                rows.push([
                    i + 1,
                    crate.id,
                    crate.type.split(' ')[0], 
                    crate.weight.toFixed(1),
                    crate.totalItems,
                    { content: details, styles: { fontSize: 7 } }
                ]);
            });

            doc.autoTable({
                startY: doc.lastAutoTable.finalY + 5,
                head: [['#', 'ID', 'Size', 'Weight', 'Qty', 'Content Summary (Groups)']],
                body: rows,
                theme: 'grid',
                styles: { fontSize: 8, cellPadding: 1.5 },
                headStyles: { fillColor: [60, 60, 60] },
                columnStyles: { 5: { cellWidth: 90 } }
            });

            doc.save('LP_{{ $cbm->project }}.pdf');
        }
     }" 
     class="w-full h-[75vh] bg-white rounded-xl flex flex-col overflow-hidden border border-gray-200 shadow-sm">
    
    <div class="flex flex-col md:flex-row flex-grow overflow-hidden">
        {{-- 3D RENDERER --}}
        <div class="w-full md:w-3/5 relative bg-slate-50 border-r border-gray-100">
            <div id="three-container" class="w-full h-full"></div>
            <div class="absolute bottom-4 left-4 flex gap-2">
                <button @click="resetToMain()" class="bg-black text-white px-4 py-2 rounded-lg font-black text-[10px] uppercase shadow-xl flex items-center gap-2">
                    <i class="ri-home-4-fill"></i> Main
                </button>
                <button onclick="window.cbmEngine.resetView()" class="bg-white text-black border border-gray-200 px-4 py-2 rounded-lg font-black text-[10px] uppercase">
                    <i class="ri-refresh-line"></i> Cam
                </button>
            </div>
        </div>

        {{-- LIST OF CRATES --}}
        <div class="w-full md:w-2/5 flex flex-col bg-white overflow-hidden">
            <div @click="resetToMain()" class="p-4 bg-gray-50/50 border-b cursor-pointer hover:bg-gray-100 transition-all">
                <div class="grid grid-cols-4 gap-2">
                    <div class="border-r border-gray-200">
                        <span class="text-[10px] font-black text-gray-400 uppercase block">Total Lbs</span>
                        <span class="text-xs font-black italic">{{ number_format($packingData['totalWeight'] ?? 0, 0) }}</span>
                    </div>
                    <div class="border-r border-gray-200 pl-1">
                        <span class="text-[10px] font-black text-gray-400 uppercase block">Excel</span>
                        <span class="text-xs font-black italic">{{ (int) $cbm->total_items }}</span>
                    </div>
                    <div class="border-r border-gray-200 pl-1">
                        <span class="text-[10px] font-black text-gray-400 uppercase block">Packed</span>
                        <span class="text-xs font-black italic text-emerald-600">{{ (int) $packingData['totalItemsPacked'] ?? 0 }}</span>
                    </div>
                    <div class="text-right pl-1">
                        <span class="text-[10px] font-black text-gray-400 uppercase block">Util</span>
                        <span class="text-xs font-black italic">{{ round($packingData['utility'] ?? 0, 1) }}%</span>
                    </div>
                </div>
            </div>

            <div class="flex-grow overflow-y-auto p-3 space-y-2 custom-scrollbar">
                <template x-for="(crate, idx) in data.items" :key="idx">
                    <div class="border rounded-xl overflow-hidden bg-white transition-all shadow-sm"
                         :class="selectedCrateIdx === idx ? 'border-black ring-2 ring-black/5 shadow-md' : 'border-gray-100 shadow-sm'">
                        
                        <div @mouseover="if(selectedCrateIdx === null) window.cbmEngine.highlightCrate(idx)" 
                             @mouseleave="if(selectedCrateIdx === null) window.cbmEngine.highlightCrate(null)"
                             @click="toggleCrate(idx)"
                             class="p-2.5 flex justify-between items-center cursor-pointer transition-colors"
                             :class="selectedCrateIdx === idx ? 'bg-black text-white' : 'hover:bg-gray-50'">
                            
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded flex items-center justify-center font-black text-[10px]"
                                     :class="selectedCrateIdx === idx ? 'bg-white text-black' : 'bg-gray-100 text-gray-400'" x-text="idx + 1"></div>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="block font-black text-[10px] uppercase italic" x-text="crate.id"></span>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded-full font-black" 
                                              :class="selectedCrateIdx === idx ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-700'"
                                              x-text="crate.utility.toFixed(0) + '%'"></span>
                                    </div>
                                    <span class="block text-[10px] font-bold opacity-60 uppercase tracking-tighter" x-text="crate.type"></span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 text-right">
                                <div>
                                    <span class="block font-black text-[10px] italic" x-text="crate.weight.toFixed(0) + ' lbs'"></span>
                                    <span class="block text-[9px] font-bold opacity-50" x-text="crate.totalItems + ' items'"></span>
                                </div>
                                <i :class="selectedCrateIdx === idx ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'" class="text-lg opacity-40"></i>
                            </div>
                        </div>

                        <div x-show="selectedCrateIdx === idx" x-collapse class="bg-gray-50 border-t">
                            <div class="p-2 space-y-1">
                                <template x-for="(part, pIdx) in crate.parts" :key="pIdx">
                                    <div @mouseover="window.cbmEngine.highlightPart(pIdx)" 
                                         class="p-2 bg-white border border-gray-100 rounded-lg flex justify-between items-center text-[11px]">
                                        <span class="font-black italic opacity-70" x-text="part.l + '″×' + part.b + '″×' + part.h + '″'"></span>
                                        <span class="font-black text-red-600" x-text="part.w + ' LB'"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="p-4 bg-gray-50 border-t flex justify-end items-center shrink-0">
                <button @click="exportPDF()" class="bg-red-600 text-white px-5 py-2.5 rounded-xl font-black uppercase text-[10px] shadow-lg flex items-center gap-2 hover:bg-red-700 transition-all">
                    <i class="ri-file-pdf-2-fill"></i> Export PDF
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>

<script>
(function() {
    let scene, camera, renderer, controls, itemsMeshes = [], partMeshes = [], frameId;
    const container = document.getElementById('three-container');
    const d = {{ $jsonPacking }};

    function init() {
        if (!container || !d || !Object.keys(d).length) return;
        scene = new THREE.Scene();
        scene.background = new THREE.Color(0xf1f5f9);
        camera = new THREE.PerspectiveCamera(35, container.clientWidth / container.clientHeight, 1, 40000);
        renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
        renderer.setSize(container.clientWidth, container.clientHeight);
        renderer.setPixelRatio(window.devicePixelRatio);
        container.appendChild(renderer.domElement);
        controls = new THREE.OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        scene.add(new THREE.AmbientLight(0xffffff, 0.8));
        const light = new THREE.DirectionalLight(0xffffff, 0.5);
        light.position.set(200, 500, 300);
        scene.add(light);
        window.cbmEngine.renderGlobal();
        animate();
        new ResizeObserver(() => {
            if(!container.clientWidth) return;
            camera.aspect = container.clientWidth / container.clientHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(container.clientWidth, container.clientHeight);
        }).observe(container);
    }

    window.cbmEngine = {
        clear: function() {
            itemsMeshes = []; partMeshes = [];
            while(scene.children.length > 2) {
                const obj = scene.children[scene.children.length - 1];
                if(obj.geometry) obj.geometry.dispose();
                if(obj.material) obj.material.dispose();
                scene.remove(obj);
            }
        },
        drawBox: function(w, h, d, x, y, z, color, isWire = false) {
            const geo = new THREE.BoxGeometry(w, h, d);
            let m;
            if(isWire) {
                m = new THREE.LineSegments(new THREE.EdgesGeometry(geo), new THREE.LineBasicMaterial({ color: color }));
            } else {
                m = new THREE.Mesh(geo, new THREE.MeshPhongMaterial({ color: color, transparent: true, opacity: 0.85 }));
                m.add(new THREE.LineSegments(new THREE.EdgesGeometry(geo), new THREE.LineBasicMaterial({ color: 0xffffff, opacity: 0.1, transparent: true })));
            }
            m.position.set(x + w/2, y + h/2, z + d/2);
            scene.add(m);
            return m;
        },
        renderGlobal: function() {
            this.clear();
            if(!d.dims) return;
            this.drawBox(d.dims.l, d.dims.h, d.dims.b, 0, 0, 0, 0x94a3b8, true);
            d.items.forEach(it => {
                itemsMeshes.push(this.drawBox(it.l, it.h, it.b, it.px, it.pz, it.py, 0x334155));
            });
            this.resetView();
        },
        renderCrateDetail: function(idx) {
            this.clear();
            const crate = d.items[idx];
            this.drawBox(crate.l, crate.h, crate.b, 0, 0, 0, 0x000000, true);
            crate.parts.forEach(p => {
                partMeshes.push(this.drawBox(p.l, p.h, p.b, p.px, p.pz, p.py, 0xef4444));
            });
            this.zoomTo(crate.l, crate.h, crate.b);
        },
        highlightCrate: function(idx) {
            itemsMeshes.forEach((m, i) => {
                m.material.color.set(i === idx ? 0xef4444 : 0x334155);
                m.material.opacity = (idx === null || i === idx) ? 0.85 : 0.1;
            });
        },
        highlightPart: function(idx) {
            partMeshes.forEach((m, i) => {
                m.material.color.set(i === idx ? 0x000000 : 0xef4444);
                m.material.opacity = (idx === null || i === idx) ? 1.0 : 0.2;
            });
        },
        resetView: function() {
            if(!d.dims) return;
            camera.position.set(d.dims.l * 1.3, d.dims.h * 1.5, d.dims.b * 1.3);
            controls.target.set(d.dims.l/2, 0, d.dims.b/2);
            controls.update();
        },
        zoomTo: function(w, h, l) {
            camera.position.set(w * 1.8, h * 1.8, l * 1.8);
            controls.target.set(w/2, h/2, l/2);
            controls.update();
        }
    };

    function animate() {
        frameId = requestAnimationFrame(animate);
        if(controls) controls.update();
        if(renderer && scene && camera) renderer.render(scene, camera);
    }
    setTimeout(init, 150);
})();
</script>

<style>
[x-cloak] { display: none !important; }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
#three-container canvas { outline: none; display: block; }
</style>
