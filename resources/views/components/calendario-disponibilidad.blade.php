@props([
    'name' => 'fecha_visita',
    'urlMes',
    'hoy',
])

<div
    x-data="{
        urlMes: @js($urlMes),
        hoy: @js($hoy),
        anio: parseInt(@js($hoy).slice(0, 4)),
        mes: parseInt(@js($hoy).slice(5, 7)),
        estados: {},
        cargando: false,
        fechaSeleccionada: '',
        peticionActual: 0,
        nombresDias: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
        nombresMeses: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        init() {
            this.cargarMes();
        },
        cargarMes() {
            this.cargando = true;
            const peticion = ++this.peticionActual;
            const mesStr = this.anio + '-' + String(this.mes).padStart(2, '0');
            fetch(this.urlMes + '?mes=' + mesStr)
                .then(r => r.json())
                .then(data => {
                    if (peticion !== this.peticionActual) return;
                    this.estados = data.dias || {};
                })
                .finally(() => {
                    if (peticion === this.peticionActual) this.cargando = false;
                });
        },
        esMesActual() {
            return this.anio === parseInt(this.hoy.slice(0, 4)) && this.mes === parseInt(this.hoy.slice(5, 7));
        },
        cambiarMes(delta) {
            let nuevoMes = this.mes + delta;
            let nuevoAnio = this.anio;
            if (nuevoMes < 1) { nuevoMes = 12; nuevoAnio--; }
            if (nuevoMes > 12) { nuevoMes = 1; nuevoAnio++; }
            const limite = parseInt(this.hoy.slice(0, 4)) * 12 + parseInt(this.hoy.slice(5, 7));
            if (nuevoAnio * 12 + nuevoMes < limite) return;
            this.anio = nuevoAnio;
            this.mes = nuevoMes;
            this.cargarMes();
        },
        diasGrilla() {
            const primerDia = new Date(this.anio, this.mes - 1, 1).getDay();
            const offset = primerDia === 0 ? 6 : primerDia - 1;
            const totalDias = new Date(this.anio, this.mes, 0).getDate();
            const dias = [];
            for (let i = 0; i < offset; i++) dias.push(null);
            for (let d = 1; d <= totalDias; d++) {
                const fecha = this.anio + '-' + String(this.mes).padStart(2, '0') + '-' + String(d).padStart(2, '0');
                dias.push({ dia: d, fecha, estado: fecha < this.hoy ? 'pasado' : (this.estados[fecha] || 'no_disponible') });
            }
            return dias;
        },
        seleccionar(d) {
            if (!d || d.estado !== 'disponible') return;
            this.fechaSeleccionada = d.fecha;
            $dispatch('fecha-seleccionada', { fecha: d.fecha });
        },
        claseDia(d) {
            if (!d) return '';
            if (d.fecha === this.fechaSeleccionada) return 'bg-[#fc5648] text-white border-[#fc5648]';
            if (d.estado === 'disponible') return 'bg-white text-gray-700 border-gray-200 hover:border-[#fc5648] cursor-pointer';
            if (d.estado === 'agotado') return 'bg-gray-50 text-gray-300 border-gray-100 line-through cursor-not-allowed';
            return 'bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed';
        },
    }"
    {{ $attributes->merge(['class' => 'border border-gray-200 rounded-xl p-3 max-w-72']) }}
>
    <div class="flex items-center justify-between mb-2">
        <button type="button" @click="cambiarMes(-1)" :disabled="esMesActual()"
                :class="esMesActual() ? 'opacity-30 cursor-not-allowed' : 'hover:text-[#fc5648]'"
                class="text-gray-400 font-bold w-7 h-7 flex items-center justify-center">‹</button>
        <span class="text-xs font-bold text-gray-700" x-text="nombresMeses[mes - 1] + ' ' + anio"></span>
        <button type="button" @click="cambiarMes(1)" class="text-gray-400 font-bold w-7 h-7 flex items-center justify-center hover:text-[#fc5648]">›</button>
    </div>

    <div class="grid grid-cols-7 gap-0.5 text-center text-[10px] font-bold text-gray-400 mb-1">
        <template x-for="n in nombresDias" :key="n"><span x-text="n"></span></template>
    </div>

    <div class="grid grid-cols-7 gap-0.5">
        <template x-for="(d, idx) in diasGrilla()" :key="idx">
            <button type="button" @click="seleccionar(d)" :disabled="!d || d.estado !== 'disponible'"
                    :class="claseDia(d)"
                    class="aspect-square rounded-md border text-[11px] font-semibold flex items-center justify-center">
                <span x-text="d ? d.dia : ''"></span>
            </button>
        </template>
    </div>

    <template x-if="cargando">
        <p class="text-[10px] text-gray-400 mt-2">Cargando disponibilidad…</p>
    </template>

    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2 pt-2 border-t border-gray-100 text-[10px] text-gray-500">
        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-white border border-gray-300 inline-block"></span> Disponible</span>
        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-gray-200 inline-block"></span> Agotado</span>
        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-gray-100 inline-block"></span> Sin horario</span>
    </div>

    <input type="hidden" name="{{ $name }}" :value="fechaSeleccionada">
</div>
