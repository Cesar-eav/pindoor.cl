@if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <div class="flex flex-col lg:flex-row gap-6">

        <div class="flex lg:flex-col items-center gap-4 lg:gap-3 lg:w-40 shrink-0">
            @if($operador?->imagen_perfil)
                <img src="{{ asset('storage/' . $operador->imagen_perfil) }}" alt=""
                     class="w-20 h-20 lg:w-36 lg:h-36 rounded-full object-cover border-4 border-teal-100 shadow-sm shrink-0">
            @else
                <div class="w-20 h-20 lg:w-36 lg:h-36 rounded-full bg-teal-100 flex items-center justify-center text-4xl lg:text-5xl border-4 border-teal-50 shrink-0">🧭</div>
            @endif
            <div class="flex-1 lg:w-full">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Foto de perfil / logo</label>
                <input type="file" name="imagen" accept="image/*"
                       class="block w-full text-xs text-gray-500
                              file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0
                              file:text-xs file:font-bold file:bg-teal-50 file:text-teal-700
                              hover:file:bg-teal-100 cursor-pointer">
                <p class="text-[10px] text-gray-400 mt-1">JPG, PNG — máx. 4 MB</p>
            </div>
        </div>

        <div class="flex-1 grid sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre del operador *</label>
                <input type="text" name="nombre" value="{{ old('nombre', $operador?->nombre) }}" required
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Ciudad</label>
                <input type="text" name="ciudad" value="{{ old('ciudad', $operador?->ciudad) }}" placeholder="Valparaíso…"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email de contacto</label>
                <input type="email" name="email_contacto" value="{{ old('email_contacto', $operador?->email_contacto) }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono', $operador?->telefono) }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Sitio web</label>
                <input type="url" name="enlace_web" value="{{ old('enlace_web', $operador?->enlace_web) }}" placeholder="https://…"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Instagram</label>
                <input type="url" name="enlace_instagram" value="{{ old('enlace_instagram', $operador?->enlace_instagram) }}" placeholder="https://…"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">WhatsApp</label>
                <input type="url" name="enlace_whatsapp" value="{{ old('enlace_whatsapp', $operador?->enlace_whatsapp) }}" placeholder="https://wa.me/…"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Descripción</label>
                <textarea name="descripcion" rows="3"
                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 outline-none">{{ old('descripcion', $operador?->descripcion) }}</textarea>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="activo" id="activo" value="1"
                       @checked(old('activo', $operador?->activo ?? true))
                       class="rounded text-teal-600 focus:ring-teal-500">
                <label for="activo" class="text-sm font-semibold text-gray-700">Activo</label>
            </div>
        </div>
    </div>
</div>

<div class="mt-6 flex justify-end">
    <button type="submit"
            class="bg-teal-600 hover:bg-teal-700 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition">
        Guardar
    </button>
</div>
