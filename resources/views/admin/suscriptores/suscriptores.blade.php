    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Suscriptores del Newsletter</h1>

        @if($suscriptores->isEmpty())
            <p class="text-gray-600">No hay suscriptores aún.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300 rounded shadow">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th class="py-2 px-4 border-b">ID</th>
                            <th class="py-2 px-4 border-b">Correo</th>
                            <th class="py-2 px-4 border-b">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suscriptores as $suscriptor)
                            <tr>
                                <td class="py-2 px-4 border-b">{{ $suscriptor->id }}</td>
                                <td class="py-2 px-4 border-b">{{ $suscriptor->email }}</td>
                                <td class="py-2 px-4 border-b">{{ $suscriptor->created_at->format('d-m-Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @endif
    </div>