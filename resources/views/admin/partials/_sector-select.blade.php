@php
$sectores = [
    'Plan'                   => 'Plan',
    'Cerro Alegre'           => 'Cerro Alegre',
    'Cerro Concepción'       => 'Cerro Concepción',
    'Cerro San Juan de Dios' => 'Cerro San Juan de Dios',
    'Cerro Bellavista'       => 'Cerro Bellavista',
    'Cerro Monjas'           => 'Cerro Monjas',
    'Cerro Mariposas'        => 'Cerro Mariposas',
    'Cerro Florida'          => 'Cerro Florida',
    'Cerro Esperanza'        => 'Cerro Esperanza',
    'Cerro Placeres'         => 'Cerro Placeres',
    'Cerro Barón'            => 'Cerro Barón',
    'Cerro Lecheros'         => 'Cerro Lecheros',
    'Cerro Larraín'          => 'Cerro Larraín',
    'Cerro Polanco'          => 'Cerro Polanco',
    'Cerro Molino'           => 'Cerro Molino',
    'Cerro Rodríguez'        => 'Cerro Rodríguez',
    'Cerro Rocuant'          => 'Cerro Rocuant',
    'Cerro Delicias'         => 'Cerro Delicias',
    "Cerro O'Higgins"        => "Cerro O'Higgins",
    'Cerro San Roque'        => 'Cerro San Roque',
    'Cerro Ramaditas'        => 'Cerro Ramaditas',
    'Cerro Merced'           => 'Cerro Merced',
    'Cerro Las Cañas'        => 'Cerro Las Cañas',
    'Cerro El Litre'         => 'Cerro El Litre',
    'Cerro La Cruz'          => 'Cerro La Cruz',
    'Cerro Jiménez'          => 'Cerro Jiménez',
    'Cerro La Loma'          => 'Cerro La Loma',
    'Cerro Yungay'           => 'Cerro Yungay',
    'Cerro Panteón'          => 'Cerro Panteón',
    'Cerro Cárcel'           => 'Cerro Cárcel',
    'Cerro Mesilla'          => 'Cerro Mesilla',
    'Cerro Toro'             => 'Cerro Toro',
    'Cerro Santo Domingo'    => 'Cerro Santo Domingo',
    'Cerro Arrayán'          => 'Cerro Arrayán',
    'Cerro Perdices'         => 'Cerro Perdices',
    'Cerro Artillería'       => 'Cerro Artillería',
    'Playa Ancha'            => 'Playa Ancha',
    'Placilla'               => 'Placilla',
    'Laguna Verde'           => 'Laguna Verde',
];
@endphp

<select name="sector" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-pindoor-accent">
    @foreach($sectores as $val => $label)
        <option value="{{ $val }}" @isset($selected) @selected($selected == $val) @endisset>
            {{ $label }}
        </option>
    @endforeach
</select>
