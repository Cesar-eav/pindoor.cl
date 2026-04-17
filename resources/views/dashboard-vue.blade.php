<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - El Pionero de Valparaíso</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- Vue App Container -->
    <div id="app-dashboard"
         data-user-name="{{ auth()->user()->name }}"
         data-csrf-token="{{ csrf_token() }}"
         data-logout-url="{{ route('logout') }}">
    </div>
</body>
</html>
