@php
$sectores = [
    'Alemndral'              => 'Almendral',
    'Cerro Alegre'           => 'Cerro Alegre',
    'Cerro Arrayán'          => 'Cerro Arrayán',
    'Cerro Artillería'       => 'Cerro Artillería',
    'Barrio Puerto'          => 'Barrio Puerto',
    'Cerro Barón'            => 'Cerro Barón',
    'Cerro Bellavista'       => 'Cerro Bellavista',
    'Cerro Cárcel'           => 'Cerro Cárcel',
    'Cerro Concepción'       => 'Cerro Concepción',
    'Cerro Cordillera'       => 'Cerro Cordillera',
    'Cerro Delicias'         => 'Cerro Delicias',
    'Cerro El Litre'         => 'Cerro El Litre',
    'Cerro Esperanza'        => 'Cerro Esperanza',
    'Cerro Florida'          => 'Cerro Florida',
    'Cerro Higo'             => 'Cerro Higo',
    'Cerro Jiménez'          => 'Cerro Jiménez',
    'Cerro La Cruz'          => 'Cerro La Cruz',
    'Cerro La Loma'          => 'Cerro La Loma',
    'Cerro Larraín'          => 'Cerro Larraín',
    'Cerro Las Cañas'        => 'Cerro Las Cañas',
    'Cerro Lecheros'         => 'Cerro Lecheros',
    'Cerro Mariposas'        => 'Cerro Mariposas',
    'Cerro Merced'           => 'Cerro Merced',
    'Cerro Mesilla'          => 'Cerro Mesilla',
    'Cerro Molino'           => 'Cerro Molino',
    'Cerro Monjas'           => 'Cerro Monjas',
    "Cerro O'Higgins"        => "Cerro O'Higgins",
    'Cerro Panteón'          => 'Cerro Panteón',
    'Cerro Perdices'         => 'Cerro Perdices',
    'Cerro Placeres'         => 'Cerro Placeres',
    'Cerro Polanco'          => 'Cerro Polanco',
    'Cerro Ramaditas'        => 'Cerro Ramaditas',
    'Cerro Rocuant'          => 'Cerro Rocuant',
    'Cerro Rodríguez'        => 'Cerro Rodríguez',
    'Cerro San Juan de Dios' => 'Cerro San Juan de Dios',
    'Cerro San Roque'        => 'Cerro San Roque',
    'Cerro Santo Domingo'    => 'Cerro Santo Domingo',
    'Cerro Toro'             => 'Cerro Toro',
    'Cerro Yungay'           => 'Cerro Yungay',
    'Laguna Verde'           => 'Laguna Verde',
    'Placilla'               => 'Placilla',
    'Plan'                   => 'Plan',
    'Playa Ancha'            => 'Playa Ancha',
];
@endphp

<select name="sector" class="block mt-1 w-full bg-white border border-slate-200 text-gray-900 rounded-lg focus:border-pindoor-accent focus:ring-pindoor-accent transition">
    @foreach($sectores as $val => $label)
        <option value="{{ $val }}" @isset($selected) @selected($selected == $val) @endisset>
            {{ $label }}
        </option>
    @endforeach
</select>
