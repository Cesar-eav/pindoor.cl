@extends('layouts.pindoor')

@section('title', 'Política de Privacidad — Pindoor Valparaíso')
@section('canonical', route('legal.privacidad'))
@section('description', 'Cómo Pindoor recopila, usa y protege tus datos personales.')
@section('robots', 'index, follow')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8 pb-28">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('puntos.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-full bg-white border border-gray-200 shadow-sm text-gray-500 hover:text-gray-800 transition shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-extrabold text-gray-900 leading-tight">Política de Privacidad</h1>
            <p class="text-xs text-gray-400 mt-0.5">Última actualización: {{ now()->translatedFormat('d \d\e F \d\e Y') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6 text-sm text-gray-700 leading-relaxed">

        <p>
            Pindoor ("nosotros", "la app") es una guía turística digital de Valparaíso, Chile, disponible en
            <a href="https://pindoor.cl" class="text-[#fc5648] font-semibold hover:underline">pindoor.cl</a> y como aplicación móvil.
            Esta política explica qué información recopilamos, para qué la usamos y qué opciones tienes al respecto,
            en cumplimiento de la Ley N.º 19.628 sobre Protección de la Vida Privada de Chile.
        </p>

        <div>
            <h2 class="font-extrabold text-gray-900 mb-2">1. Qué datos recopilamos</h2>
            <ul class="list-disc pl-5 space-y-2">
                <li><strong>Datos de registro:</strong> si creas una cuenta (como negocio, artista o usando tu cuenta de Google), guardamos tu nombre y correo electrónico.</li>
                <li><strong>Formularios de contacto:</strong> si nos escribes o postulas tu negocio, recopilamos el nombre, correo, teléfono y mensaje que nos entregas voluntariamente.</li>
                <li><strong>Ubicación aproximada:</strong> si usas la función "cerca de ti", tu navegador comparte tu ubicación con nuestro servidor solo para ordenar los resultados por distancia en el momento — no la almacenamos ni la asociamos a tu cuenta.</li>
                <li><strong>Fotos:</strong> si administras un negocio en Pindoor, las imágenes que subas (logo, galería, carta) se guardan para mostrarlas públicamente en tu ficha.</li>
                <li><strong>Datos de uso y analítica:</strong> usamos Microsoft Clarity y Google Tag Manager para entender cómo se usa el sitio (páginas visitadas, interacciones). Puedes optar por no participar — ver sección 4.</li>
            </ul>
        </div>

        <div>
            <h2 class="font-extrabold text-gray-900 mb-2">2. Para qué usamos tus datos</h2>
            <ul class="list-disc pl-5 space-y-2">
                <li>Operar y mostrar tu ficha de negocio, perfil o publicación.</li>
                <li>Responder consultas y contactarte por formularios de contacto o postulación.</li>
                <li>Ordenar los resultados de búsqueda según cercanía a tu ubicación.</li>
                <li>Entender el uso general del sitio para mejorarlo (analítica agregada, no identificamos personas individualmente para este fin).</li>
            </ul>
            <p class="mt-2">No vendemos tus datos personales a terceros. No mostramos publicidad de terceros ni usamos tus datos con fines publicitarios.</p>
        </div>

        <div>
            <h2 class="font-extrabold text-gray-900 mb-2">3. Con quién se comparten</h2>
            <p>
                Solo compartimos datos de uso agregados con nuestros proveedores de analítica (Microsoft Clarity, Google Tag Manager),
                sujetos a sus propias políticas de privacidad. No compartimos tu nombre, correo o teléfono con terceros para fines comerciales.
            </p>
        </div>

        <div>
            <h2 class="font-extrabold text-gray-900 mb-2">4. Cookies y analítica</h2>
            <p>
                Usamos cookies propias (sesión, preferencias de idioma) y de analítica de terceros. Si prefieres no participar en la
                analítica, puedes indicarlo y dejaremos de cargar esos scripts en tu navegador.
            </p>
        </div>

        <div>
            <h2 class="font-extrabold text-gray-900 mb-2">5. Tus derechos</h2>
            <p>
                Puedes solicitar acceso, corrección o eliminación de tus datos personales, o el cierre de tu cuenta, escribiendo a
                <a href="mailto:soporte@pindoor.cl" class="text-[#fc5648] font-semibold hover:underline">soporte@pindoor.cl</a>.
                Responderemos tu solicitud dentro de un plazo razonable.
            </p>
        </div>

        <div>
            <h2 class="font-extrabold text-gray-900 mb-2">6. Menores de edad</h2>
            <p>Pindoor no está dirigida a niños ni recopila deliberadamente datos de menores de edad.</p>
        </div>

        <div>
            <h2 class="font-extrabold text-gray-900 mb-2">7. Cambios a esta política</h2>
            <p>Podemos actualizar esta política ocasionalmente. Los cambios se publicarán en esta misma página con la fecha de actualización.</p>
        </div>

        <div>
            <h2 class="font-extrabold text-gray-900 mb-2">8. Contacto</h2>
            <p>
                Ante cualquier duda sobre esta política o el tratamiento de tus datos, escríbenos a
                <a href="mailto:soporte@pindoor.cl" class="text-[#fc5648] font-semibold hover:underline">soporte@pindoor.cl</a>.
            </p>
        </div>

    </div>
</div>
@endsection
