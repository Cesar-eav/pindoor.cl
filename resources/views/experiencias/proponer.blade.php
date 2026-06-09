@extends('layouts.pindoor')

@section('title', 'Proponer una Experiencia — Pindoor.cl')
@section('canonical', route('experiencias.proponer'))
@section('description', 'Ofreces clases, talleres u otras actividades recurrentes en Valparaíso? Comparte tu experiencia con turistas y locales a través de Pindoor.')
@section('bodyClass', 'bg-gray-50 text-gray-900')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">

    {{-- Back --}}
    <a href="{{ route('experiencias.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 mb-6 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Volver a Experiencias
    </a>

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-950 mb-2">
            Proponer una <span class="text-[#fc5648]">experiencia</span>
        </h1>
        <p class="text-gray-500 text-sm leading-relaxed">
            ¿Ofreces clases de cueca, talleres de cerámica, tours guiados o cualquier actividad que se repite en el tiempo?
            Cuéntanos y nosotros la revisamos antes de publicarla.
        </p>
    </div>

    {{-- Alerta éxito --}}
    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-2xl px-5 py-4 flex items-start gap-3">
        <svg class="w-5 h-5 shrink-0 mt-0.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Errores --}}
    @if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-5 py-4">
        <p class="text-sm font-bold mb-1">Por favor corrige los siguientes errores:</p>
        <ul class="text-sm list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('experiencias.proponer.store') }}" method="POST" enctype="multipart/form-data"
          class="space-y-6 bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8">
        @csrf

        {{-- Título --}}
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">
                Nombre de la experiencia <span class="text-[#fc5648]">*</span>
            </label>
            <input type="text" name="titulo" value="{{ old('titulo') }}" required maxlength="255"
                   placeholder="Ej: Clases de cueca para principiantes"
                   class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648]/30 focus:border-[#fc5648] @error('titulo') border-red-400 @enderror">
        </div>

        {{-- Quién lo imparte --}}
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">
                ¿Quién lo imparte / ofrece? <span class="text-[#fc5648]">*</span>
            </label>
            <input type="text" name="proveedor" value="{{ old('proveedor') }}" required maxlength="200"
                   placeholder="Tu nombre o el nombre de tu organización"
                   class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648]/30 focus:border-[#fc5648] @error('proveedor') border-red-400 @enderror">
        </div>

        {{-- Descripción --}}
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">
                Descripción <span class="text-[#fc5648]">*</span>
            </label>
            <textarea name="descripcion" rows="4" required maxlength="2000"
                      placeholder="Describe la actividad: qué se hace, qué aprende el participante, qué incluye, etc."
                      class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648]/30 focus:border-[#fc5648] resize-none @error('descripcion') border-red-400 @enderror">{{ old('descripcion') }}</textarea>
        </div>

        {{-- Categoría --}}
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">
                Categoría <span class="text-[#fc5648]">*</span>
            </label>
            <div class="flex flex-wrap gap-2">
                @foreach($categorias as $slug => $cat)
                <label class="cursor-pointer">
                    <input type="radio" name="categoria" value="{{ $slug }}"
                           {{ old('categoria') === $slug ? 'checked' : '' }}
                           class="sr-only peer" required>
                    <span class="px-3 py-1.5 rounded-full text-xs font-bold border transition-colors
                                 peer-checked:bg-gray-900 peer-checked:text-white peer-checked:border-gray-900
                                 bg-white text-gray-500 border-gray-300 hover:border-gray-500">
                        {{ $cat['emoji'] }} {{ $cat['label'] }}
                    </span>
                </label>
                @endforeach
            </div>
            @error('categoria')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Días de la semana --}}
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">¿Qué días se realiza?</label>
            <div class="flex flex-wrap gap-2">
                @foreach($dias as $num => $nombre)
                <label class="cursor-pointer">
                    <input type="checkbox" name="dias_semana[]" value="{{ $num }}"
                           {{ in_array($num, old('dias_semana', [])) ? 'checked' : '' }}
                           class="sr-only peer">
                    <span class="px-3 py-1.5 rounded-full text-xs font-bold border transition-colors
                                 peer-checked:bg-[#fc5648] peer-checked:text-white peer-checked:border-[#fc5648]
                                 bg-white text-gray-500 border-gray-300 hover:border-gray-400">
                        {{ $nombre }}
                    </span>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Hora + Duración --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Hora</label>
                <input type="text" name="hora" value="{{ old('hora') }}" maxlength="100"
                       placeholder="Ej: 10:00 AM"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648]/30 focus:border-[#fc5648]">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Duración aproximada</label>
                <input type="text" name="duracion" value="{{ old('duracion') }}" maxlength="50"
                       placeholder="Ej: 1 hora"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648]/30 focus:border-[#fc5648]">
            </div>
        </div>

        {{-- Ubicación --}}
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Lugar o dirección</label>
            <input type="text" name="ubicacion" value="{{ old('ubicacion') }}" maxlength="255"
                   placeholder="Ej: Cerro Alegre, Valparaíso"
                   class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648]/30 focus:border-[#fc5648]">
        </div>

        {{-- Precio --}}
        <div x-data="{ gratis: {{ old('es_gratuito') ? 'true' : 'false' }} }">
            <label class="block text-sm font-bold text-gray-700 mb-2">Precio</label>
            <label class="flex items-center gap-2 cursor-pointer mb-3">
                <input type="checkbox" name="es_gratuito" value="1"
                       x-model="gratis"
                       {{ old('es_gratuito') ? 'checked' : '' }}
                       class="w-4 h-4 accent-green-500">
                <span class="text-sm text-gray-700 font-medium">Es gratuito</span>
            </label>
            <div x-show="!gratis">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-gray-500">$</span>
                    <input type="number" name="precio" value="{{ old('precio') }}" min="0"
                           placeholder="0"
                           class="w-40 border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648]/30 focus:border-[#fc5648]">
                    <span class="text-xs text-gray-400">CLP por sesión</span>
                </div>
            </div>
        </div>

        {{-- Nivel + Capacidad --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Nivel</label>
                <select name="nivel"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#fc5648]/30 focus:border-[#fc5648]">
                    @foreach($niveles as $slug => $label)
                    <option value="{{ $slug }}" {{ old('nivel', 'todos') === $slug ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Capacidad máxima</label>
                <input type="number" name="capacidad" value="{{ old('capacidad') }}" min="1"
                       placeholder="Ej: 10 personas"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648]/30 focus:border-[#fc5648]">
            </div>
        </div>

        {{-- Contacto --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">WhatsApp de contacto</label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 border border-r-0 border-gray-300 rounded-l-xl bg-gray-50 text-gray-500 text-sm">+</span>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" maxlength="30"
                           placeholder="56912345678"
                           class="flex-1 border border-gray-300 rounded-r-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648]/30 focus:border-[#fc5648]">
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Correo de contacto</label>
                <input type="email" name="email_contacto" value="{{ old('email_contacto') }}" maxlength="200"
                       placeholder="tuemail@ejemplo.cl"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648]/30 focus:border-[#fc5648]">
            </div>
        </div>

        {{-- Período de vigencia --}}
        <div class="border border-gray-200 rounded-xl p-4 space-y-3">
            <p class="text-sm font-bold text-gray-700">📅 ¿Solo en ciertos meses?</p>
            <p class="text-xs text-gray-400 leading-relaxed">
                Si la actividad se da solo durante un período (ej: todos los martes de junio a agosto), indica el rango de fechas.
                Si es permanente, déjalo en blanco.
            </p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Desde</label>
                    <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio') }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648]/30 focus:border-[#fc5648]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Hasta</label>
                    <input type="date" name="fecha_fin" value="{{ old('fecha_fin') }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648]/30 focus:border-[#fc5648]">
                    @error('fecha_fin')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Enlace --}}
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Enlace (opcional)</label>
            <input type="url" name="enlace" value="{{ old('enlace') }}" maxlength="500"
                   placeholder="https://..."
                   class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648]/30 focus:border-[#fc5648] @error('enlace') border-red-400 @enderror">
            <p class="text-xs text-gray-400 mt-1">Instagram, web, Facebook o cualquier link con más información.</p>
        </div>

        {{-- Imagen --}}
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Foto (opcional)</label>
            <input type="file" name="imagen" accept="image/*"
                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-[#fff0ef] file:text-[#fc5648] hover:file:bg-[#ffe0de] transition">
            <p class="text-xs text-gray-400 mt-1">Máximo 4 MB. Preferible horizontal (16:9).</p>
        </div>

        {{-- Submit --}}
        <div class="pt-2">
            <button type="submit"
                    class="w-full bg-[#fc5648] hover:bg-[#d94439] text-white font-bold py-3 px-6 rounded-xl transition text-sm">
                Enviar mi experiencia
            </button>
            <p class="text-xs text-center text-gray-400 mt-3">
                La revisaremos y te contactaremos antes de publicarla.
            </p>
        </div>

    </form>
</div>
@endsection
