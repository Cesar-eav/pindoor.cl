<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Pindoor · La Brújula de Valparaíso')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('head')
    @if(app()->environment('production'))
    <script>(function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);})(window,document,"clarity","script","wajfuymjy1");</script>
    @endif
</head>
<body class="@yield('bodyClass', 'bg-gray-100 text-gray-900 font-serif')">

<div class="flex min-h-screen">
    {{-- Sidebar lateral: solo desktop --}}
    <x-nav_lateral />

    {{-- Contenido principal --}}
    <div class="flex-1 min-w-0 md:ml-56">
        @yield('content')
    </div>
</div>

@yield('scripts')
</body>
</html>
