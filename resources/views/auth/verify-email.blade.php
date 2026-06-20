<x-guest-layout>

    @if(auth()->user()?->type === 'artista')
    <x-slot name="panelLeft">
        <div class="hidden lg:flex lg:w-1/2 flex-col justify-between p-12 relative overflow-hidden"
             style="background: linear-gradient(155deg, #7c3aed 0%, #6d28d9 45%, #5b21b6 100%)">

            <div class="absolute -top-24 -left-24 w-72 h-72 bg-white/5 rounded-full"></div>
            <div class="absolute bottom-0 left-1/4 w-48 h-48 bg-black/5 rounded-full"></div>

            <div class="relative z-10">
                <a href="/" class="inline-flex items-center">
                    <span class="text-white font-bold text-3xl tracking-tight">Pin</span><span class="text-white/65 font-bold text-3xl tracking-tight">door</span>
                </a>
            </div>

            <div class="relative z-10">
                <h1 class="text-white text-4xl font-bold leading-tight mb-4">
                    Casi listo,<br>solo falta un paso
                </h1>
                <p class="text-white/80 text-base leading-relaxed">
                    Confirma tu email para activar<br>tu perfil de artista o agrupación en Pindoor.
                </p>
            </div>

            <p class="relative z-10 text-white/35 text-sm">© {{ date('Y') }} Pindoor · Valparaíso, Chile</p>
        </div>
    </x-slot>
    @endif

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
            @if(auth()->user()?->type === 'artista')
                <button type="submit"
                        class="w-full justify-center inline-flex items-center px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white font-bold text-sm rounded-xl transition">
                    Reenviar email de verificación
                </button>
            @else
                <x-primary-button class="w-full justify-center">
                    Reenviar email de verificación
                </x-primary-button>
            @endif
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
