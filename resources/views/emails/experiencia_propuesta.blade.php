<x-mail::message>
# Nueva experiencia propuesta 🎯

Alguien ha enviado una experiencia para revisión en Pindoor.

<x-mail::table>
| Campo | Valor |
|:------|:------|
| Título | {{ $experiencia->titulo }} |
| Proveedor | {{ $experiencia->proveedor ?? '—' }} |
| Categoría | {{ \App\Models\Experiencia::CATEGORIAS[$experiencia->categoria]['label'] ?? $experiencia->categoria }} |
@if($experiencia->ubicacion)
| Ubicación | {{ $experiencia->ubicacion }} |
@endif
@if($experiencia->dias_semana_label)
| Días | {{ $experiencia->dias_semana_label }} |
@endif
@if($experiencia->hora)
| Hora | {{ $experiencia->hora }} |
@endif
@if($experiencia->duracion)
| Duración | {{ $experiencia->duracion }} |
@endif
@if($experiencia->periodo_label)
| Período | {{ $experiencia->periodo_label }} |
@endif
| Precio | {{ $experiencia->precio_formateado ?? '—' }} |
@if($experiencia->whatsapp)
| WhatsApp | {{ $experiencia->whatsapp }} |
@endif
@if($experiencia->email_contacto)
| Email contacto | {{ $experiencia->email_contacto }} |
@endif
</x-mail::table>

@if($experiencia->descripcion)
**Descripción:**

{{ $experiencia->descripcion }}
@endif

<x-mail::button :url="config('app.url') . '/admin/experiencias'">
Revisar en el admin
</x-mail::button>

— Pindoor.cl
</x-mail::message>
