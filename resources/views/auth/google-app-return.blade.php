<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Volver a Pindoor</title>
    <script>window.location.replace(@json($url));</script>
</head>
<body style="margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;background:#fff0ef;font-family:system-ui,sans-serif;">
    <div style="text-align:center;padding:24px;">
        <p style="font-weight:800;font-size:20px;color:#111;margin:0 0 16px;">Sesión iniciada</p>
        <a href="{{ $url }}" style="display:inline-block;background:#fc5648;color:#fff;font-weight:800;text-decoration:none;padding:12px 24px;border-radius:16px;">Volver a Pindoor</a>
    </div>
</body>
</html>
