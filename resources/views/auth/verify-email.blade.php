<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="text-4xl mb-3">📬</div>
        <h2 class="text-lg font-bold text-gray-900">Verifica tu email</h2>
    </div>

    <p class="text-sm text-gray-600 mb-4 text-center">
        Te enviamos un enlace de verificación. Haz clic en él para activar tu cuenta.<br>
        Revisa también tu carpeta de spam.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 text-sm font-semibold text-green-600 text-center bg-green-50 rounded-lg py-3">
            ✓ Email reenviado correctamente.
        </div>
    @endif

    <div class="mt-4 flex flex-col gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button class="w-full justify-center">
                Reenviar email de verificación
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full text-sm text-gray-500 hover:text-gray-700 underline text-center">
                Cerrar sesión
            </button>
        </form>
    </div>
</x-guest-layout>
