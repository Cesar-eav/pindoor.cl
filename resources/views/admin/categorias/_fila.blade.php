<tr class="hover:bg-gray-50/70 transition group">
    <td class="px-5 py-4 text-xs text-gray-300 font-mono">{{ $cat->id }}</td>

    <td class="px-5 py-4">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-[#fff0ef] flex items-center justify-center shrink-0">
                @if($cat->icono)
                    <i class="fa-solid fa-{{ $cat->icono }} text-[#fc5648] text-sm"></i>
                @else
                    <i class="fa-solid fa-tag text-gray-300 text-sm"></i>
                @endif
            </div>
            <div>
                <p class="font-bold text-gray-900">{{ $cat->nombre }}</p>
                <p class="text-[11px] text-gray-400 font-mono">{{ $cat->slug }}</p>
            </div>
        </div>
    </td>

    <td class="px-5 py-4">
        @if($cat->tipo)
            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-[11px] font-semibold">{{ $cat->tipo }}</span>
        @else
            <span class="text-gray-300 text-xs">—</span>
        @endif
    </td>

    <td class="px-5 py-4">
        @php $modulos = $cat->modulos_defecto ?? []; @endphp
        @if(count($modulos))
            <div class="flex flex-wrap gap-1">
                @foreach($modulos as $mod)
                    @if(isset($catalogo[$mod]))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 text-[11px] font-semibold border border-emerald-100">
                            {{ $catalogo[$mod]['emoji'] }} {{ $catalogo[$mod]['label'] }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-red-50 text-red-400 text-[11px] font-mono border border-red-100" title="Clave inválida">⚠ {{ $mod }}</span>
                    @endif
                @endforeach
            </div>
        @else
            <span class="text-xs text-gray-300 italic">Sin módulos por defecto</span>
        @endif
    </td>

    <td class="px-5 py-4 text-center">
        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold
            {{ ($cat->puntos_interes_count ?? 0) > 0 ? 'bg-gray-100 text-gray-700' : 'text-gray-300' }}">
            {{ $cat->puntos_interes_count ?? 0 }}
        </span>
    </td>

    <td class="px-5 py-4">
        <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition">
            <a href="{{ route('admin.categorias.edit', $cat) }}"
               class="flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-bold hover:bg-blue-100 transition">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Editar
            </a>
            <form action="{{ route('admin.categorias.destroy', $cat) }}" method="POST"
                  onsubmit="return confirm('¿Eliminar «{{ $cat->nombre }}»?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-50 text-red-500 text-xs font-bold hover:bg-red-100 transition">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Borrar
                </button>
            </form>
        </div>
    </td>
</tr>
