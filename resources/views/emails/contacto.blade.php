<x-mail::message>
# Nuevo contacto desde Pindoor

**Tipo:** {{ $lead->tipo === 'artista' ? '🎨 Artista' : '🏠 Local / Negocio' }}

<x-mail::table>
| Campo | Valor |
|:------|:------|
| Nombre | {{ $lead->nombre }} |
| Email | {{ $lead->email }} |
@if($lead->telefono)
| Teléfono | {{ $lead->telefono }} |
@endif
@if($lead->ciudad)
| Ciudad | {{ $lead->ciudad }} |
@endif
@if($lead->tipo === 'cliente')
| Tipo de local | {{ $lead->tipo_negocio }} |
| Nombre del local | {{ $lead->nombre_negocio }} |
@else
| Disciplina | {{ $lead->especialidad }} |
@endif
</x-mail::table>

@if($lead->mensaje)
**Mensaje:**

{{ $lead->mensaje }}
@endif

<x-mail::button :url="config('app.url') . '/admin/leads'">
Ver todos los leads
</x-mail::button>

— Pindoor.cl
</x-mail::message>
